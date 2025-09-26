<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trens e Rotas</title>
    <link rel="stylesheet" href="../styles/style2.css">
    <link rel="stylesheet" href="../styles/trenserotas.css">
</head>
<body>
    <header class="cabecalho-trens">
                <button id="menuBtn" class="menu-btn" aria-label="Abrir menu">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                </button>
                <a href="dashboard.php">
          <img src="../assets/logo.PNG" alt="Viafácil" class="logo-trens" />
        </a>
    </header>

    <main class="conteudo-trens">
        <h1 class="titulo-trens">Trens e Rotas</h1>
        <div class="bloco-mapa">
            <span class="subtitulo-mapa">Estações atuando agora na região</span>
            <img src="../assets/imagemmapa.png" alt="Mapa das estações" class="imagem-mapa" />
        </div>
        <div class="lista-estacoes">
            <div class="linha-estacao">
                <span class="cor-estacao cor-preto"></span>
                <span class="nome-estacao">Estação 1</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-roxo"></span>
                <span class="nome-estacao">Estação 2</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-verde"></span>
                <span class="nome-estacao">Estação 3</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-vermelho"></span>
                <span class="nome-estacao">Estação 4</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-azul"></span>
                <span class="nome-estacao">Estação 5</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-laranja"></span>
                <span class="nome-estacao">Estação 6</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
            <div class="linha-estacao">
                <span class="cor-estacao cor-rosa"></span>
                <span class="nome-estacao">Estação 7</span>
                <a href="#" class="link-horario">Quadro de horários</a>
            </div>
        </div>
        <div class="paginacao-trens">
            <span class="seta-paginacao">&#8592;</span>
            <span class="numero-pagina">01</span>
            <span class="seta-paginacao">&#8594;</span>
        </div>
    </main>
        <nav class="menu-lateral" id="menuLateral" aria-hidden="true">
            <ul class="lista-itens">
                <li class="item-menu"><a href="dashboard.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
                <li class="item-menu"><a href="conta.php"><img src="../assets/logo usuario menu.png" class="icone-item" alt="Conta"/><span class="texto-item">CONTA</span></a></li>
                <li class="item-menu"><a href="configs.php"><img src="../assets/configurações.png" class="icone-item" alt="Configurações"/><span class="texto-item">CONFIGURAÇÕES</span></a></li>
                <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
            </ul>
        </nav>
        <div class="sobreposicao-menu" id="sobreposicaoMenu"></div>

        <script>
            (function(){
                const botaoMenu = document.querySelector('.menu-btn');
                const menuLateral = document.getElementById('menuLateral');
                const sobreposicao = document.getElementById('sobreposicaoMenu');
                if(!menuLateral || !sobreposicao) return;

                function abrirMenu(){ menuLateral.classList.add('ativo'); sobreposicao.classList.add('ativo'); menuLateral.setAttribute('aria-hidden','false'); }
                function fecharMenu(){ menuLateral.classList.remove('ativo'); sobreposicao.classList.remove('ativo'); menuLateral.setAttribute('aria-hidden','true'); }

                if(botaoMenu) botaoMenu.addEventListener('click', function(){ if(menuLateral.classList.contains('ativo')) fecharMenu(); else abrirMenu(); });
                sobreposicao.addEventListener('click', fecharMenu);
                document.addEventListener('keydown', function(e){ if(e.key === 'Escape') fecharMenu(); });
                Array.from(menuLateral.querySelectorAll('a')).forEach(function(link){ link.addEventListener('click', function(){ fecharMenu(); }); });
            })();
        </script>
</body>
</html>