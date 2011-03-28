<?php

require_once 'reactor.php';
require_once 'socket.php';
require_once 'server.php';


asyncio\Reactor::init();

$server = new asyncio\Server(8000);
echo "Listening on 0.0.0.0:8000\n";

$server->addEventListener('onConnection', function($sock) {
	$sock->addEventListener('onData', function($data) use ($sock) {
		echo 'got: '.$data;
		$sock->write('server says: '.$data);
	});
});

asyncio\Reactor::run();

