<?php
namespace Aifolou;

class GameHandler {
    public array $players = [];
    public static $field = [35, 28]; // [x, y]

    public function processMessage($playerId, $msg) {
        $message = json_decode($msg, true);

        $response = ['type' => $message['type'],];

        switch ($message['type']) {
            case 'player':
                $response['action'] = $message['action'];

                if ($message['action'] == 'move') {
                    $response['playerId'] = $playerId;
                    $response['direction'] = $message['direction'];
                    
                    $valid = $this->movePlayer($playerId, $message['direction']);
                    $response['valid'] = $valid;

                    if ($valid) {
                        $response['sendTo'] = SendTo::Everyone;
                    } else {
                        $response['sendTo'] = SendTo::Me;
                    }

                    // Checkar se a mensagem é lixo (se não deve ser enviada a ninguém)
                    if (!in_array($message['direction'], ['up', 'down', 'left', 'right'])) {
                        $response['sendTo'] = SendTo::NoOne;
                    }
                }
                break;
            
            default:
                break;
        }

        return $response;
    }

    public function movePlayer($playerId, $direction) {
        return $this->players[$playerId]->move($direction);
    }
    
}