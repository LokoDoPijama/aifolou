<?php
namespace Aifolou;
use Aifolou\GameHandler;

class Player {

    public function __construct(
        public int $id,
        public ?string $name = null,
        public array $position = [0, 0], // [x, y]
    ) {
        if ($name == null) $this->name = 'P' . $id;
    }


    // Tenta mover o player e retorna se o movimento foi válido
    public function move($direction) : bool {
        $valid = false;

        switch ($direction) {
            case 'up':
                if ($this->position[1] != 0) {
                    $this->position[1]--;
                    $valid = true;
                }
                break;
            
            case 'down':
                if ($this->position[1] != GameHandler::$field[1]) {
                    $this->position[1]++;
                    $valid = true;
                };
                break;
            
            case 'left':
                if ($this->position[0] != 0) {
                    $this->position[0]--;
                    $valid = true;
                };
                break;
            
            case 'right':
                if ($this->position[0] != GameHandler::$field[0]) {
                    $this->position[0]++;
                    $valid = true;
                };
                break;
            
            default:
                break;
        }

        return $valid;
    }

}