<?php

//Reference: https://github.com/ghedipunk/PHP-Websockets
error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
// ob_implicit_flush();

class l2l_server {

	private $ip;
	private $port;
	private $master_socket;
	private $all_sockets;
	private $max_buffer_size;
	private $backlog;
	private $runServer = true;

	//TODO: Move this shit to a database, if possible
	private $client_sockets;
	
	function __construct($ip, $port, $max_buffer_size = 2048, $backlog = 20) {

		$this->max_buffer_size = $max_buffer_size;
		$this->ip = $ip;
		$this->port = $port;
		$this->client_sockets = array();
		$this->all_sockets = array();

		$this->createSocket();
		$this->setSocketOptions();
		$this->bindAndListen();

		if($this->runServer == true) {
			$this->socketInfo("Server started\nListening on: $ip:$port\nMaster socket: ". $this->master_socket);
		}
	}

	private function createSocket() {
		$this->master_socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if (!is_resource($this->master_socket)) {
			$this->socketError("Unable to create socket.", true);
		}
		
		// $this->setSocketToNonBlockingMode($this->master_socket, true);
		array_push($this->all_sockets, $this->master_socket);
	}

	private function setSocketToNonBlockingMode($socket, $showStoppingError = false) {
		if (!socket_set_nonblock($this->master_socket)) {
			$this->socketError("Unable to set non blocking mode for socket.", $showStoppingError);
		}
	}

	private function setSocketOptions() {

		//Reference: http://php.net/manual/en/function.socket-get-option.php
		//We will record debugging information
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_DEBUG, 0)) {
			$this->socketError("Unable to set debug option on socket", true);
		}

		//We will Support transmission of broadcast messages
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_BROADCAST, 0)) {
			$this->socketError("Unable to set option to support transmission of broadcast messages on socket", true);
		}

		//Local addresses will be reused
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
			$this->socketError("Unable to set option to reuse local addresses on socket", true);
		}

		//connections are to be kept active with periodic transmission of messages
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_KEEPALIVE, 1)) {
			$this->socketError("Unable to set option to keep connection alive on socket", true);
		}

		//We will report the size of the send buffer
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_SNDBUF, 1)) {
			$this->socketError("Unable to set option to report the size of the send buffer on socket", true);
		}

		//We will report the size of the receive buffer
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_RCVBUF, 1)) {
			$this->socketError("Unable to set option to report the size of the receive buffer on socket", true);
		}

		//Attempt to send any unsent data at the time socket_close() is called
		$linger = array('l_linger' => 1, 'l_onoff' => 1);
		if (!socket_set_option($this->master_socket, SOL_SOCKET, SO_LINGER, $linger)) {
			$this->socketError("Unable to set option that would have attempted to send unsent data before closing socket, on socket", true);
		}

		//TODO: support multi-casting
		//Reference: http://www.metaswitch.com/resources/what-is-multicast-ip-routing
	}

	private function bindAndListen() {
		if (!socket_bind($this->master_socket, $this->ip, $this->port)) {
			$this->socketError("Unable to bind to IP address [' . $this->ip . '] port[' . $this->port . ']'", true);
		}

		if (!socket_listen($this->master_socket,$this->backlog)) {
			$this->socketError("Unable to listen to IP address [' . $this->ip . '] port[' . $this->port . ']'", true);
		}
	}

	private function getSocketName($socket, &$socket_address, &$socket_port) {
		if (!socket_getsockname($socket, $socket_address, $socket_port)) {
			$this->socketError("Unable to get socket name", false);
		}
		else {
			print 'Details From Socket: IP address [' . $this->ip . '] port[' . $this->port . ']';
		}
	}

	private function socketError($message, $showStoppingError = false) {
		print $message . socket_strerror(socket_last_error()) . PHP_EOL;
		$this->runServer = !$showStoppingError;
		exit;
	}

	private function socketInfo($message) {
		print $message . PHP_EOL;
	}

	public function run() {

		while($this->runServer)
		{
			$read = $this->all_sockets;
        	$write  = NULL;
			$except = NULL;

	   		$num_changed_sockets = socket_select($read, $write, $except, 0);

			if ($num_changed_sockets === false) {
			    /* Error handling */
			} 
			else if ($num_changed_sockets > 0) {
				/* At least at one of the sockets something interesting happened */
			    if (in_array($this->master_socket, $read)) {
			    	$this->acceptNewClientIfNeeded();
		        } 
		        else {
			        $this->readFromSocketClients($read);
		        }
			}  
		}
	}

	private function acceptNewClientIfNeeded() {

	   	$this->socketInfo("Attempting to accept new client...");

		//Handle new connections
		if(($client_socket = socket_accept($this->master_socket)) !== false)
		{
			$this->connect($client_socket);
			$this->broadcast("Welcome $client_socket");
		}
	}

	private function readFromSocketClients($read) {
			
	   	$buffer = null;
	   	$bytes = 0;

	   	 // Handle Input From 
	    foreach ($this->client_sockets as $key => $client) { // for each client        
			if (in_array($client, $read)) {
		    	if (false !== ($bytes = socket_recv($client, $buffer, $this->max_buffer_size, MSG_DONTWAIT))) {
				    $this->socketInfo("Read $bytes bytes from socket_recv()");
		            $buffer = trim($buffer);

		            if(!empty($buffer)) {
		            	$lowerCase = strtolower($buffer);

		            	if(strcmp($lowerCase, "quit") == 0
		            		|| strcmp($lowerCase, "shutdown") == 0) {
							socket_close($client);
							$this->disconnect($client);
		            	}
		            }

		            $message = "$client: $buffer";
		            $this->broadcast($message);			    	
				} else {
					// $this->socketError("Failed to read from client socket[$client]", false);
				}
	        }
	    } 
	}

	private function broadcast($message, $recipient = null) {
		$length = strlen($message);
		if($length > 0) {

			$this->socketInfo("Broadcasting Message=[$message] size=[$length]");
	
			foreach($this->client_sockets as $key => $next_client_socket) {

				$st = $message;
				$length = strlen($message);

				if($next_client_socket != $recipient) {
					do {
						$sent = socket_write($next_client_socket, $st, strlen($st));
					
						if ($sent === false) {
							// continue;
							// $this->socketInfo("Error sending message=[$message] client=[$next_client_socket]");
							$this->disconnect($next_client_socket);
							break;
						}

						// Check if the entire message has been sented
						if ($sent < $length) {
							// If not sent the entire message.
							// Get the part of the message that has not yet been sented as message
							$st = substr($st, $sent);

							// Get the length of the not sented part
							$length -= $sent;

						} 
						else {
							break;
						}
					} while($length > 0);
				}
			}

			$length = strlen($message);
			$this->socketInfo("Message Broadcasted=[$message] size=[$length]");
		}
	}

	private function connect($client_socket) {
		$message = "Client $client_socket has connected";
		$this->broadcast($message, $client_socket);
		// $this->setSocketToNonBlockingMode($client_socket);
		array_push($this->client_sockets, $client_socket);
		array_push($this->all_sockets, $client_socket);
	}

	private function disconnect($client_socket) {
		$message = "Client $client_socket has been disconnected";
		
		if(($key = array_search($client_socket, $this->client_sockets)) !== false) {
		    unset($this->client_sockets[$key]);
		}
		if(($key = array_search($client_socket, $this->all_sockets)) !== false) {
		    unset($this->all_sockets[$key]);
		}

		$this->broadcast($message);
	}

	function __destruct()
	{
		while(sizeof($this->client_sockets) > 0) {
			$client_socket = array_pop($this->client_sockets);
			unset($client_socket);
		}

		socket_close($this->master_socket);

		unset($this->ip);
		unset($this->port);
		unset($this->master_socket);
		unset($this->max_buffer_size);
		unset($this->backlog);
		unset($this->runServer);
	}
}

$ip = "192.168.29.225";
$port = 9009;
$max_buffer_size = 4096;
$backlog = 20;
$l2l_server = new l2l_server($ip, $port, $max_buffer_size, $backlog);
$l2l_server->run();

?>
