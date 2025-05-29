document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('recuperar-form');
  var email = document.getElementById('recuperar-email');
  form.addEventListener('submit', function(e) {
    if (!email.value.trim()) {
      e.preventDefault();
      alert('Por favor, preencha o e-mail.');
      email.focus();
      return false;
    }
    // Validação básica de e-mail
    var re = /^\S+@\S+\.\S+$/;
    if (!re.test(email.value.trim())) {
      e.preventDefault();
      alert('Digite um e-mail válido.');
      email.focus();
      return false;
    }
    alert('Se o e-mail informado estiver cadastrado, você receberá as instruções em breve.');
  });
});