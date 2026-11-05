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
                $response['playerId'] = $playerId;

                if ($message['action'] == 'move') { // Se for movimentação
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

                } elseif ($message['action'] == 'chat') { // Se for mensagem de chat
                    $response['playerName'] = $this->players[$playerId]->name;
                    $response['message'] = substr($message['message'],0,9999999); // No máximo 9999999 caracteres
                    $response['sendTo'] = SendTo::EveryoneElse;

                } elseif ($message['action'] == 'rename') { // Se o player está mudando de nome
                    $name = trim($message['name']);

                    if ($name != '') {
                        $this->players[$playerId]->name = $name;
                        $response['playerName'] = $name;
                        $response['sendTo'] = SendTo::EveryoneElse;
                    }  else {
                        $response['sendTo'] = SendTo::NoOne;
                    }

                }
                break;
            
            default:
                break;
        }

        return $response;
    }

    public function movePlayer($playerId, $direction) : bool {

        if ($this->isPlayerMoveValid($this->players[$playerId], $direction)) {
            $this->players[$playerId]->move($direction);
            return true;
        }

        return false;
    }

    private function isPlayerMoveValid($player, $direction) : bool {
        $valid = true;


        // Checkando se player tentou se mover além da borda

        switch ($direction) {
            case 'up':
                if ($player->position[1] == 0) {
                    $valid = false;
                }
                break;
            
            case 'down':
                if ($player->position[1] == GameHandler::$field[1]) {
                    $valid = false;
                }
                break;
            
            case 'left':
                if ($player->position[0] == 0) {
                    $valid = false;
                }
                break;
            
            case 'right':
                if ($player->position[0] == GameHandler::$field[0]) {
                    $valid = false;
                }
                break;
            
            default:
                break;
        }

        if (!$valid) return false;


        // Checkando se há outro player na nova posição

        $directionArray = $this->translateDirectionStringToArray($direction);
        $newPosition = [
            $player->position[0] + $directionArray[0],
            $player->position[1] + $directionArray[1]
        ];

        foreach ($this->players as $otherPlayer) {
            if ($otherPlayer->id == $player->id) {
                continue;
            }

            if ($otherPlayer->position[0] == $newPosition[0] &&
                $otherPlayer->position[1] == $newPosition[1]) {
                $valid = false;
            }
        }

        return $valid;
    }
    
    private function translateDirectionStringToArray(string $direction) {
        switch ($direction) {
            case 'up':
                return [0, -1];
                break;
            
            case 'down':
                return [0, 1];
                break;
            
            case 'left':
                return [-1, 0];
                break;
            
            case 'right':
                return [1, 0];
                break;
            
            default:
                return [0,0];
                break;
        }

        return [0,0];

    }


}