<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Aifolou\WebSocketHandler;

    require dirname(__DIR__) . '\vendor\autoload.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new WebSocketHandler()
            )
        ),
        8080
    );

    $server->run();