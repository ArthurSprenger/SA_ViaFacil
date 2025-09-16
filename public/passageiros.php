<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Passageiros</title>
    <link rel="stylesheet" href="../styles/style3.css">
</head>
<body>
    <!-- Exemplo de uso das novas classes -->
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
    <h1 class="titulo-passageiros">PASSAGEIROS</h1>
    <!-- Substitua a tabela por esta estrutura -->
    <main>
      <div class="lista-passageiros">
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Amanada teixeira</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">48</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">12:14</span>
          </div>
        </div>
        <!-- Repita para cada passageiro -->
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Pedro Murf</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:30</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Arthur Itinga</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">12:39</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Ewerton Oliveira</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">62</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">20:37</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Luiz Correa</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">78</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:20</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Icaro Botelho</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">24</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">14:30</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Luiza bohn</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">38</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">21:45</span>
          </div>
        </div>
        <div class="card-passageiro">
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Nome:</span>
            <span class="valor-passageiro">Bruno Jason</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Rota:</span>
            <span class="valor-passageiro">27</span>
          </div>
          <div class="campo-passageiro">
            <span class="rotulo-passageiro">Horário:</span>
            <span class="valor-passageiro">23:50</span>
          </div>
        </div>
      </div>
    </main>
    <div class="menu-overlay"></div>
    <!-- Menu lateral -->
    <nav class="sidebar-menu">
      <ul>
        <li>
          <a href="dashboard.html">
            <img src="../assets/dashboard.png" alt="Dashboard" class="sidebar-icon dashboard-icon" />
            <span>DASHBOARD</span>
          </a>
        </li>
        <li>
          <a href="conta.html">
            <img src="../assets/logo usuario menu.png" alt="Conta" class="sidebar-icon conta-icon" />
            <span>CONTA</span>
          </a>
        </li>
        <li>
          <a href="#">
            <img src="../assets/configurações.png" alt="Configurações" class="sidebar-icon" />
            <span>CONFIGURAÇÕES</span>
          </a>
        </li>
        <li>
          <a href="login.html">
            <img src="../assets/sair.png" alt="Sair" class="sidebar-icon" />
            <span>SAIR</span>
          </a>
        </li>
      </ul>
    </nav>
    <footer class="rodape-passageiros">
        <button class="seta-passageiros" id="prevBtn">&#8592;</button>
        <span class="numero-pagina-passageiros">01</span>
        <button class="seta-passageiros" id="nextBtn">&#8594;</button>
    </footer>
    <script src="../scripts/passageiros.js"></script>
    <script src="../scripts/script.js"></script>
</body>
</html>