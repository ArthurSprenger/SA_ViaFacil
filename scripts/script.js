document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.querySelector('.menu-btn');
  menuBtn.addEventListener('click', () => document.body.classList.toggle('menu-open'));

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
});