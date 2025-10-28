<?php
require_once __DIR__ . '/../includes/db_connect.php';

if(isset($_GET['email']) && isset($_GET['nova_senha'])){
    $email = $_GET['email'];
    $novaSenha = $_GET['nova_senha'];
    
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
    $resultado = $stmt->execute([$senhaHash, $email]);
    
    if($resultado){
        echo "<h2>✓ Senha atualizada com sucesso!</h2>";
        echo "<p>Email: <strong>{$email}</strong></p>";
        echo "<p>Nova senha: <strong>{$novaSenha}</strong></p>";
        echo "<p><a href='login.php'>Fazer login agora</a></p>";
    } else {
        echo "<h2>✗ Erro ao atualizar senha</h2>";
    }
} else {
    echo "<h2>Resetar Senha de Usuário</h2>";
    echo "<form method='GET'>";
    echo "<p><input type='email' name='email' placeholder='Email do usuário' required style='padding:10px;width:300px;'/></p>";
    echo "<p><input type='text' name='nova_senha' placeholder='Nova senha' required style='padding:10px;width:300px;'/></p>";
    echo "<p><button type='submit' style='padding:10px 20px;background:#003366;color:#fff;border:none;cursor:pointer;'>Resetar Senha</button></p>";
    echo "</form>";
    
    echo "<hr>";
    echo "<h3>Usuários Aprovados:</h3>";
    $stmt = $pdo->query("SELECT id, nome, email, status FROM usuarios WHERE status = 'aprovado' ORDER BY criado_em DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach($usuarios as $u){
        echo "<li><strong>{$u['nome']}</strong> - {$u['email']} 
        <a href='?email={$u['email']}&nova_senha=senha123' style='color:blue;'>[Resetar para: senha123]</a></li>";
    }
    echo "</ul>";
}
?>
