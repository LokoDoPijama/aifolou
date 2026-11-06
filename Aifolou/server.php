<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Aifolou\WebSocketHandler;

    require_once __DIR__ . '/../vendor/autoload.php';

    $portDebug = $argv[1] ?? null;

    $host = '0.0.0.0';
    $port = (int) (getenv('PORT') ?: 10000);

    $handler = new WebSocketHandler();

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                $handler
            )
        ),
        $portDebug == null ? $port : $portDebug,
        $host
    );

    $server->loop->addPeriodicTimer(2, function () use ($handler) {
        $handler->gameHandler->mapTest();
    });

    $server->run();