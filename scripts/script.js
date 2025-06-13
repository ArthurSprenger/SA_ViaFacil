document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.querySelector('.menu-btn');
  const sidebar = document.querySelector('.sidebar-menu');
  const overlay = document.querySelector('.menu-overlay');

  // Abre/fecha o menu ao clicar no botão
  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      document.body.classList.toggle('menu-open');
    });
  }

  // Fecha o menu ao clicar fora dele
  document.addEventListener('click', (e) => {
    if (
      document.body.classList.contains('menu-open') &&
      !sidebar.contains(e.target) &&
      !menuBtn.contains(e.target)
    ) {
      document.body.classList.remove('menu-open');
    }
  });

  if (document.querySelector('.spinner')) {
    setTimeout(() => window.location.href = '../public/Login.html', 3000);
  }
  
  // Validação do formulário de aviso
  const avisoForm = document.getElementById('aviso-form');
  if (avisoForm) {
    avisoForm.addEventListener('submit', (e) => {
      const avisoInput = document.getElementById('aviso-input');
      if (!avisoInput.value.trim()) {
        // e.preventDefault();
        alert('Por favor, preencha o campo de aviso.');
        avisoInput.focus();
        return false;
      }
    });
  }

  // Validação do formulário de solicitação
  const solicitacaoForm = document.getElementById('solicitacao-form');
  if (solicitacaoForm) {
    solicitacaoForm.addEventListener('submit', (e) => {
      const solicitacaoInput = document.getElementById('solicitacao-input');
      if (!solicitacaoInput.value.trim()) {
        e.preventDefault();
        alert('Por favor, preencha o campo de solicitação.');
        solicitacaoInput.focus();
        return false;
      }
    });
  }

  ['passageiros','trens-rotas','avisos','solicitacoes'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => window.location.href='#');
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const botaoMenu = document.querySelector('.botao-menu');
  if (botaoMenu) {
    botaoMenu.addEventListener('click', () => document.body.classList.toggle('menu-open'));
  }

  // Validação do formulário de aviso
  const formularioAviso = document.getElementById('formulario-aviso');
  if (formularioAviso) {
    formularioAviso.addEventListener('submit', (e) => {
      const inputAviso = document.getElementById('input-aviso');
      if (!inputAviso.value.trim()) {
        e.preventDefault();
        alert('Por favor, preencha o campo de aviso.');
        inputAviso.focus();
        return false;
      }
      alert(`Aviso enviado: ${inputAviso.value.trim()}`);
      inputAviso.value = '';
    });
  }

  // Validação do formulário de solicitação
  const formularioSolicitacao = document.getElementById('formulario-solicitacao');
  if (formularioSolicitacao) {
    formularioSolicitacao.addEventListener('submit', (e) => {
      const inputSolicitacao = document.getElementById('input-solicitacao');
      if (!inputSolicitacao.value.trim()) {
        e.preventDefault();
        alert('Por favor, preencha o campo de solicitação.');
        inputSolicitacao.focus();
        return false;
      }
      alert(`Solicitação enviada: ${inputSolicitacao.value.trim()}`);
      inputSolicitacao.value = '';
    });
  }

  // Navegação dos cartões
  ['passageiros','trens','aviso','solicitacao'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => window.location.href='#');
  });

  // Tabela de Solicitações
  const tabelaSolicitacoes = document.getElementById('tabela-solicitacoes');
  const formAddSolicitacao = document.getElementById('form-add-solicitacao');

  if (tabelaSolicitacoes && formAddSolicitacao) {
    formAddSolicitacao.addEventListener('submit', (e) => {
      e.preventDefault();
      const estacaoInput = document.getElementById('nova-estacao');
      const horarioInput = document.getElementById('novo-horario');
      const situacaoInput = document.getElementById('nova-situacao');

      if (!estacaoInput.value.trim() || !horarioInput.value.trim() || !situacaoInput.value.trim()) {
        return alert('Por favor, preencha todos os campos.');
      }

      const newRow = tabelaSolicitacoes.insertRow(-1);
      newRow.innerHTML = `
        <td>${estacaoInput.value.trim()}</td>
        <td>${horarioInput.value.trim()}</td>
        <td>${situacaoInput.value.trim()}</td>
        <td>
          <button class="btn-editar">Editar</button>
          <button class="btn-excluir">Excluir</button>
        </td>
      `;

      estacaoInput.value = '';
      horarioInput.value = '';
      situacaoInput.value = '';

      // Adiciona eventos para os botões de editar e excluir
      newRow.querySelector('.btn-editar').addEventListener('click', () => {
        estacaoInput.value = newRow.cells[0].innerText;
        horarioInput.value = newRow.cells[1].innerText;
        situacaoInput.value = newRow.cells[2].innerText;
        tabelaSolicitacoes.deleteRow(newRow.rowIndex - 1);
      });

      newRow.querySelector('.btn-excluir').addEventListener('click', () => {
        tabelaSolicitacoes.deleteRow(newRow.rowIndex - 1);
      });
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const tabela = document.getElementById('tabela-solicitacoes');
  const form = document.getElementById('form-add-solicitacao');
  const estacao = document.getElementById('nova-estacao');
  const horario = document.getElementById('novo-horario');
  const situacao = document.getElementById('nova-situacao');

  if (form && tabela) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      if (!estacao.value.trim() || !horario.value.trim() || !situacao.value.trim()) {
        alert('Por favor, preencha todos os campos.');
        return;
      }
      adicionarLinha(estacao.value, horario.value, situacao.value);
      estacao.value = '';
      horario.value = '';
      situacao.value = '';
    });
  }

  function adicionarLinha(est, hor, sit) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td contenteditable="true">${est}</td>
      <td contenteditable="true">${hor}</td>
      <td contenteditable="true">${sit}</td>
      <td>
        <button class="btn-acoes btn-remover" type="button">Remover</button>
      </td>
    `;
    tr.querySelector('.btn-remover').onclick = function() {
      tr.remove();
    };
    tabela.appendChild(tr);
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const menuBtn = document.querySelector('.menu-btn');
  const sidebar = document.querySelector('.sidebar-menu');
  const overlay = document.querySelector('.menu-overlay');

  if (menuBtn && sidebar && overlay) {
    menuBtn.addEventListener('click', function() {
      sidebar.classList.toggle('ativo');
      overlay.classList.toggle('ativo');
    });
    overlay.addEventListener('click', function() {
      sidebar.classList.remove('ativo');
      overlay.classList.remove('ativo');
    });
    // Fecha o menu ao clicar em um link
    sidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function() {
        sidebar.classList.remove('ativo');
        overlay.classList.remove('ativo');
      });
    });
  }
});