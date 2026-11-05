<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Aifolou\WebSocketHandler;

    require dirname(__DIR__) . '\vendor\autoload.php';

    $handler = new WebSocketHandler();

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                $handler
            )
        ),
        8080
    );

    $server->loop->addPeriodicTimer(2, function () use ($handler) {
        $handler->gameHandler->mapTest();
    });

    $server->run();