<?php ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Próximas Viagens - Completo | Viafácil</title>
  <link rel="stylesheet" href="../styles/viagens_completa.css">
</head>
<body>
  <div class="topo-bg">
    <header class="header">
      <button class="menu-btn" aria-label="Abrir menu" id="btnMenuFull">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <a href="dashboard_funcionario.php" aria-label="Dashboard Funcionário">
        <img src="../assets/logo.PNG" alt="Viafácil" class="logo" />
      </a>
    </header>
  </div>
  <nav class="menu-lateral" id="menuLateralFull" aria-hidden="true">
    <ul class="lista-itens">
      <li class="item-menu"><a href="dashboard_funcionario.php"><img src="../assets/dashboard.png" class="icone-item" alt="Dashboard"/><span class="texto-item">DASHBOARD</span></a></li>
      <li class="item-menu"><a href="passageiros.php"><img src="../assets/passageiros.png" class="icone-item" alt="Passageiros"/><span class="texto-item">PASSAGEIROS</span></a></li>
      <li class="item-menu"><a href="trenserotas.php"><img src="../assets/trens.png" class="icone-item" alt="Trens e Rotas"/><span class="texto-item">TRENS E ROTAS</span></a></li>
      <li class="item-menu"><a href="aviso.php"><img src="../assets/aviso.png" class="icone-item" alt="Avisos"/><span class="texto-item">AVISOS</span></a></li>
      <li class="item-menu"><a href="solicitacoes.php"><img src="../assets/solicitacao.png" class="icone-item" alt="Solicitações"/><span class="texto-item">SOLICITAÇÕES</span></a></li>
      <li class="item-menu"><a href="login.php"><img src="../assets/sair.png" class="icone-item" alt="Sair"/><span class="texto-item">SAIR</span></a></li>
    </ul>
  </nav>
  <div class="sobreposicao-menu" id="sobreposicaoFull"></div>

  <div class="conteudo">
    <h1>Próximas Viagens (Completo)</h1>
    <div class="tabela-area">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Horários</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Status</th>
            <th>Telefone do maquinista</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>08:00</td><td>Central</td><td>Jardim</td><td class="status-Embarque">Embarque</td><td>(11) 99999-0001</td></tr>
          <tr><td>09:30</td><td>Jardim</td><td>Vila Nova</td><td class="status-EmRota">Em rota</td><td>(11) 99999-0002</td></tr>
          <tr><td>10:15</td><td>Vila Nova</td><td>Central</td><td class="status-Aguardando">Aguardando</td><td>(11) 99999-0003</td></tr>
          <tr><td>11:00</td><td>Central</td><td>Vila Nova</td><td class="status-Embarque">Embarque</td><td>(11) 99999-0004</td></tr>
          <tr><td>11:45</td><td>Jardim</td><td>Central</td><td class="status-EmRota">Em rota</td><td>(11) 99999-0005</td></tr>
          <tr><td>12:20</td><td>Vila Nova</td><td>Jardim</td><td class="status-Aguardando">Aguardando</td><td>(11) 99999-0006</td></tr>
          <tr><td>13:05</td><td>Central</td><td>Jardim</td><td class="status-EmRota">Em rota</td><td>(11) 99999-0007</td></tr>
          <tr><td>13:50</td><td>Jardim</td><td>Vila Nova</td><td class="status-Aguardando">Aguardando</td><td>(11) 99999-0008</td></tr>
          <tr><td>14:30</td><td>Vila Nova</td><td>Central</td><td class="status-Embarque">Embarque</td><td>(11) 99999-0009</td></tr>
          <tr><td>15:10</td><td>Central</td><td>Jardim</td><td class="status-EmRota">Em rota</td><td>(11) 99999-0010</td></tr>
          <tr><td>16:00</td><td>Jardim</td><td>Vila Nova</td><td class="status-Aguardando">Aguardando</td><td>(11) 99999-0011</td></tr>
        </tbody>
      </table>
    </div>
    <div class="paginacao" role="navigation" aria-label="Paginação da tabela de viagens">
      <button type="button" class="nav-btn" id="prevPage" aria-label="Página anterior" title="Página anterior">&#8592;</button>
      <span id="pageIndicator" aria-live="polite">01</span>
      <button type="button" class="nav-btn" id="nextPage" aria-label="Próxima página" title="Próxima página">&#8594;</button>
    </div>
    </div>
  </div>

  <script>
    (function(){
      const btn = document.getElementById('btnMenuFull');
      const menu = document.getElementById('menuLateralFull');
      const overlay = document.getElementById('sobreposicaoFull');
      function abrir(){ menu.classList.add('ativo'); overlay.classList.add('ativo'); menu.setAttribute('aria-hidden','false'); }
      function fechar(){ menu.classList.remove('ativo'); overlay.classList.remove('ativo'); menu.setAttribute('aria-hidden','true'); }
      btn.addEventListener('click', ()=> menu.classList.contains('ativo') ? fechar() : abrir());
      overlay.addEventListener('click', fechar);
      document.addEventListener('keydown', e=>{ if(e.key==='Escape') fechar(); });
      Array.from(menu.querySelectorAll('a')).forEach(a=> a.addEventListener('click', fechar));
    })();

    
    (function(){
      const tbody = document.querySelector('table tbody');
      if(!tbody) return;
      const linhas = Array.from(tbody.querySelectorAll('tr'));
      let porPagina = 6; // valor inicial; será recalculado
      let paginaAtual = 1;
      const prev = document.getElementById('prevPage');
      const next = document.getElementById('nextPage');
      const indicador = document.getElementById('pageIndicator');
      const pagBar = document.querySelector('.paginacao');

      function calcularPorPagina(){
        if(linhas.length === 0) return 1;
        // Exibe todas temporariamente para medições
        linhas.forEach(tr=> tr.style.display='table-row');
        const tableEl = tbody.parentElement; // table
        const thead = tableEl.querySelector('thead');
        const headH = thead ? thead.getBoundingClientRect().height : 0;
        const topTabela = tableEl.getBoundingClientRect().top;
        const pagH = pagBar ? pagBar.getBoundingClientRect().height : 0;
        const margemExtra = 16; // folga inferior
        const disponivel = window.innerHeight - topTabela - pagH - margemExtra;
        const primeiraLinha = linhas[0];
        const rowH = primeiraLinha.getBoundingClientRect().height || 28;
        const cabecalhoESobra = headH + 4; // pequena folga
        const calculado = Math.floor((disponivel - cabecalhoESobra) / rowH);
        return Math.max(1, Math.min(calculado, linhas.length));
      }

      function totalPaginas(){
        return Math.ceil(linhas.length / porPagina) || 1;
      }

      function render(){
        const total = totalPaginas();
        if(paginaAtual > total) paginaAtual = total;
        linhas.forEach((tr, i)=>{
          const p = Math.floor(i / porPagina) + 1;
          tr.style.display = (p === paginaAtual) ? 'table-row' : 'none';
        });
        indicador.textContent = String(paginaAtual).padStart(2,'0');
        prev.disabled = paginaAtual === 1;
        next.disabled = paginaAtual === total;
      }

      function recalcular(){
        const novo = calcularPorPagina();
        if(novo !== porPagina){
          porPagina = novo;
          paginaAtual = 1; // volta para primeira
        }
        render();
      }

      if(prev && next){
        prev.addEventListener('click', ()=>{ if(paginaAtual>1){ paginaAtual--; render(); } });
        next.addEventListener('click', ()=>{ if(paginaAtual< totalPaginas()){ paginaAtual++; render(); } });
      }

      window.addEventListener('resize', ()=>{ clearTimeout(window.__pgTO); window.__pgTO = setTimeout(recalcular,150); });

      // inicial
      recalcular();
    })();
  </script>
</body>
</html>