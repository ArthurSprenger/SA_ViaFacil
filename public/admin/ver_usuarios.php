<?php
require_once __DIR__ . '/../../config/db.php';

$conn = db_connect();

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Usuários no Banco de Dados</title>";
echo "<link rel='stylesheet' href='../../styles/ver_usuarios.css'>";
echo "</head>";
echo "<body>";

echo "<h2>Usuários no Banco de Dados</h2>";

$result = $conn->query("SELECT id, nome, email, senha, tipo, status FROM usuarios ORDER BY id ASC");

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Senha (Hash)</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td class='senha'>" . htmlspecialchars(substr($row['senha'], 0, 50)) . "...</td>";
        echo "<td><strong>" . strtoupper($row['tipo']) . "</strong></td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<h3 style='margin-top: 30px;'>Senhas padrão para teste:</h3>";
    echo "<ul>";
    echo "<li><strong>admin@exemplo.com</strong> → senha: <code>admin123</code></li>";
    echo "<li><strong>usuario@exemplo.com</strong> → senha: <code>senha123</code></li>";
    echo "<li><strong>operador@exemplo.com</strong> → senha: <code>operador123</code></li>";
    echo "<li><strong>felipe@viafacil.com</strong> → senha: <code>felipe123</code></li>";
    echo "</ul>";
} else {
    echo "<p>Nenhum usuário encontrado.</p>";
}

$conn->close();

echo "</body>";
echo "</html>";
?>
