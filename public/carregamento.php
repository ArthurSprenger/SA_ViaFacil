<?php
	
	header('Refresh: 2.8; url=login.php');
	
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Viafácil • Carregando</title>
				<link rel="stylesheet" href="../styles/carregamento.css?v=20250930" />
</head>
<body>
	<div class="wrap">
		<div class="logo-panel" aria-label="Viafácil">
			<img src="../assets/logo.PNG" alt="Viafácil" class="logo" />
		</div>
	</div>
	<div class="spinner" aria-hidden="true"></div>

</body>
</html>