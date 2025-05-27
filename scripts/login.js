document.getElementById('botao-entrar').onclick = function() {
  var inputs = document.querySelectorAll('.login-form input');
  var usuario = inputs[0].value.trim();
  var senha = inputs[1].value.trim();
  if (!usuario) {
    alert('Por favor, preencha o usuário.');
    inputs[0].focus();
    return false;
  }
  if (!senha) {
    alert('Por favor, preencha a senha.');
    inputs[1].focus();
    return false;
  }
  window.location.href = "dashboard.html";
}