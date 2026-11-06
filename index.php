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

        #chat .chat-messages {
            max-height: 20vh;
            overflow-y: auto;
        }

        #chat .chat-messages p {
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
            object-fit: cover;
        }

        #playerImagePreview {
            aspect-ratio: 1/1;
            height: 128px;
        }

        .jiggle {
            animation: jiggle 120ms;
        }

        @keyframes jiggle {
            33% { transform: rotate(-25deg) }
            66% { transform: rotate(25deg) }
            100% { transform: rotate(0deg) }
        }


        /* Mobile */

        #nameWrapperMobile {
            width: 100%;
            position: absolute;
            top: 45%;
            transform: translateY(-50%);
        }

        #nameWrapperMobile #textInputNameMobile {
            width: 50%;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            font-size: 5rem;
        }

        #nameWrapperMobile #btnNameMobile {
            width: 50%;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            font-size: 5rem;
        }

        #mobileControls {
            position: fixed;
            bottom: 0;
            width: 100%; height: 40%;
            z-index: 9999;
        }

        #mobileControls #chatMobile .chat-messages {
            font-size: 2.5rem;
            height: 500px;
            overflow-y: auto;
        }

        #mobileControls #chatMobile #textInputMobile {
            font-size: 2.5rem;
        }

        #mobileControls #mobileDirections {
            position: relative;
            background-color: red;
            width: 100%;
            height: 55%;
            opacity: 0.4;
        }

        #mobileDirections #leftBtn {
            position: absolute;
            width: 20%;
            height: 50%;
            background-color: blue;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 20%;
        }

        #mobileDirections #rightBtn {
            position: absolute;
            width: 20%;
            height: 50%;
            background-color: blue;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 80%;
        }

        #mobileDirections #upBtn {
            position: absolute;
            width: 20%;
            height: 50%;
            background-color: blue;
            top: 20%;
            transform: translate(-50%, -50%);
            left: 50%;
        }

        #mobileDirections #downBtn {
            position: absolute;
            width: 20%;
            height: 50%;
            background-color: blue;
            top: 80%;
            transform: translate(-50%, -50%);
            left: 50%;
        }

    </style>
</head>
<body>

    <div id="nameWrapper">
        <input id="textInputName" type="text" placeholder="Seu nome">
        <button id="btnName">Enviar</button>

        <div class="d-block mt-4">
            <label for="playerImage" class="d-block fs-5">Imagem do jogador:</label>
            <form id="playerImageForm">
                <input id="playerImageInput" type="file" name="playerImage" class="d-block" onchange="previewPlayerImage()">
            </form>
            <img id="playerImagePreview" src="images/aifolou.jpg" alt="" class="mt-4" style="object-fit: cover">
            <br>
            <span id="imageErrorMsg" class="fs-5 text-danger" style="display: none"><b>ALGO DE ERRADO COM <br> SUA IMAGEM!</b></span>
        </div>
    </div>

    <div id="nameWrapperMobile" style="display: none">
        <input id="textInputNameMobile" type="text" placeholder="Seu nome">
        <button id="btnNameMobile">Enviar</button>
    </div>


    <div id="playersWrapper" style="display: none">
    </div>

    <div id="chat" class="p-2" style="display: none">
        <div class="chat-messages">
        </div>
        <input id="textInput" type="text" placeholder="Aperte Enter para digitar no chat">
    </div>

    <div id="mobileControls" style="display: none;">
        <div class="d-flex flex-column position-relative justify-content-end h-100">
            <div id="chatMobile" class="p-2">
                <div class="chat-messages">
                </div>
                <input id="textInputMobile" type="text" placeholder="Aperte para digitar no chat">
            </div>

            <div id="mobileDirections">
                <div id="leftBtn">
                </div>
                <div id="rightBtn">
                </div>
                <div id="upBtn">
                </div>
                <div id="downBtn">
                </div>
            </div>

        </div>
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

        function verifyValidMovement(direction) {
            let valid = false;


            // Verificando se player tentou ir além da borda do mapa

            let fieldValid = false;

            if (direction == 'up' && player.position[1] > 0) {
                fieldValid = true;
            } else if (direction == 'down' && player.position[1] < field.height) {
                fieldValid = true;
            } else if (direction == 'left' && player.position[0] > 0) {
                fieldValid = true;
            } else if (direction == 'right' && player.position[0] < field.width) {
                fieldValid = true;
            }

            if (!fieldValid) return false;


            // Verificando se há outro player na nova posição

            let collisionValid = true;

            directionArray = [
                direction == 'left' ? -1 : (direction == 'right' ? 1 : 0),
                direction == 'up' ? -1 : (direction == 'down' ? 1 : 0),
            ];

            newPosition = [player.position[0] + directionArray[0], player.position[1] + directionArray[1]];

            players.forEach(p => {
                if (p.id == player.id) return;

                if (p.position[0] == newPosition[0] &&
                    p.position[1] == newPosition[1]) {
                        collisionValid = false;
                }
            });

            valid = fieldValid && collisionValid;

            return valid;
        }

        function triggerMovement(direction) {
            // Manda movimento pro servidor
            let obj = JSON.stringify({"type": "player", "action": "move", "direction": direction});
            ws.send(obj);


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
        }

        function move(direction) { // Apenas para mover você mesmo
            if (verifyValidMovement(direction)) {
                if (direction == 'up' && player.position[1] > 0) {
                    player.position[1] -= 1;
                    playerElement.css("top", () => {return player.position[1] * 128});

                    if (parseInt(playerElement.css("top")) - document.documentElement.scrollTop < window.innerHeight / 2) {
                        scrollBy(0, -128);
                    }
                } else if (direction == 'down' && player.position[1] < field.height) {
                    player.position[1] += 1;
                    playerElement.css("top", () => {return player.position[1] * 128});

                    if (parseInt(playerElement.css("top")) - document.documentElement.scrollTop > window.innerHeight / 2) {
                        scrollBy(0, 128);
                    }
                } else if (direction == 'left' && player.position[0] > 0) {
                    player.position[0] -= 1;
                    playerElement.css("left", () => {return player.position[0] * 128});

                    if (parseInt(playerElement.css("left")) - document.documentElement.scrollLeft < window.innerWidth / 2) {
                        scrollBy(-128, 0);
                    }
                } else if (direction == 'right' && player.position[0] < field.width) {
                    player.position[0] += 1;
                    playerElement.css("left", () => {return player.position[0] * 128});

                    if (player.position[0] * 128 - document.documentElement.scrollLeft > window.innerWidth / 2) {
                        scrollBy(128, 0);
                    }
                }

                return true;
            }

            return false;
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

        function updatePlayerImage(player, imagePath) {
            player.playerImage = imagePath;
            let playerImageElement = $(`#player${player.id}`).attr('src', imagePath);
        }

        function previewPlayerImage() {
            var fr = new FileReader();
            fr.onload = function(e) { $('#playerImagePreview').attr('src', this.result) };
            fr.readAsDataURL($('#playerImageInput')[0].files[0]);
        }

        function sendPlayerImage() {

            let success = true;

            if ($('#playerImageInput')[0].files.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "save_image.php",
                    data: new FormData($('#playerImageForm')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    async: false,
                    success: (data) => {
                        updatePlayerImage(player, data.filename);

                        let obj = JSON.stringify({"type": "player", "action": "updateImage", "playerId": player.id, "playerImage": player.playerImage});
                        ws.send(obj);

                        $('#imageErrorMsg').hide();

                        success = true;
                    },
                    error: (xhr) => {
                        // console.log(xhr.responseText)
                        success = false;
                        $('#imageErrorMsg').show();
                    }
                });
            }

            return success;
        }

        function startGame() {

            if (!sendPlayerImage()) {
                
                return;
            }

            gameStart = true;

            $('#nameWrapper').hide();
            $('#nameWrapperMobile').hide();
            $('#playersWrapper').show();
            $('#chat').show();

            if (isMobile()) {
                mobileZoomCorrectionOnGameStart();
                $('#chat').hide();
                $('#chatMobile').show();
            }
        }

        $('#btnName, #btnNameMobile').on('click', function() {
            let name = isMobile() ? $('#textInputNameMobile').val() : $('#textInputName').val();

            if (setPlayerName(player, name)) {
                let obj = JSON.stringify({"type": "player", "action": "rename", "name": name, "playerImage": player.playerImage});
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

            let chatMessagesDiv = isMobile() ? $('#chatMobile .chat-messages') : $('#chat .chat-messages');
            let textInput = isMobile() ? $('#chatMobile #textInputMobile') : $('#chat #textInput');
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

            if (direction == '') {
                if (e.key == 'Enter') {
                    textInput.focus();
                }

                return;
            }

            triggerMovement(direction);
        });

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }


        // Websocket

        const ws = new WebSocket('wss://aifolou.onrender.com/'); // For production
        // const ws = new WebSocket('ws://localhost:8080'); // For regular local network run
        // const ws = new WebSocket('ws://192.168.5.132:8080'); // For no Docker local network run

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
                    let chatMessagesDiv = isMobile() ? $('#chatMobile .chat-messages') : $('#chat .chat-messages');
                    let textInput = $('#chat #textInput');

                    chatMessagesDiv.append(`<p><b>${msg.playerName}:</b> ${msg.message}</p>`);
                    chatMessagesDiv.scrollTop(chatMessagesDiv.prop("scrollHeight"));

                } else if (msg.action == 'rename') { // PLAYER MUDOU DE NOME
                    let index = getPlayerIndexById(msg.playerId);
                    setPlayerName(players[index], msg.playerName);

                } else if (msg.action == 'updateImage') { // PLAYER ATUALIZOU IMAGEM
                    let index = getPlayerIndexById(msg.playerId);
                    updatePlayerImage(players[index], msg.playerImage);

                } else if (msg.action == 'connect') { // PLAYER SE CONECTOU
                    let newPlayer = {
                        "id": msg.playerId,
                        "name": "P" + msg.playerId,
                        "position": [0, 0]
                    };

                    players.push(newPlayer);
                    $('#playersWrapper').append(`<img id="player${newPlayer.id}" class="player" style="top: ${newPlayer.position[1] * 128}px; left: ${newPlayer.position[0] * 128}px" src="${newPlayer.playerImage ?? 'images/aifolou.jpg'}" alt="i">`);

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
                    $('#playersWrapper').append(`<img id="player${key}" class="player" style="top: ${msg.players[key].position[1] * 128}px; left: ${msg.players[key].position[0] * 128}px" src="${msg.players[key].playerImage ?? 'images/aifolou.jpg'}" alt="i">`);
                }
            }
        });



        // Mobile (experimental)
        
        function isMobile(a = navigator.userAgent||navigator.vendor||window.opera){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {return true} else {return false} };


        function mobileZoomCorrection() {
            var viewport = document.querySelector('meta[name="viewport"]');

            if ( viewport ) {
                viewport.content = "initial-scale=0.1";
                // viewport.content = "width=1200";
            }
        }

        function mobileZoomCorrectionOnGameStart() {
            var viewport = document.querySelector('meta[name="viewport"]');

            if ( viewport ) {
                viewport.content = "initial-scale=1";
                viewport.content = "width=device-width";
            }
        }

        function mobileBtnCorrection() {
            $('#nameWrapper').hide();
            $('#nameWrapperMobile').show();
        }

        if (isMobile()) {
            $('#mobileControls').show();
            mobileZoomCorrection();
            mobileBtnCorrection();

        } else {
            $('#mobileControls').hide();
        }


        $('#leftBtn').on('click', function() {
            let direction = 'left';
            triggerMovement(direction);
        });

        $('#rightBtn').on('click', function() {
            let direction = 'right';
            triggerMovement(direction);
        });

        $('#upBtn').on('click', function() {
            let direction = 'up';
            triggerMovement(direction);
        });

        $('#downBtn').on('click', function() {
            let direction = 'down';
            triggerMovement(direction);
        });
            
    </script>
</body>
</html>