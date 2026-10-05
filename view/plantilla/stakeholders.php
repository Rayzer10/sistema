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
        thead th { background: #5E6AD2; color: white; padding: 6px 8px; text-align: left; font-size: 9pt; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #d0d0d4; font-size: 9pt; vertical-align: top; }
        .row-h td { height: 45px; }
        .cuadrante { display: inline-block; width: 12px; height: 12px; margin-right: 4px; vertical-align: middle; border: 1px solid #4A4B52; }
        .c-green { background: #5BCB7A; }
        .c-yellow { background: #FFC107; }
        .c-orange { background: #FF9800; }
        .c-red { background: #DC3545; }
        .footer { position: fixed; bottom: 20px; left: 50px; right: 50px; text-align: center; font-size: 8pt; color: #8A8F98; border-top: 1px solid #d0d0d4; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="institucion">UPTP-LMR &middot; Departamento de Informática &middot; PNFI</p>
        <p class="subtitulo">Matriz de Identificacion y Analisis de Stakeholders</p>
    </div>

    <h1>Matriz de Stakeholders</h1>

    <div class="meta">
        <strong>Proyecto:</strong> <?= str_repeat('_', 60) ?><br>
        <strong>Estudiante:</strong> <?= htmlspecialchars($nombreEstudiante) ?><br>
        <strong>Fecha:</strong> <?= $fecha ?>
    </div>

    <h2>1. Identificacion de Stakeholders</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 22%;">Stakeholder</th>
                <th style="width: 17%;">Rol / Cargo</th>
                <th style="width: 22%;">Interes principal</th>
                <th style="width: 17%;">Nivel de influencia</th>
                <th style="width: 17%;">Nivel de interes</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <tr class="row-h">
                <td style="text-align:center;"><?= $i ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td>[ ] Alto [ ] Medio [ ] Bajo</td>
                <td>[ ] Alto [ ] Medio [ ] Bajo</td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <h2>2. Matriz Poder vs. Interes</h2>
    <p style="font-size: 10pt; color: #4A4B52;">Clasifica a cada stakeholder segun su nivel de poder e interes para definir la estrategia de gestion.</p>

    <table>
        <thead>
            <tr>
                <th style="width: 30%; text-align: center; background: #2A2B32;">PODER \ INTERES</th>
                <th style="text-align: center;">Bajo interes</th>
                <th style="text-align: center;">Interes medio</th>
                <th style="text-align: center;">Alto interes</th>
            </tr>
        </thead>
        <tbody>
            <tr class="row-h">
                <td style="background: #f5f5f7; font-weight: bold;">Alto poder</td>
                <td><span class="cuadrante c-yellow"></span> Mantener satisfecho</td>
                <td><span class="cuadrante c-green"></span> Mantener satisfecho</td>
                <td><span class="cuadrante c-green"></span> Gestionar de cerca</td>
            </tr>
            <tr class="row-h">
                <td style="background: #f5f5f7; font-weight: bold;">Poder medio</td>
                <td><span class="cuadrante c-orange"></span> Monitorear</td>
                <td><span class="cuadrante c-yellow"></span> Mantener informado</td>
                <td><span class="cuadrante c-green"></span> Mantener informado</td>
            </tr>
            <tr class="row-h">
                <td style="background: #f5f5f7; font-weight: bold;">Bajo poder</td>
                <td><span class="cuadrante c-orange"></span> Monitorear</td>
                <td><span class="cuadrante c-yellow"></span> Mantener informado</td>
                <td><span class="cuadrante c-yellow"></span> Mantener satisfecho</td>
            </tr>
        </tbody>
    </table>

    <h2>3. Estrategia de Gestion por Stakeholder</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Stakeholder</th>
                <th style="width: 17%;">Cuadrante</th>
                <th style="width: 31%;">Estrategia</th>
                <th style="width: 15%;">Frecuencia</th>
                <th style="width: 15%;">Canal</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <tr class="row-h">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <h2>4. Observaciones y riesgos</h2>
    <div style="border: 1px solid #4A4B52; border-radius: 3px; height: 100px;"></div>

    <div class="footer">
        Matriz de Stakeholders &middot; UPTP-LMR &middot; PNFI &middot; Plantilla version 1.0
    </div>
</body>
</html>
