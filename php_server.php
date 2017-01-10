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
	private $max_buffer_size;
	private $backlog;
	private $runServer = true;

	//TODO: Move this shit to a database, if possible
	private $client_sockets = array();
	
	function __construct($ip, $port, $max_buffer_size = 2048, $backlog = 20) {

		$this->max_buffer_size = $max_buffer_size;
		$this->ip = $ip;
		$this->port = $port;

		$this->createSocket();
		$this->setSocketOptions();
		$this->bindAndListen();

		if(empty($this->runServer)) {
			$this->socketInfo("Server started\nListening on: $ip:$port\nMaster socket: ". $this->master_socket);
		}
	}

	private function createSocket() {
		$this->master_socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if (!is_resource($this->master_socket)) {
			$this->socketError("Unable to create socket.", true);
		}
		
		$this->setSocketToNonBlockingMode($this->master_socket, true);
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
			$buffer = null;

			//Handle new connections
		    if(($client_socket = socket_accept($this->master_socket)) !== false)
		    {
		    	$this->connect($client_socket);

		        //TODO: Welcome new socket
		        $message = "Welcome $client_socket";

		        socket_write($client_socket, $msg, strlen($msg));
				$this->socketInfo("Message sent to newcomer: " . $message);
		    }

		    // Handle Input From 
		    foreach ($this->client_sockets as $key => $client) { // for each client        
		        if (false === ($buffer = socket_read($client, $this->max_buffer_size, PHP_NORMAL_READ))) {
		        	$this->socketError("Failed to read from client socket[$client]");
	            }

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
		    }   
		}
	}

	private function broadcast($message) {
		foreach($this->client_sockets as $key => $next_client_socket) {
			if(!socket_write($next_client_socket, $message)) {
				$this->disconnect($next_client_socket);
			}
			$this->socketInfo("Message Broadcasted: " . $message);
		}
	}

	private function connect($client_socket) {
		$message = "Client $client_socket has connected\n";
		$this->broadcast($message);
		$this->setSocketToNonBlockingMode($client_socket);
		array_push($this->$client_sockets, $client_socket);
	}

	private function disconnect($client_socket) {
		if(($key = array_search($client_socket, $this->client_sockets)) !== false) {
		    unset($this->client_socket[$key]);
		}

		$message = "Client $client_socket has been disconnected\n";
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

$ip = "192.168.200.86";
$port = 9009;
$max_buffer_size = 4096;
$backlog = 20;
$l2l_server = new l2l_server($ip, $port, $max_buffer_size, $backlog);
$l2l_server->run();

?>