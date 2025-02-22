<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>aifolou iu</title>

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="images/aifolou.jpg">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

    <style>
        html {
            scroll-behavior: auto !important; /* Desabilita smooth scrolling */
        }

        body {
            overflow: hidden;
            background-image: url('images/bliss.png');
            width: 4510px;
            height: 3627px;
            font-family: 'Times New Roman', Times, serif;
        }

        #nameWrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #chat {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 25vw;
            min-width: 400px;
            max-height: 30vh;
            background-color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
            word-wrap: break-word;
        }

        #chat #messages {
            max-height: 20vh;
            overflow-y: auto;
        }

        #chat #messages p {
            width: 100%;
        }

        #chat #textInput {
            width: 100%;
        }

        #playersWrapper {
            position: relative;
        }

        .player {
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

    <div id="nameWrapper">
        <input id="textInputName" type="text" placeholder="Seu nome">
        <button id="btnName">Enviar</button>
    </div>


    <div id="playersWrapper" style="display: none">
    </div>

    <div id="chat" class="p-2" style="display: none">
        <div id="messages">
        </div>
        <input id="textInput" type="text" placeholder="Aperte Enter para digitar no chat">
    </div>


    <script src="js/jquery-3.7.1.min.js"></script>

    <script>

        // Game logic

        $('#textInputName').focus();

        let gameStart = false;

        let player;
        let playerElement;

        let players = [];
        
        let field = {
            "width": Math.floor(parseInt($('body').css("width")) / 128),
            "height": Math.floor(parseInt($('body').css("height")) / 128)
        };

        let toBeValidated = []; // Movimentos a serem validados pelo servidor


        {
            /* Balela */

            let inimigo = {
                "tipo": "inimigo",
                "vida": 50,
                "ataque": 5,
                "defesa": 2,
                "posicao": null
            };

            let imgInimigo = document.createElement('img');

            imgInimigo.src = "images/aifolou.jpg";
            imgInimigo.width = 128;
            imgInimigo.height = 128;
            imgInimigo.style.position = "absolute";
            inimigo.posicao = [Math.round(Math.random() * field.width), Math.round(Math.random() * field.height)];
            imgInimigo.style.top = inimigo.posicao[1] * 128 + "px";
            imgInimigo.style.left = inimigo.posicao[0] * 128 + "px";
            imgInimigo.style.animation = "jiggle 700ms infinite";

            document.body.appendChild(imgInimigo);

            /* Fim Balela */
        }


        function move(direction) { // Apenas para mover você mesmo
            let valid = false;

            if (direction == 'up' && player.position[1] > 0) {
                valid = true;
                player.position[1] -= 1;
                playerElement.css("top", () => {return player.position[1] * 128});

                if (parseInt(playerElement.css("top")) - document.documentElement.scrollTop < window.innerHeight / 2) {
                    scrollBy(0, -128);
                }
            } else if (direction == 'down' && player.position[1] < field.height) {
                valid = true;
                player.position[1] += 1;
                playerElement.css("top", () => {return player.position[1] * 128});

                if (parseInt(playerElement.css("top")) - document.documentElement.scrollTop > window.innerHeight / 2) {
                    scrollBy(0, 128);
                }
            } else if (direction == 'left' && player.position[0] > 0) {
                valid = true;
                player.position[0] -= 1;
                playerElement.css("left", () => {return player.position[0] * 128});

                if (parseInt(playerElement.css("left")) - document.documentElement.scrollLeft < window.innerWidth / 2) {
                    scrollBy(-128, 0);
                }
            } else if (direction == 'right' && player.position[0] < field.width) {
                valid = true;
                player.position[0] += 1;
                playerElement.css("left", () => {return player.position[0] * 128});

                if (player.position[0] * 128 - document.documentElement.scrollLeft > window.innerWidth / 2) {
                    scrollBy(128, 0);
                }
            }

            return valid;
        }

        function absoluteMove(playerToMove, direction) { // O absoluteMove força o movimento do player sem verificar se é válido.
            let element = $('#player' + playerToMove.id);
            let ownPlayer = playerToMove.id == player.id; // Se o player a se mover for o próprio player, mover a câmera também (se precisar)

            switch (direction) {
                case 'up':
                    playerToMove.position[1]--;
                    element.css("top", playerToMove.position[1] * 128);

                    if (ownPlayer && parseInt(element.css("top")) - document.documentElement.scrollTop < window.innerHeight / 2) {
                        scrollBy(0, -128);
                    }
                    break;
            
                case 'down':
                    playerToMove.position[1]++;
                    element.css("top", playerToMove.position[1] * 128);

                    if (ownPlayer && parseInt(element.css("top")) - document.documentElement.scrollTop > window.innerHeight / 2) {
                        scrollBy(0, 128);
                    }
                    break;
            
                case 'left':
                    playerToMove.position[0]--;
                    element.css("left", playerToMove.position[0] * 128);

                    if (ownPlayer && parseInt(element.css("left")) - document.documentElement.scrollLeft < window.innerWidth / 2) {
                        scrollBy(-128, 0);
                    }
                    break;
            
                case 'right':
                    playerToMove.position[0]++;
                    element.css("left", playerToMove.position[0] * 128);

                    if (ownPlayer && parseInt(element.css("left")) - document.documentElement.scrollLeft > window.innerWidth / 2) {
                        scrollBy(128, 0);
                    }
                    break;

                default:
                    break;
            }
        }

        function directionFromKey(keyPressed) { // Retorna a direção equivalente a tecla pressionada
            let direction = '';

            if (/ArrowUp|^w$/.test(keyPressed)) {
                direction = 'up';

            } else if (/ArrowDown|^s$/.test(keyPressed)) {
                direction = 'down';

            } else if (/ArrowLeft|^a$/.test(keyPressed)) {
                direction = 'left';

            } else if (/ArrowRight|^d$/.test(keyPressed)) {
                direction = 'right';
            }

            return direction;

        }

        function invertDirection(direction) {
            switch (direction) {
                case 'up':
                    return 'down';
                    break;
            
                case 'down':
                    return 'up';
                    break;
            
                case 'left':
                    return 'right';
                    break;
            
                case 'right':
                    return 'left';
                    break;

                default:
                    return '';
                    break;
            }
        }

        function getPlayerIndexById(id) { // Retorna o index do player no array de players
            for (let index in players) {
                if (players[index].id == id) {
                    return index;
                }
            }
        }

        function setPlayerName(playerToRename, name) { // Trata o nome, tenta mudar e retorna se o nome é válido
            name = name.trim();

            if (name != '') {
                playerToRename.name = name;
                return true;
            }

            return false;
        }

        function startGame() {
            gameStart= true;

            $('#nameWrapper').hide();
            $('#playersWrapper').show();
            $('#chat').show();
        }

        $('#btnName').on('click', function() {
            let name = $('#textInputName').val();

            if (setPlayerName(player, name)) {
                let obj = JSON.stringify({"type": "player", "action": "rename", "name": name});
                ws.send(obj);

                startGame();
            }
        });

        
        $(document).on('keydown', e => {
            if (!gameStart) {
                if (e.key == 'Enter') {
                    let name = $('#textInputName').val();

                    if (setPlayerName(player, name)) {
                        let obj = JSON.stringify({"type": "player", "action": "rename", "name": name});
                        ws.send(obj);

                        startGame();
                    }
                }
                return;
            }

            let chatMessagesDiv = $('#chat #messages')
            let textInput = $('#chat #textInput');
            let chatMessage = textInput.val().trim();

            if (textInput.is(':focus')) {
                if (e.key == 'Enter' && chatMessage != '') {
                    chatMessagesDiv.append(`<p><b>${player.name} (Você):</b> ${chatMessage}</p>`);

                    // Manda mensagem pro servidor
                    let obj = JSON.stringify({"type": "player", "action": "chat", "message": chatMessage});

                    textInput.val('');
                    textInput.blur();
                    chatMessagesDiv.scrollTop(chatMessagesDiv.prop("scrollHeight"));
                    ws.send(obj);
                }

                return;
            }


            let direction = directionFromKey(e.key);

            if (direction != '') {
                
                // Manda movimento pro servidor
                let obj = JSON.stringify({"type": "player", "action": "move", "direction": direction});
                ws.send(obj);

            } else { // Se a tecla que ele apertou não foi uma tecla de movimento
                if (e.key == 'Enter') {
                    textInput.focus();
                }

                return;
            }


            // Validação de movimento no front-end

            let valid = move(direction); // Tenta movimentar o personagem e retorna se o movimento foi válido

            if (direction != '') {
                let movement = {"direction": direction, "valid": valid};
                toBeValidated.push(movement); // Manda pra lista de movimentos a serem validados pelo servidor
            }

            if (valid) { // Se o movimento foi válido, é aplicado uma animação
                playerElement.removeClass("jiggle");
                playerElement.get(0).offsetWidth;
                playerElement.addClass("jiggle");
            }
        });

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }


        // Websocket

        const ws = new WebSocket('ws://<?php echo getHostByName(getHostName()) ?>:8080');

        ws.onopen = function(e) {
            console.log("Connection established!");
        };

        ws.addEventListener('message', e => console.log(e.data));

        ws.addEventListener('message', e => {
            let msg = JSON.parse(e.data);

            if (msg.type == 'player') {
                if (msg.action == 'move') { // PLAYER SE MOVEU
                    let playerToMove;
                    players.forEach(p => {
                        if (p.id == msg.playerId) playerToMove = p;
                    });

                    if (msg.playerId == player.id) {
                        // Servidor mandou seu movimento validado

                        if (msg.valid == toBeValidated[0].valid) { // Front-end e servidores concordaram
                            console.log('alright with this move')
                        } else if (toBeValidated[0].valid) { // Front-end marcou incorretamente como válido, aplicar rubber band
                            console.log('RUBBER BAND')
                            let direction = invertDirection(msg.direction);
                            absoluteMove(playerToMove, direction);
                        } else { // Front-end marcou incorretamente como inválido, aplicar movimento
                            absoluteMove(playerToMove, msg.direction);
                        }

                        toBeValidated.splice(0, 1);
                    } else {
                        // Se o servidor mandou mover outro player que não seja você
                        absoluteMove(playerToMove, msg.direction);
                        let element = $('#player' + playerToMove.id);

                        if (gameStart) {
                            element.removeClass("jiggle");
                            element.get(0).offsetWidth;
                            element.addClass("jiggle");
                        }
                    }

                } else if (msg.action == 'chat') { // PLAYER MANDOU MENSAGEM NO CHAT
                    let chatMessagesDiv = $('#chat #messages')
                    let textInput = $('#chat #textInput');

                    chatMessagesDiv.append(`<p><b>${msg.playerName}:</b> ${msg.message}</p>`);
                    chatMessagesDiv.scrollTop(chatMessagesDiv.prop("scrollHeight"));

                } else if (msg.action == 'rename') { // PLAYER MUDOU DE NOME
                    let index = getPlayerIndexById(msg.playerId);
                    setPlayerName(players[index], msg.playerName);

                } else if (msg.action == 'connect') { // PLAYER SE CONECTOU
                    let newPlayer = {
                        "id": msg.playerId,
                        "name": "P" + msg.playerId,
                        "position": [0, 0]
                    };

                    players.push(newPlayer);
                    $('#playersWrapper').append(`<img id="player${newPlayer.id}" class="player" style="top: ${newPlayer.position[1] * 128}px; left: ${newPlayer.position[0] * 128}px" src="images/aifolou.jpg" alt="i">`);

                    // Se for o primeiro player (você mesmo)
                    if (players.length == 1) { 
                        console.log('setando player e playerElement')
                        player = newPlayer;
                        playerElement = $('#player' + player.id);
                    }
                } else if (msg.action == 'disconnect') { // PLAYER DISCONECTOU
                    // Tirando da lista de players
                    let indexToRemove;
                    for (let index in players) {
                        if (msg.playerId == players[index].id) {
                            indexToRemove = index;
                            break;
                        }
                    }
                    players.splice(indexToRemove, 1);

                    // Tirando elemento
                    $('#player' + msg.playerId).remove();

                }
            } else if (msg.type == 'populate') {
                for (let key in msg.players) {
                    players.push(msg.players[key]);
                    $('#playersWrapper').append(`<img id="player${key}" class="player" style="top: ${msg.players[key].position[1] * 128}px; left: ${msg.players[key].position[0] * 128}px" src="images/aifolou.jpg" alt="i">`);
                }
            }
        });

    </script>
</body>
</html>