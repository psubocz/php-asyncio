<?php
namespace asyncio;

abstract class EventEmitter {

	private $listeners = array();
	
	public function addEventListener($event, $callback) {
	
		if (!isset($this->listeners[$event])) {
			$this->listeners[$event] = array($callback);
		} else {
			$this->listeners[$event][] = $callback;
		}
	
	}

	public function removeEventListener($event, $callback) {

		if (!isset($this->listeners[$event])) throw new Exception("No listeners for event ".$event);
		if (!isset($this->listeners[$event][$callback])) throw new Exception("Callback is not listening for event ".$event);
		unset($this->listeners[$event][$callback]);

	}

	protected function emit($event) {

		if (!isset($this->listeners[$event])) return;
		$args = func_get_args();
		array_shift($args);
		foreach($this->listeners[$event] as $callback) {
			call_user_func_array($callback, $args);
		}

	}


}

