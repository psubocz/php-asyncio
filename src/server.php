<?php
namespace asyncio;

require_once('eventemitter.php');


class Server extends EventEmitter {

	const BACKLOG = 128;
	private $sock;
	private $event;
	

	public function __construct($port, $host=null) {

		if (!$host) $host = '0.0.0.0';
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_nonblock($this->sock);
		socket_bind($this->sock, $host, $port);

		$this->event = event_new();
		event_set($this->event, $this->sock, EV_READ | EV_PERSIST, array($this, '_connev'));
		Reactor::registerEvent($this->event);

		socket_listen($this->sock, self::BACKLOG);
		
	}


	public function _connev($socket, $flag, $args) {

		$client = socket_accept($this->sock);
		$clisock = new Socket($client);
		$this->emit('onConnection', $clisock);
		
	}
}

