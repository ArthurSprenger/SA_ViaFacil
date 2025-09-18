<?php
// Exibe tela de carregamento
echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Carregando...</title>
	<style>
		body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #003366; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; }
		.carregando { font-size: 2em; text-align: center; }
	</style>
</head>
<body>
	<div class="carregando">Carregando...</div>
</body>
</html>';
flush();
sleep(3);
header('Location: login.php');
exit;
?>