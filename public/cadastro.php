<?php

session_start();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../src/User.php';

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = trim($_POST['uf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    if (!$nome || !$cep || !$email || !$senha) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        $userRepo = new User($pdo);
        
        $usuarioExistente = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $usuarioExistente->execute([$email]);
        
        if ($usuarioExistente->fetch()) {
            $erro = "Este email já está cadastrado.";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'normal', 'pendente')");
                $stmt->execute([$nome, $email, $senhaHash, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf]);
                $sucesso = "Cadastro realizado! Aguarde a aprovação do administrador para fazer login.";
                header("refresh:3;url=login.php");
            } catch (Exception $e) {
                $erro = "Erro ao realizar cadastro. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Viafacil - Cadastro</title>
  <link rel="stylesheet" href="../styles/login.css" />
</head>
<body>
  <div class="page-center">
    <div class="login-card" role="main">
      <img src="../assets/logo.PNG" alt="Viafacil" class="login-logo">
      <div class="avatar-holder">
        <img src="../assets/logo usuario.png" alt="Usuário">
      </div>

      <form class="form-login" method="POST" action="">
        <input class="input-pill" type="text" name="nome" placeholder="nome completo" required>
        <input class="input-pill" type="email" name="email" placeholder="email" required>
        <input class="input-pill" type="password" name="senha" placeholder="senha" required>
        
        <input class="input-pill" type="text" name="cep" id="cep" placeholder="CEP" maxlength="9" required>
        
        <div id="camposEndereco" style="display: none;">
          <input class="input-pill" type="text" name="logradouro" id="logradouro" placeholder="logradouro" readonly>
          <input class="input-pill" type="text" name="numero" id="numero" placeholder="número">
          <input class="input-pill" type="text" name="complemento" id="complemento" placeholder="complemento">
          <input class="input-pill" type="text" name="bairro" id="bairro" placeholder="bairro" readonly>
          <input class="input-pill" type="text" name="cidade" id="cidade" placeholder="cidade" readonly>
          <input class="input-pill" type="text" name="uf" id="uf" placeholder="UF" maxlength="2" readonly>
        </div>
        
        <button class="btn-entrar" type="submit">CADASTRAR</button>
        
        <?php if ($erro) { echo '<div class="erro-login">'.htmlspecialchars($erro).'</div>'; } ?>
        <?php if ($sucesso) { echo '<div class="sucesso-login">'.htmlspecialchars($sucesso).'</div>'; } ?>
      </form>

      <div class="suporte link-cadastro">
        <a href="login.php">Já tem conta? Faça login</a>
      </div>
      
      <div class="suporte"><a href="suporte.php">&#9432;&nbsp;Entre em contato com o Suporte Técnico.</a></div>
    </div>
  </div>

  <script>
    const cepInput = document.getElementById('cep');
    const logradouroInput = document.getElementById('logradouro');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const ufInput = document.getElementById('uf');
    const numeroInput = document.getElementById('numero');
    const camposEndereco = document.getElementById('camposEndereco');

    cepInput.addEventListener('blur', function() {
      const cep = this.value.replace(/\D/g, '');
      
      if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
          .then(response => response.json())
          .then(data => {
            if (!data.erro) {
              logradouroInput.value = data.logradouro || '';
              bairroInput.value = data.bairro || '';
              cidadeInput.value = data.localidade || '';
              ufInput.value = data.uf || '';
              camposEndereco.style.display = 'block';
              numeroInput.focus();
            } else {
              alert('CEP não encontrado!');
              limparCampos();
            }
          })
          .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
            limparCampos();
          });
      }
    });

    cepInput.addEventListener('input', function() {
      let valor = this.value.replace(/\D/g, '');
      if (valor.length > 5) {
        valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
      }
      this.value = valor;
    });

    function limparCampos() {
      logradouroInput.value = '';
      bairroInput.value = '';
      cidadeInput.value = '';
      ufInput.value = '';
      camposEndereco.style.display = 'none';
    }
  </script>
</body>
</html>