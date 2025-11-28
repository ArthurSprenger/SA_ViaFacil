<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
  header('Location: dashboard.php');
  exit;
}

$menuLinks = [
  [
    'href' => 'dashboard_funcionario.php',
    'icon' => 'dashboard.png',
    'alt' => 'Dashboard',
    'label' => 'Dashboard',
    'is_active' => false,
  ],
  [
    'href' => 'conta.php',
    'icon' => 'logo usuario menu.png',
    'alt' => 'Conta',
    'label' => 'Conta',
    'is_active' => false,
  ],
  [
    'href' => 'passageiros_funcionario.php',
    'icon' => 'passageiros.png',
    'alt' => 'Passageiros',
    'label' => 'Passageiros',
    'is_active' => true,
  ],
  [
    'href' => 'trenserotas.php',
    'icon' => 'trens.png',
    'alt' => 'Trens e Rotas',
    'label' => 'Trens e Rotas',
    'is_active' => false,
  ],
  [
    'href' => 'aviso_funcionario.php',
    'icon' => 'aviso.png',
    'alt' => 'Avisos',
    'label' => 'Aviso',
    'is_active' => false,
  ],
  [
    'href' => 'solicitacoes_funcionario.php',
    'icon' => 'solicitacao.png',
    'alt' => 'Solicitação',
    'label' => 'Solicitação',
    'is_active' => false,
  ],
  [
    'href' => 'configs.php',
    'icon' => 'configurações.png',
    'alt' => 'Configurações',
    'label' => 'Configurações',
    'is_active' => false,
  ],
  [
    'href' => 'logout.php',
    'icon' => 'sair.png',
    'alt' => 'Sair',
    'label' => 'Sair',
    'is_active' => false,
  ],
];

$passageiros = [
  [
    'id' => '001',
    'status' => 'Ativo',
    'status_class' => 'status-ativo',
    'nome' => 'João Silva Santos',
    'cpf' => '123.456.789-00',
    'email' => 'joao.silva@email.com',
    'telefone' => '(11) 98765-4321',
    'viagem' => 'Central → Jardim',
    'data' => '13/10/2025 às 14:30',
  ],
  [
    'id' => '002',
    'status' => 'Ativo',
    'status_class' => 'status-ativo',
    'nome' => 'Maria Oliveira Costa',
    'cpf' => '987.654.321-00',
    'email' => 'maria.oliveira@email.com',
    'telefone' => '(11) 91234-5678',
    'viagem' => 'Vila Nova → Central',
    'data' => '13/10/2025 às 16:45',
  ],
  [
    'id' => '003',
    'status' => 'Inativo',
    'status_class' => 'status-inativo',
    'nome' => 'Carlos Eduardo Pereira',
    'cpf' => '456.789.123-00',
    'email' => 'carlos.pereira@email.com',
    'telefone' => '(11) 99876-5432',
    'viagem' => 'Jardim → Vila Nova',
    'data' => '10/10/2025 às 09:15',
  ],
  [
    'id' => '004',
    'status' => 'Ativo',
    'status_class' => 'status-ativo',
    'nome' => 'Ana Paula Rodrigues',
    'cpf' => '321.654.987-00',
    'email' => 'ana.rodrigues@email.com',
    'telefone' => '(11) 97654-3210',
    'viagem' => 'Central → Vila Nova',
    'data' => '14/10/2025 às 08:00',
  ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Passageiros | Viafácil</title>
  <link rel="stylesheet" href="../styles/funcionario_pages.css" />
</head>
<body>
  <div class="page-wrapper">
    <header class="header-func" role="banner">
      <button class="menu-btn" aria-label="Abrir menu lateral">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <a href="dashboard_funcionario.php" class="logo-link">
        <img src="../assets/logo.PNG" alt="Viafacil" class="logo" />
      </a>
    </header>

    <nav class="menu-lateral" id="menuLateral" aria-label="Menu principal">
      <ul class="lista-itens">
        <?php foreach ($menuLinks as $link): ?>
          <li class="item-menu<?php echo $link['is_active'] ? ' ativo' : ''; ?>">
            <a
              href="<?php echo htmlspecialchars($link['href']); ?>"
              <?php echo $link['is_active'] ? 'aria-current="page"' : ''; ?>
            >
              <img
                src="../assets/<?php echo htmlspecialchars($link['icon']); ?>"
                class="icone-item"
                alt="<?php echo htmlspecialchars($link['alt']); ?>"
              />
              <span class="texto-item"><?php echo strtoupper(htmlspecialchars($link['label'])); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="sobreposicao-menu" id="sobreposicaoMenu" aria-hidden="true"></div>

    <main class="conteudo-principal" role="main">
      <section class="cabecalho-pagina">
        <p class="subtitulo">Área do colaborador</p>
        <h1 class="titulo-pagina">Passageiros</h1>
        <p class="descricao-pagina">
          Consulte rapidamente o status de passageiros, últimas viagens e dados de contato.
        </p>
      </section>

      <section class="secao-busca">
        <input
          type="text"
          id="buscarPassageiro"
          class="input-busca"
          placeholder="Buscar passageiro por nome, CPF ou ID..."
          autocomplete="off"
        />
      </section>

      <section class="secao-conteudo" aria-live="polite">
        <?php foreach ($passageiros as $passageiro): ?>
          <article
            class="card-passageiro"
            data-card-text="<?php echo htmlspecialchars($passageiro['id'] . ' ' . $passageiro['nome'] . ' ' . $passageiro['cpf'] . ' ' . $passageiro['email']); ?>"
          >
            <header class="passageiro-header">
              <div class="passageiro-id">#<?php echo htmlspecialchars($passageiro['id']); ?></div>
              <div class="passageiro-status <?php echo htmlspecialchars($passageiro['status_class']); ?>">
                <?php echo htmlspecialchars($passageiro['status']); ?>
              </div>
            </header>

            <div class="passageiro-info">
              <h3><?php echo htmlspecialchars($passageiro['nome']); ?></h3>
              <p><strong>CPF:</strong> <?php echo htmlspecialchars($passageiro['cpf']); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($passageiro['email']); ?></p>
              <p><strong>Telefone:</strong> <?php echo htmlspecialchars($passageiro['telefone']); ?></p>
            </div>

            <div class="passageiro-viagem">
              <p><strong>Última Viagem:</strong> <?php echo htmlspecialchars($passageiro['viagem']); ?></p>
              <p><strong>Data:</strong> <?php echo htmlspecialchars($passageiro['data']); ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    </main>
  </div>

  <script>
    (() => {
      const botaoMenu = document.querySelector('.menu-btn');
      const menuLateral = document.getElementById('menuLateral');
      const sobreposicao = document.getElementById('sobreposicaoMenu');
      const inputBusca = document.getElementById('buscarPassageiro');
      const cards = Array.from(document.querySelectorAll('[data-card-text]'));

      if (botaoMenu && menuLateral && sobreposicao) {
        const abrirMenu = () => {
          menuLateral.classList.add('ativo');
          sobreposicao.classList.add('ativo');
        };

        const fecharMenu = () => {
          menuLateral.classList.remove('ativo');
          sobreposicao.classList.remove('ativo');
        };

        botaoMenu.addEventListener('click', () => {
          if (menuLateral.classList.contains('ativo')) {
            fecharMenu();
          } else {
            abrirMenu();
          }
        });

        sobreposicao.addEventListener('click', fecharMenu);
        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            fecharMenu();
          }
        });

        menuLateral.querySelectorAll('a').forEach((link) => {
          link.addEventListener('click', fecharMenu);
        });
      }

      if (inputBusca && cards.length) {
        inputBusca.addEventListener('input', (event) => {
          const termo = event.target.value.trim().toLowerCase();

          cards.forEach((card) => {
            const texto = (card.dataset.cardText || '').toLowerCase();
            card.style.display = termo === '' || texto.includes(termo) ? 'block' : 'none';
          });
        });
      }
    })();
  </script>
</body>
</html>
