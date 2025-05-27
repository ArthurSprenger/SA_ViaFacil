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
        e.preventDefault();
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
  const menuBtn = document.querySelector('.menu-btn');
  menuBtn.addEventListener('click', () => {
    document.body.classList.toggle('menu-open');
  });

  const avisoForm = document.getElementById('aviso-form');
  if (avisoForm) {
    avisoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const avisoInput = document.getElementById('aviso-input');
      const aviso = avisoInput.value.trim();
      
      if (aviso) {
        alert(`Aviso enviado: ${aviso}`);
        avisoInput.value = '';
      }
    });
  }

  const solicitacaoForm = document.getElementById('solicitacao-form');
  if (solicitacaoForm) {
    solicitacaoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const solicitacaoInput = document.getElementById('solicitacao-input');
      const solicitacao = solicitacaoInput.value.trim();
      
      if (solicitacao) {
        alert(`Solicitação enviada: ${solicitacao}`);
        solicitacaoInput.value = '';
      }
    });
  }

  const cards = document.querySelectorAll('.card');
  cards.forEach(card => {
    card.addEventListener('click', () => {
      const section = card.id;
      console.log(`Navegando para seção: ${section}`);
    });
  });
});