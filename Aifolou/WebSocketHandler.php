<?php
namespace Aifolou;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Aifolou\GameHandler;

class WebSocketHandler implements MessageComponentInterface {

    private $gameHandler;
    private $connections;

    public function __construct() {
        $this->gameHandler = new GameHandler;
        $this->connections = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {

        $this->connections->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
        
        // Avisando a todos (inclusive o novo player) que um novo player se conectou
        $msg = [
            'type' => 'player',
            'action' => 'connect',
            'playerId' => $conn->resourceId,
        ];
        foreach ($this->connections as $existingConn) {
            $existingConn->send(json_encode($msg));
        }

        // Enviando estado atual do jogo para o novo player
        $msg = [
            'type' => 'populate',
            'players' => $this->gameHandler->players,
        ];
        $conn->send(json_encode($msg));

        // Por fim, colocando o novo player na lista de players
        $this->gameHandler->players[$conn->resourceId] = new Player($conn->resourceId);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $response = $this->gameHandler->processMessage($from->resourceId, $msg);

        $responseJson = $response;
        unset($responseJson['sendTo']); // Não quero enviar o campo 'sendTo' no JSON
        $responseJson = json_encode($responseJson);

        if ($response['sendTo'] == SendTo::NoOne) {
            return; // Mensagem é lixo, não mande para ninguém
        }

        if ($response['sendTo'] == SendTo::Me) {
            $from->send($responseJson); // Send it to mensager
            return;
        }

        foreach ($this->connections as $conn) {
            if ($response['sendTo'] == SendTo::EveryoneElse && $conn != $from) {
                $conn->send($responseJson); // Send it to everyone else

            } else if ($response['sendTo'] == SendTo::Everyone) {
                $conn->send($responseJson); // Send it to everyone

            }
        }
    }

    public function onClose(ConnectionInterface $conn)  {
        $this->connections->detach($conn);
        unset($this->gameHandler->players[$conn->resourceId]);

        $msg = [
            'type' => 'player',
            'action' => 'disconnect',
            'playerId' => $conn->resourceId,
        ];

        foreach ($this->connections as $remainingConn) {
            $remainingConn->send(json_encode($msg));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

    }
}