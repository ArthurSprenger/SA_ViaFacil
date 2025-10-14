<?php
  <link rel="stylesheet" href="../styles/redefinirsenha.css">
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .formulario-login button:hover {
      background: #0056b3;
    }
    .suporte-link {
      margin-top: 16px;
      font-size: 0.95em;
    }
    .suporte-link a {
      color: #007bff;
      text-decoration: none;
    }
    .suporte-link a:hover {
      text-decoration: underline;
    }
    @media (max-width: 600px) {
      .forgot-container {
        padding: 18px 8px;
        font-size: 1em;
      }
      .logo-login {
        max-width: 80px;
      }
      .avatar-usuario img {
        max-width: 50px;
      }
    }
  </style>
</head>
<body>
  <div class="forgot-container">
    <img src="../assets/logo.PNG" alt="Viafacil" class="logo-login">
    <h1 class="titulo-redefinir">RECUPERE SUA SENHA</h1>
    <div class="avatar-usuario">
      <img src="../assets/logo usuario.png" alt="Usuário">
    </div>
    <div class="form-box">
      <form id="form-redefinir" class="formulario-login">
        <input type="password" placeholder="digite sua nova senha" required>
        <input type="password" placeholder="confirme sua nova senha" required>
        <button type="submit">Redefinir Senha</button>
      </form>
      <div class="suporte-link">
        <a href="suporte.php">
          <span class="info-icon">&#9432;</span>
          Entre em contato com o Suporte Técnico.
        </a>
      </div>
    </div>
  </div>
</body>
</html>