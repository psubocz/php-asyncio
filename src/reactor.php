<?php

namespace asyncio;


class Reactor {

	private static $instance = null;
	private $ev_base;

	public static function init() {

		if (self::$instance) return;
		self::$instance = new Reactor();

	}

	private function __construct() {

		$this->ev_base = event_base_new();
		if (!$this->ev_base) throw new \Exception("Cannot initialize libevent");

	}


	public static function run() {

		if (!self::$instance) self::init();
		
		$ret = event_base_loop(self::$instance->ev_base);
		if ($ret == -1) throw new \Exception("Cannot launch libevent's loop");
		//if ($ret == 1) throw new \Exception("No events were registered");

	}


	public static function halt() {

		if (!self::$instance) self::init();
		event_base_loopbreak(self::$instance->ev_base);

	}


	public static function finish() {

		if (!self::$instance) self::init();
		event_base_loopexit(self::$instance->ev_base);

	}


	public static function registerEvent($event) {

		if (!self::$instance) self::init();
		if (!event_base_set($event, self::$instance->ev_base)) echo "event_base_set err\n";
		if (!event_add($event)) echo "evet_add err\n";
	}

	public static function unregisterEvent($event) {

		if (!self::$instance) self::init();
		event_del($event);

	}


	public static function registerBufferedEvent($bevent) {

		if (!self::$instance) self::init();
		event_buffer_base_set($bevent, self::$instance->ev_base);
		event_buffer_enable($bevent, EV_READ);

	}


	public static function unregisterBufferedEvent($bevent) {

		if (!self::$instance) self::init();
		event_buffer_disable($bevent, EV_READ);

	}


}

