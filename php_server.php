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

class l2l_client_socket {
	public $socket;
	public $handShook;
	public $headers;

	function __construct($socket, $handShook = false, $headers = null) {
		$this->socket = $socket;
		$this->handShook = $handShook;
		$this->headers = $headers;		
	}

	function __destruct() {
		unset($this->socket);
		unset($this->handShook);
		unset($this->headers);
	}
}

class l2l_server {

	private $ip;
	private $port;
	private $master_socket;
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
		$this->createSocket();
		$this->setSocketOptions();
		$this->bindAndListen();

		if(!empty($this->runServer)) {
			$this->socketInfo("Server started! Listening on: IP=[$ip] port=[$port] Master socket=[". $this->master_socket . "]");
		}
	}

	private function createSocket() {
		$this->master_socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if (!is_resource($this->master_socket)) {
			$this->socketError("Unable to create socket.", true);
		}
		
		// $this->setSocketToNonBlockingMode($this->master_socket, true);
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
		print PHP_EOL . date('Y-m-d H:i:s') . ": " . $message . socket_strerror(socket_last_error()) . PHP_EOL;
		$this->runServer = !$showStoppingError;
	}

	private function socketInfo($message) {
		print PHP_EOL . date('Y-m-d H:i:s') . ": " . $message . PHP_EOL;
	}

	public function run() {

		while($this->runServer)
		{
			$buffer = null;

			$this->socketInfo("Accepting new client sockets...");

			if(sizeof($this->client_sockets) == 0 && ($client_socket = socket_accept($this->master_socket)) !== false)
		    {
				$this->socketInfo("New socket client=[" . $client_socket . "] resource=[" . get_resource_type($client_socket) . "]");

				$this->connect($client_socket);

				// $pid = pcntl_fork(); 

				// if ($pid == -1 || $pid > 0) 
				// { 
				// 	//fork failed *
				// 	$this->socketError("FAILED to fork ", true);
				// }
		    } 
		    else {
		    	$this->socketInfo("No new client sockets to accept...");
		    }

			$this->socketInfo("total_clients=[" . sizeof($this->client_sockets) . "]");

		    // Handle Input From 
		    foreach ($this->client_sockets as $key => $l2l_client_socket) { // for each client        

		    	$client = $l2l_client_socket->socket;
		    	
				$this->socketInfo("Next client=[$client]");

				$numBytes = @socket_recv($client, $buffer, $this->max_buffer_size, 0); 
				
				$this->socketInfo("Received bytes=[$numBytes]");

				if ($numBytes === false) {
				
					$sockErrNo = socket_last_error($client);
					switch ($sockErrNo)
					{
						case 102: // ENETRESET    -- Network dropped connection because of reset
						case 103: // ECONNABORTED -- Software caused connection abort
						case 104: // ECONNRESET   -- Connection reset by peer
						case 108: // ESHUTDOWN    -- Cannot send after transport endpoint shutdown -- probably more of an error on our part, if we're trying to write after the socket is closed.  Probably not a critical error, though.
						case 110: // ETIMEDOUT    -- Connection timed out
						case 111: // ECONNREFUSED -- Connection refused -- We shouldn't see this one, since we're listening... Still not a critical error.
						case 112: // EHOSTDOWN    -- Host is down -- Again, we shouldn't see this, and again, not critical because it's just one connection and we still want to listen to/for others.
						case 113: // EHOSTUNREACH -- No route to host
						case 121: // EREMOTEIO    -- Rempte I/O error -- Their hard drive just blew up.
						case 125: // ECANCELED    -- Operation canceled

						$this->socketInfo("Socket disconnect=[$client] error=[" . socket_strerror($sockErrNo) . "]");
						$this->disconnect($client); // disconnect before clearing error, in case someone with their own implementation wants to check for error conditions on the socket.
						break;
						default:

						if(!$l2l_client_socket->handShook) {
							continue;
						}

						$this->socketInfo("Socket error=[$client] error=[" . socket_strerror($sockErrNo) . "]");
					}

				}
				else if ($numBytes == 0) {
					$this->disconnect($client);
				} 
				else if(!isset($l2l_client_socket->handShook)) {
		            $this->handShake($l2l_client_socket, $buffer);
				}
				else {
		            $message = "$client: $buffer";
					$this->socketInfo("New Message: client=[$client] message=[" . $message . "]");
					if(!$l2l_client_socket->handShook) {
						$this->handShake($l2l_client_socket, $buffer);
					}
					else {
			            $this->broadcast($message);
					}
				}

		  //   	if (false === ($buffer = socket_read($client, $this->max_buffer_size, PHP_BINARY_READ))) {
		  //       	$this->socketError("Failed to read from client socket[$client]");
	   //          }

	   //          $buffer = trim($buffer);

	   //          if(!empty($buffer)) {
	   //          	$lowerCase = strtolower($buffer);

	   //          	if(strcmp($lowerCase, "quit") == 0
	   //          		|| strcmp($lowerCase, "shutdown") == 0) {
				// 		socket_close($client);
				// 		$this->disconnect($client);
	   //          	}
	   //          }

	   //          $message = "$client: $buffer";
				// $this->socketInfo("Read message=[" . $message . "]");
	   //          $this->broadcast($message, $client);
		    }   
		}
	}

	private function broadcast($message, $l2l_client_recepient = "") {

		$this->socketInfo("Broadcasting message=[$message]");

		foreach($this->client_sockets as $key => $next_client_recepient) {
			if($next_client_recepient != $l2l_client_recepient 
				&& $next_client_recepient->handShook
				&& !socket_write($next_client_recepient->socket, $message)) {
				$this->socketInfo("FAILED to broadcast to=[" . $next_client_socket . "]: message=[" . $message . "]");
				$this->disconnect($next_client_recepient);
			}
			else {
				$this->socketInfo("Message Broadcasted to=[" . $next_client_recepient->socket . "]: message=[" . $message . "]");
			}
		}
	}

	private function connect($client_socket) {
		$message = "Client $client_socket has connected\n";

		$l2l_client_socket = new l2l_client_socket($client_socket);

		$this->broadcast($message);
		// $this->setSocketToNonBlockingMode($client_socket);
		$this->client_sockets[sizeof($this->client_sockets)] = $l2l_client_socket;

		$this->socketInfo("Added client_socket=[" . $client_socket . "] total_clients=[" . sizeof($this->client_sockets) . "]");
	}

	private function disconnect($l2l_client_socket) {
		
		$message = "Client " . $l2l_client_socket->socket . " has been disconnected\n";
		
		$this->socketInfo("Removing client_socket=[" . $l2l_client_socket->socket . "]");

		if($l2l_client_socket->handShook) {
			$this->broadcast($message);
		}
		
		$key = array_search($l2l_client_socket, $this->client_sockets);
		if(isset($key)) {
		    unset($this->client_sockets[$key]);
		}
		if(is_resource($l2l_client_socket->socket)) {
			socket_close($l2l_client_socket->socket);
		}
		
		unset($client_socket);
	}

	private function handShake($l2l_client_socket, $buffer) {

		$magicGUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
		$headers = array();
		$lines = explode("\n",$buffer);

		foreach ($lines as $line) {
			if (strpos($line,":") !== false) {
				$header = explode(":",$line,2);
				$headers[strtolower(trim($header[0]))] = trim($header[1]);
				}
			elseif (stripos($line,"get ") !== false) {
				preg_match("/GET (.*) HTTP/i", $buffer, $reqResource);
				$headers['get'] = trim($reqResource[1]);
			}
		}

		echo "HEADERS:\n";
		print_r($headers);
		echo "\n\n";

		// Request Method
		// if (isset($headers['get'])) {
		// 	$user->requestedResource = $headers['get'];
		// } 
		// else {
		// 	// todo: fail the connection
		// 	$handshakeResponse = "HTTP/1.1 405 Method Not Allowed\r\n\r\n";     
		// }

		// //Control who we will be accepting...
		// if (!isset($headers['host']) || !$this->checkHost($headers['host'])) {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// }
		// if (!isset($headers['upgrade']) || strtolower($headers['upgrade']) != 'websocket') {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// } 
		// if (!isset($headers['connection']) || strpos(strtolower($headers['connection']), 'upgrade') === FALSE) {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// }
		// if (!isset($headers['sec-websocket-key'])) {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// } 
		// else {

		// }

		// if (!isset($headers['sec-websocket-version']) || strtolower($headers['sec-websocket-version']) != 13) {
		// 	$handshakeResponse = "HTTP/1.1 426 Upgrade Required\r\nSec-WebSocketVersion: 13";
		// }
		// if (($this->headerOriginRequired && !isset($headers['origin']) ) || ($this->headerOriginRequired && !$this->checkOrigin($headers['origin']))) {
		// 	$handshakeResponse = "HTTP/1.1 403 Forbidden";
		// }
		// if (($this->headerSecWebSocketProtocolRequired && !isset($headers['sec-websocket-protocol'])) || ($this->headerSecWebSocketProtocolRequired && !$this->checkWebsocProtocol($headers['sec-websocket-protocol']))) {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// }
		// if (($this->headerSecWebSocketExtensionsRequired && !isset($headers['sec-websocket-extensions'])) || ($this->headerSecWebSocketExtensionsRequired && !$this->checkWebsocExtensions($headers['sec-websocket-extensions']))) {
		// 	$handshakeResponse = "HTTP/1.1 400 Bad Request";
		// }

		// Done verifying the _required_ headers and optionally required headers.

		// if (isset($handshakeResponse)) {
		// 	socket_write($user->socket,$handshakeResponse,strlen($handshakeResponse));
		// 	$this->disconnect($user->socket);
		// 	return;
		// }

		// $user->headers = $headers;
		// $user->handshake = $buffer;

		$webSocketKeyHash = sha1($headers['sec-websocket-key'] . $magicGUID);

		$rawToken = "";
		for ($i = 0; $i < 20; $i++) {
			$rawToken .= chr(hexdec(substr($webSocketKeyHash,$i*2, 2)));
		}
		$handshakeToken = base64_encode($rawToken) . "\r\n";

		$subProtocol = (isset($headers['sec-websocket-protocol'])) ? $this->processProtocol($headers['sec-websocket-protocol']) : "";
		$extensions = (isset($headers['sec-websocket-extensions'])) ? $this->processExtensions($headers['sec-websocket-extensions']) : "";

		$handshakeResponse = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: $handshakeToken$subProtocol$extensions\r\n";
		if(socket_write($l2l_client_socket->socket,$handshakeResponse,strlen($handshakeResponse))) {
			$l2l_client_socket->handShook = 1;
		}
		else {
			$this->socketInfo("FAILED to send welcome message to=[" . $l2l_client_socket->socket . "]: [" . $message . "]");
			$this->disconnect($l2l_client_socket->socket);
		}
		
		
        //TODO: Welcome new socket
  //       $message = "Welcome " . $l2l_client_socket->socket;

  //       $this->socketInfo("Sending welcome message to client_socket=[" . $l2l_client_socket->socket . "] message=[$message]");	

  //       if(!socket_write($l2l_client_socket->socket, $message)) {
		// 	$this->socketInfo("FAILED to send welcome message to=[" . $l2l_client_socket->socket . "]: [" . $message . "]");
		// 	$this->disconnect($l2l_client_socket->socket);
		// }
		// else {
		// 	$this->socketInfo("Send welcome message to=[" . $l2l_client_socket->socket . "]: [" . $message . "]");
		// }

		$this->broadcast("client socket=[" . $l2l_client_socket->socket . "] joined", $l2l_client_socket);
        
	}

	private function processProtocol($protocol) {
		// return either "Sec-WebSocket-Protocol: SelectedProtocolFromClientList\r\n" or return an empty string.  
		// The carriage return/newline combo must appear at the end of a non-empty string, and must not
		// appear at the beginning of the string nor in an otherwise empty string, or it will be considered part of 
		// the response body, which will trigger an error in the client as it will not be formatted correctly.
		return ""; 
	  }

	private function processExtensions($extensions) {
		// return either "Sec-WebSocket-Extensions: SelectedExtensions\r\n" or return an empty string.
		return ""; 
	}

	function __destruct()
	{
		while(sizeof($this->client_sockets) > 0) {
			$client_socket = array_pop($this->client_sockets);
			unset($client_socket);
		}

		if(is_resource($this->master_socket)) {
			socket_close($this->master_socket);
		}
		
		unset($this->ip);
		unset($this->port);
		unset($this->master_socket);
		unset($this->max_buffer_size);
		unset($this->backlog);
		unset($this->runServer);
	}
}

$ip = "192.168.200.86";
$port = 9090;
$max_buffer_size = 512;
$backlog = 20;
$l2l_server = new l2l_server($ip, $port, $max_buffer_size, $backlog);
$l2l_server->run();

?>
