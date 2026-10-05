<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 50px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1a1a1a; font-size: 11pt; line-height: 1.4; }
        h1 { font-size: 18pt; margin: 0 0 4px; color: #2A2B32; }
        h2 { font-size: 13pt; margin: 18px 0 6px; color: #2A2B32; border-bottom: 2px solid #5E6AD2; padding-bottom: 4px; }
        h3 { font-size: 11pt; margin: 12px 0 4px; color: #4A4B52; }
        .header { text-align: center; border-bottom: 3px solid #5E6AD2; padding-bottom: 10px; margin-bottom: 18px; }
        .header .institucion { font-size: 12pt; font-weight: bold; color: #2A2B32; margin: 0; }
        .header .subtitulo { font-size: 9pt; color: #4A4B52; margin: 2px 0 0; font-style: italic; }
        .meta { background: #f5f5f7; border: 1px solid #d0d0d4; border-radius: 4px; padding: 8px 12px; margin-bottom: 14px; font-size: 10pt; }
        .meta strong { color: #4A4B52; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        thead th { background: #5E6AD2; color: white; padding: 6px 8px; text-align: left; font-size: 8.5pt; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #d0d0d4; font-size: 8.5pt; vertical-align: top; }
        .row-h td { height: 45px; }
        .footer { position: fixed; bottom: 20px; left: 50px; right: 50px; text-align: center; font-size: 8pt; color: #8A8F98; border-top: 1px solid #d0d0d4; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="institucion">UPTP-LMR &middot; Departamento de Informática &middot; PNFI</p>
        <p class="subtitulo">Tabla de Backlog de Producto (Historias de Usuario)</p>
    </div>

    <h1>Tabla de Backlog</h1>

    <div class="meta">
        <strong>Proyecto:</strong> <?= str_repeat('_', 60) ?><br>
        <strong>Estudiante:</strong> <?= htmlspecialchars($nombreEstudiante) ?><br>
        <strong>Fecha:</strong> <?= $fecha ?>
    </div>

    <h2>Convenciones</h2>
    <ul style="font-size: 10pt; color: #4A4B52; margin: 4px 0 12px 18px;">
        <li><strong>Prioridad MoSCoW:</strong> Must / Should / Could / Won't</li>
        <li><strong>Story Points:</strong> Estimacion de esfuerzo (Fibonacci: 1, 2, 3, 5, 8, 13)</li>
        <li><strong>Estado:</strong> Pendiente / En progreso / En revision / Completada</li>
    </ul>

    <h2>Backlog Priorizado</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 7%;">ID</th>
                <th style="width: 23%;">Titulo</th>
                <th style="width: 28%;">Descripcion (Como... quiero... para...)</th>
                <th style="width: 11%;">Criterios de aceptacion</th>
                <th style="width: 7%;">SP</th>
                <th style="width: 8%;">Prioridad</th>
                <th style="width: 12%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 14; $i++): ?>
            <tr class="row-h">
                <td style="text-align: center;"><?= $i ?></td>
                <td>US-<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>[ ] M [ ] S [ ] C [ ] W</td>
                <td></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <h2>Notas y suposiciones</h2>
    <div style="border: 1px solid #4A4B52; border-radius: 3px; height: 80px;"></div>

    <div class="footer">
        Tabla de Backlog &middot; UPTP-LMR &middot; PNFI &middot; Plantilla version 1.0
    </div>
</body>
</html>
