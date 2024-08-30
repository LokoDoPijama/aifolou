<?php

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Inversão de Dependências</title>

    <style>
        body {
            overflow: hidden;
            background-image: url('images/bliss.png');
            width: 4510px;
            height: 3627px;
        }

        #aifolou {
            aspect-ratio: 1/1;
            height: 128px;
            position: absolute;
        }

        .jiggle {
            animation: jiggle 120ms;
        }

        @keyframes jiggle {
            33% { transform: rotate(-25deg) }
            66% { transform: rotate(25deg) }
            100% { transform: rotate(0deg) }
        }
    </style>
</head>
<body>


    <div style="position: relative">
        <img id="aifolou" style="top: 0; left: 0" src="images/aifolou.jpg" alt="i">
    </div>

    <p id="vidaJogador" style="position: fixed; bottom : 0;">Sua Vida: 200</p>
    <p id="vidaInimigo" style="position: fixed; bottom: 0; left: 150px">Vida do Inimigo: 50</p>



    <script src="js/jquery-3.7.1.min.js"></script>

    <script>

        let body = {
            "width": parseInt($('body').css("width")),
            "height": parseInt($('body').css("height"))
        };
        let aifolou = $('#aifolou');
        let turnoDoJogador = true;
        let enviandoRequest = false;

        let player = {
            "tipo": "jogador",
            "vida": 200,
            "ataque": 10,
            "defesa": 5
        };

        let inimigo = {
            "tipo": "inimigo",
            "vida": 50,
            "ataque": 5,
            "defesa": 2
        };


        let imgInimigo = document.createElement('img');

        imgInimigo.src = "images/aifolou.jpg";
        imgInimigo.width = 128;
        imgInimigo.height = 128;
        imgInimigo.style.position = "absolute";
        imgInimigo.style.top = Math.round( Math.random() * Math.floor(body.height / 128) ) * 128 + "px";
        imgInimigo.style.left = Math.round( Math.random() * Math.floor(body.width / 128) ) * 128 + "px";
        imgInimigo.style.animation = "jiggle 700ms infinite";

        document.body.appendChild(imgInimigo);


        function movimentar(tecla) {
            let movimentoValido = false;

            if (/ArrowUp|^w$/.test(tecla) && 0 <= parseInt(aifolou.css("top")) - 128 ) {
                aifolou.css("top", () => {return parseInt(aifolou.css("top")) - 128});
                movimentoValido = true;

                if (parseInt(aifolou.css("top")) - document.documentElement.scrollTop < window.innerHeight / 2) {
                    scrollBy(0, -128);
                }
            } else if (/ArrowDown|^s$/.test(tecla) && body.height - 128 >= parseInt(aifolou.css("top")) + 128 ) {
                aifolou.css("top", () => {return parseInt(aifolou.css("top")) + 128});
                movimentoValido = true;

                if (parseInt(aifolou.css("top")) - document.documentElement.scrollTop > window.innerHeight / 2) {
                    scrollBy(0, 128);
                }
            } else if (/ArrowLeft|^a$/.test(tecla) && 0 <= parseInt(aifolou.css("left")) - 128 ) {
                aifolou.css("left", () => {return parseInt(aifolou.css("left")) - 128});
                movimentoValido = true;

                if (parseInt(aifolou.css("left")) - document.documentElement.scrollLeft < window.innerWidth / 2) {
                    scrollBy(-128, 0);
                }
            } else if (/ArrowRight|^d$/.test(tecla) && body.width - 128 >= parseInt(aifolou.css("left")) + 128 ) {
                aifolou.css("left", () => {return parseInt(aifolou.css("left")) + 128});
                movimentoValido = true;

                if (parseInt(aifolou.css("left")) - document.documentElement.scrollLeft > window.innerWidth / 2) {
                    scrollBy(128, 0);
                }
            }

            return movimentoValido;
        }

        function turno() {
            let json_str;
            let dano;

            if (turnoDoJogador) {
                json_str = JSON.stringify(inimigo);
                dano = player.ataque;
            } else {
                json_str = JSON.stringify(player);
                dano = inimigo.ataque;
            }

            let sendable = new FormData();
            sendable.append('entidade', json_str);
            sendable.append('dano', dano);

            let request = new XMLHttpRequest();

            request.open("POST", "processo.php", true);

            request.onreadystatechange = function() {
                if (request.readyState === 4 && request.status === 200) {
                    console.log("RESPONSE: " + JSON.parse(request.responseText).vida);

                    let vida = JSON.parse(request.responseText).vida;

                    if (turnoDoJogador) {
                        inimigo.vida = vida;
                        $('#vidaInimigo').text("Vida do Inimigo: " + vida);
                    } else {
                        player.vida = vida;
                        $('#vidaJogador').text("Sua Vida: " + vida);
                    }

                    turnoDoJogador = !turnoDoJogador;
                    enviandoRequest = false;
                }
            }

            if (!enviandoRequest) {
                request.send(sendable);
                enviandoRequest = true;
                console.log("enviando request...")
            }
        }

        $(document).on('keydown', e => {

            let movimentoValido = movimentar(e.key);

            if (movimentoValido) {
                aifolou.removeClass("jiggle");
                aifolou.get(0).offsetWidth;
                aifolou.addClass("jiggle");
            } else {
                return;
            }

            turno();

        });

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }

    </script>
</body>
</html>