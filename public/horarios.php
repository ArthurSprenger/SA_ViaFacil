<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quadro de horários</title>
    <link rel="stylesheet" href="../styles/style2.css">
</head>
<body>
    <header class="cabecalho-trens">
      <a href="dashboard.html">
        <img src="../assets/logo.PNG" alt="Viafácil" class="logo-trens" />
      </a>
    </header>

    <main class="conteudo-trens">
        <h1 class="titulo-trens">Quadro de horários</h1>
        <div class="bloco-mapa horarios-wrapper">
            <div class="horarios-header">
                <span class="cor-estacao cor-verde"></span>
                <span class="horarios-estacao-titulo">Estação 3</span>
            </div>
            <table class="horarios-tabela">
                <thead>
                    <tr>
                        <th class="horarios-th">Dia de semana</th>
                        <th class="horarios-th">Final de semana</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="horarios-td">14:00 <span class="horarios-agora">Agora</span></td>
                        <td class="horarios-td">12:00</td>
                    </tr>
                    <tr>
                        <td class="horarios-td">14:30 <span class="horarios-interditado">Interditado</span></td>
                        <td class="horarios-td">12:30</td>
                    </tr>
                    <tr>
                        <td class="horarios-td">15:10</td>
                        <td class="horarios-td">13:15</td>
                    </tr>
                    <tr>
                        <td class="horarios-td">15:45</td>
                        <td class="horarios-td">13:50</td>
                    </tr>
                    <tr>
                        <td class="horarios-td">16:20</td>
                        <td class="horarios-td">15:00</td>
                    </tr>
                    <tr>
                        <td class="horarios-td">17:00</td>
                        <td class="horarios-td">15:00</td>
                    </tr>
                </tbody>
            </table>
            <div class="horarios-footer">
                <a href="#" class="horarios-ver-mais">ver mais...</a>
            </div>
        </div>
        <div class="paginacao-trens">
            <span class="seta-paginacao">&#8592;</span>
            <span class="numero-pagina">01</span>
            <span class="seta-paginacao">&#8594;</span>
        </div>
    </main>
</body>
</html>