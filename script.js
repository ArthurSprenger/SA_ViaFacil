document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.querySelector('.menu-btn');
  menuBtn.addEventListener('click', () => document.body.classList.toggle('menu-open'));

  if (document.querySelector('.spinner')) {
    setTimeout(() => window.location.href = 'login.html', 3000);
  }

  const avisoForm = document.getElementById('aviso-form');
  if (avisoForm) {
    avisoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const txt = document.getElementById('aviso-input').value.trim();
      if (txt) { alert(`Aviso enviado: ${txt}`); avisoForm.reset(); }
    });
  }

  ['passageiros','trens-rotas','avisos','solicitacoes'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => window.location.href='#');
  });
});

document.addEventListener('DOMContentLoaded', () => {
  // Menu button functionality
  const menuBtn = document.querySelector('.menu-btn');
  menuBtn.addEventListener('click', () => {
    document.body.classList.toggle('menu-open');
  });

  // Form submission handling
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

  // Solicitação form handling
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

  // Card click handlers
  const cards = document.querySelectorAll('.card');
  cards.forEach(card => {
    card.addEventListener('click', () => {
      const section = card.id;
      console.log(`Navegando para seção: ${section}`);
      // Implement navigation logic here
    });
  });
});