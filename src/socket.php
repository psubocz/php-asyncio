<?php
namespace asyncio;

require_once 'eventemitter.php';


/*

Events:
	connect
	data
	close
	error
	timeout

*/
class Socket extends EventEmitter {

	private $sock;
	private $event;
	private $buff;
	
	public function __construct($fd=null) {
		
		if (!$fd) {
			$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		} else {
			$this->sock = $fd;
			$this->initBufferedEvents();
		}
		socket_set_nonblock($this->sock);
		
	}


	public function __destruct() {
echo "destruct\n";
		Reactor::unregisterEvent($this->event);
		event_free($this->event);
		if ($this->buff) {
			Reactor::unregisterBufferedEvent($this->buff);
			event_buffer_free($this->buff);
		}
	}


	public function connect($host, $port, $timeout=0) {

		$this->event = event_new();
		if (!event_set($this->event, $this->sock, EV_WRITE, array($this, '_connev'))) echo "event_set err\n";
		Reactor::registerEvent($this->event);
		@socket_connect($this->sock, $host, $port);

	}


	public function close() {

		socket_shutdown($this->sock);
		$this->emit('onClose');
	}


	public function write($data) {

		if (!event_buffer_write($this->buff, $data)) echo "write error\n";

	}


	private function initBufferedEvents() {
		$this->buff = event_buffer_new($this->sock, array($this, '_buffread'), null, array($this, '_bufferror')); 
		Reactor::registerBufferedEvent($this->buff);

	}

	
	public function _connev($socket, $flag, $args) {
		if (socket_get_option($this->sock, SOL_SOCKET, SO_ERROR)) {
			$this->emit('onError');
		} else {
			$this->initBufferedEvents();
			$this->emit('onConnect');
		}
	}


	public function _buffread($buf, $args) {

		$data = event_buffer_read($this->buff, 4096);
		$this->emit('onData', $data);
		
	}


	public function _bufferror($buf, $flags, $args) {

		if (($flags & EVBUFFER_EOF) != 0) {
			$this->emit('onClose');
			return;
		}
		echo "error ".$flags."\n";
	}

}

