<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WsHandler;

require __DIR__ . '/../vendor/autoload.php';

try {
	$server = IoServer::factory(
		new HttpServer(
			new WsServer(
				new WsHandler()
			)
		),
		8080,
		'0.0.0.0'
	);

	echo "WebSocket server running on 0.0.0.0:8080\n";
	$server->run();
} catch (Exception $e) {
	echo "An error has occurred: " . $e->getMessage() . "\n";
	echo "An eroro getTraceAsString" . $e->getTraceAsString();
	echo "Eror code" . $e->getCode();
}
