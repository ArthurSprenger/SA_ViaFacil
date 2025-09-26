<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações | Viafácil</title>
    <link rel="stylesheet" href="../styles/style3.css">
    <link rel="stylesheet" href="../styles/solicitacoes.css">
</head>
<body>
    <header class="cabecalho-passageiros">
                <button id="menuBtn" class="icone-menu-passageiros menu-btn" aria-label="Abrir menu">
                    <div></div>
                    <div></div>
                    <div></div>
                </button>
        <a href="dashboard.html">
            <img src="../assets/logo.PNG" alt="VIAFACIL" class="logo-passageiros">
        </a>
    </header>

    <main class="solicitacoes-container">
        <h1 class="titulo-solicitacoes">SOLICITAÇÕES</h1>
        
        <div class="tabela-container">
            <table class="tabela-solicitacoes">
                <tr>
                    <th>LINHA</th>
                    <th>HORÁRIO</th>
                </tr>
                <tr>
                    <td>LINHA 47 INTERDITADA</td>
                    <td>00:00</td>
                </tr>
                <tr>
                    <td>LINHA 33 EM MANUTENÇÃO</td>
                    <td>00:00</td>
                </tr>
                <tr>
                    <td>LINHA 63 INTERDITADA</td>
                    <td>00:00</td>
                </tr>
                <tr>
                    <td><input type="text" placeholder="Digite aqui"></td>
                    <td><input type="text" placeholder="Digite aqui"></td>
                </tr>
            </table>

            <table class="tabela-solicitacoes">
                <tr>
                    <th>LINHA</th>
                    <th>SITUAÇÃO</th>
                </tr>
                <tr>
                    <td>LINHA 47</td>
                    <td>Trilho danificado</td>
                </tr>
                <tr>
                    <td>LINHA 33</td>
                    <td>Falha no motor de tração</td>
                </tr>
                <tr>
                    <td>LINHA 63</td>
                    <td>Objeto na linha de trem</td>
                </tr>
                <tr>
                    <td><input type="text" placeholder="Digite aqui"></td>
                    <td><input type="text" placeholder="Digite aqui"></td>
                </tr>
            </table>
        </div>

        <button class="botao-enviar">ENVIAR SOLICITAÇÃO</button>
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

    <script src="../scripts/script.js"></script>
</body>
</html>