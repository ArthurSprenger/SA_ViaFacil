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
      <!-- Removido o botão dos três risquinhos -->
      <a href="dashboard.html">
        <img src="../assets/logo.PNG" alt="Viafácil" class="logo-trens" />
      </a>
    </header>

    <main class="conteudo-trens">
        <h1 class="titulo-trens">Quadro de horários</h1>
        <div class="bloco-mapa" style="padding: 0 0 16px 0;">
            <div style="display: flex; align-items: center; justify-content: center; margin-top: 12px; margin-bottom: 8px;">
                <span class="cor-estacao cor-verde"></span>
                <span style="font-weight: bold; font-size: 1.3rem; font-style: italic;">Estação 3</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="padding: 6px; border-bottom: 1px solid #222;">Dia de semana</th>
                        <th style="padding: 6px; border-bottom: 1px solid #222;">Final de semana</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; padding: 4px;">14:00 <span style="color: #ef4444; font-style: italic;">Agora</span></td>
                        <td style="text-align: center; padding: 4px;">12:00</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 4px;">14:30 <span style="color: #ef4444; font-weight: bold;">Interditado</span></td>
                        <td style="text-align: center; padding: 4px;">12:30</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 4px;">15:10</td>
                        <td style="text-align: center; padding: 4px;">13:15</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 4px;">15:45</td>
                        <td style="text-align: center; padding: 4px;">13:50</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 4px;">16:20</td>
                        <td style="text-align: center; padding: 4px;">15:00</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 4px;">17:00</td>
                        <td style="text-align: center; padding: 4px;">15:00</td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 4px;">
                <a href="#" style="color: #222; font-size: 0.98rem; text-decoration: underline;">ver mais...</a>
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