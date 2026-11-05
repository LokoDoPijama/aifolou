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


    public function move($direction) {
        switch ($direction) {
            case 'up':
                    $this->position[1]--;
                break;
            
            case 'down':
                    $this->position[1]++;
                break;
            
            case 'left':
                    $this->position[0]--;
                break;
            
            case 'right':
                    $this->position[0]++;
                break;
            
            default:
                break;
        }
    }

}