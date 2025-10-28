<?php
require_once __DIR__ . '/../includes/db_connect.php';

echo "<h2>Teste de Login - Debug</h2>";

if(isset($_GET['email'])){
    $email = $_GET['email'];
    
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo, status FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user){
        echo "<h3>Usuário encontrado:</h3>";
        echo "<pre>";
        echo "ID: " . $user['id'] . "\n";
        echo "Nome: " . $user['nome'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Tipo: " . $user['tipo'] . "\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Hash da senha: " . substr($user['senha'], 0, 50) . "...\n";
        echo "Tipo de hash: ";
        if(strpos($user['senha'], '$2y$') === 0){
            echo "password_hash (correto)";
        } else if(strlen($user['senha']) == 32){
            echo "MD5 (antigo)";
        } else {
            echo "desconhecido";
        }
        echo "\n</pre>";
        
        if(isset($_GET['senha'])){
            $senha = $_GET['senha'];
            echo "<h3>Teste de senha:</h3>";
            
            if(strpos($user['senha'], '$2y$') === 0){
                $resultado = password_verify($senha, $user['senha']);
                echo "password_verify: " . ($resultado ? "✓ CORRETA" : "✗ INCORRETA");
            } else {
                $resultado = (md5($senha) === $user['senha']);
                echo "md5: " . ($resultado ? "✓ CORRETA" : "✗ INCORRETA");
            }
        } else {
            echo "<p>Adicione &senha=suasenha na URL para testar</p>";
        }
    } else {
        echo "<p style='color:red'>Usuário não encontrado!</p>";
    }
} else {
    echo "<p>Use: teste_login.php?email=seuemail@exemplo.com&senha=suasenha</p>";
    
    echo "<h3>Listar todos os usuários:</h3>";
    $stmt = $pdo->query("SELECT id, nome, email, tipo, status, LEFT(senha, 15) as senha_inicio FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Status</th><th>Hash</th></tr>";
    foreach($usuarios as $u){
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['nome']}</td>";
        echo "<td><a href='?email={$u['email']}'>{$u['email']}</a></td>";
        echo "<td>{$u['tipo']}</td>";
        echo "<td>{$u['status']}</td>";
        echo "<td>{$u['senha_inicio']}...</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
