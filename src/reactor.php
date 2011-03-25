<?php

namespace asyncio;

class Reactor {

	private $ev_base;
	
	public function __construct() {

		$this->ev_base = event_base_new();
		if (!$this->ev_base) throw new \Exception("Cannot initialize libevent");

	}


	public function run() {

		$ret = event_base_loop($this->ev_base);
		if ($ret == -1) throw new \Exception("Cannot launch libevent's loop");
		if ($ret == 1) throw new \Exception("No events were registered");
		
	}


	public function halt() {

		event_base_loopbreak($this->ev_base);

	}


	public function finish() {

		event_base_loopexit($this->ev_base);

	}


}

