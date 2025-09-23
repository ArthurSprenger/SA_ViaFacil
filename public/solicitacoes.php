<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações | Viafácil</title>
    <link rel="stylesheet" href="../styles/style3.css">
        <style>
            /* local menu-lateral + input sizing for solicitações */
            .menu-lateral { position: fixed; left: 0; top: 0; height: 100vh; width: 260px; background: #2f2f2f; color: #fff; padding-top: 28px; box-shadow: 2px 0 12px rgba(0,0,0,0.3); transform: translateX(-110%); transition: transform 0.28s ease; z-index: 1100; }
            .menu-lateral.ativo { transform: translateX(0); }
            .sobreposicao-menu { position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: opacity .2s ease; z-index: 1090; }
            .sobreposicao-menu.ativo { opacity: 1; visibility: visible; }
            .lista-itens { list-style: none; padding: 0 12px; margin: 0; }
            .item-menu { display:flex; align-items:center; gap:12px; padding:14px 8px; border-radius:8px; color:#fff; cursor:pointer; margin-bottom:8px; }
            .item-menu:hover { background: rgba(255,255,255,0.04); }
            .item-menu a { color: inherit; text-decoration: none; display:flex; align-items:center; gap:12px; width:100%; }
            .icone-item { width:36px; height:36px; display:block; }
            .texto-item { font-weight:700; font-size:0.95em; }

            .tabela-solicitacoes input[type="text"] {
                width: 100%;
                box-sizing: border-box;
                padding: 8px 10px;
                border-radius: 6px;
                border: 1px solid #ccc;
                font-size: 0.95em;
                display: block;
                background: #fff;
            }
            .tabela-container { display: grid; gap: 18px; }
            .menu-btn { background: transparent !important; border: 0 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; justify-content: center !important; align-items: center !important; width: 36px !important; height: 36px !important; cursor: pointer; }
            .menu-btn div { width: 28px !important; height: 4px !important; background: #fff !important; border-radius: 2px !important; margin: 2px 0 !important; display: block !important; }
        </style>
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

    <!-- slide-in menu lateral -->
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