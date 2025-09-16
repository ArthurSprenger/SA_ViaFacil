<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações | Viafácil</title>
    <link rel="stylesheet" href="../styles/style3.css">
</head>
<body>
    <header class="cabecalho-passageiros">
        <div class="icone-menu-passageiros menu-btn" id="menuBtn">
            <div></div>
            <div></div>
            <div></div>
        </div>
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

    <div class="menu-overlay"></div>
    <nav class="sidebar-menu">
        <ul>
            <li>
                <a href="dashboard.html">
                    <img src="../assets/dashboard.png" alt="Dashboard" class="sidebar-icon dashboard-icon">
                    <span>DASHBOARD</span>
                </a>
            </li>
            <li>
                <a href="conta.html">
                    <img src="../assets/logo usuario menu.png" alt="Conta" class="sidebar-icon conta-icon">
                    <span>CONTA</span>
                </a>
            </li>
            <li>
                <a href="configuracoes.html">
                    <img src="../assets/configurações.png" alt="Configurações" class="sidebar-icon">
                    <span>CONFIGURAÇÕES</span>
                </a>
            </li>
            <li>
                <a href="login.html">
                    <img src="../assets/sair.png" alt="Sair" class="sidebar-icon">
                    <span>SAIR</span>
                </a>
            </li>
        </ul>
    </nav>

    <script src="../scripts/script.js"></script>
</body>
</html>