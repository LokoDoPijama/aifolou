<?php

interface Calculos {
    public function calcularDano($danoInicial, $defesa);
}

class Jogador implements Calculos {
    public function calcularDano($danoInicial, $defesa) {
        $dano = $danoInicial - $defesa;

        return $dano > 0 ? $dano : 1;
    }
}

class Inimigo implements Calculos {
    public function calcularDano($danoInicial, $defesa) {
        $dano = $danoInicial - $defesa / 2;

        return $dano > 0 ? $dano : 1;
    }
}

class Entidade {

    private $entidade;
    public $vida;
    public $ataque;
    public $defesa;

    public function __construct($entidade, $vida, $ataque, $defesa) {
        $this->entidade = $entidade;
        $this->vida = $vida;
        $this->ataque = $ataque;
        $this->defesa = $defesa;
    }

    public function levarDano($danoInicial, $defesa) {
        $this->vida -= $this->entidade->calcularDano($danoInicial, $defesa);
        $this->vida = max($this->vida, 0);
    }

}

header('Content-type: application/json'); // Setando a header para mandar os dados de volta


// Pegando os dados passados por request HTTP

$json = json_decode($_POST['entidade']);
$dano = $_POST['dano'];


// Instaciando classe de acordo com quem está atacando

if ($json->tipo == "jogador") {
    $tipo = new Jogador;
} else if ($json->tipo == "inimigo") {
    $tipo = new Inimigo;
}

$entidade = new Entidade($tipo, $json->vida, $json->ataque, $json->defesa);

$entidade->levarDano($dano, $entidade->defesa);

$json->vida = $entidade->vida;

echo json_encode($json);