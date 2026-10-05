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
        tbody td { padding: 5px 8px; border-bottom: 1px solid #d0d0d4; font-size: 8.5pt; vertical-align: top; }
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
        <p class="subtitulo">Matriz de Identificacion y Analisis de Riesgos</p>
    </div>

    <h1>Matriz de Riesgos del Proyecto</h1>

    <div class="meta">
        <strong>Proyecto:</strong> <?= str_repeat('_', 60) ?><br>
        <strong>Estudiante:</strong> <?= htmlspecialchars($nombreEstudiante) ?><br>
        <strong>Fecha:</strong> <?= $fecha ?>
    </div>

    <h2>1. Convenciones de Probabilidad e Impacto</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Nivel</th>
                <th style="width: 25%;">Probabilidad</th>
                <th style="width: 25%;">Impacto</th>
                <th style="width: 25%;">Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Muy bajo</td><td>&lt; 10%</td><td>Insignificante</td><td>1</td></tr>
            <tr><td>Bajo</td><td>10-30%</td><td>Menor</td><td>2</td></tr>
            <tr><td>Medio</td><td>30-50%</td><td>Moderado</td><td>3</td></tr>
            <tr><td>Alto</td><td>50-70%</td><td>Mayor</td><td>4</td></tr>
            <tr><td>Muy alto</td><td>&gt; 70%</td><td>Catastrofico</td><td>5</td></tr>
        </tbody>
    </table>

    <h2>2. Matriz Probabilidad x Impacto</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 18%; text-align: center; background: #2A2B32;">IMPACTO \ PROB.</th>
                <th style="text-align: center;">Muy baja (1)</th>
                <th style="text-align: center;">Baja (2)</th>
                <th style="text-align: center;">Media (3)</th>
                <th style="text-align: center;">Alta (4)</th>
                <th style="text-align: center;">Muy alta (5)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $matriz = [
                ['Catastrofico (5)', 'c-yellow', 'c-orange', 'c-red', 'c-red', 'c-red'],
                ['Mayor (4)',         'c-green',  'c-yellow', 'c-orange', 'c-red', 'c-red'],
                ['Moderado (3)',      'c-green',  'c-yellow', 'c-yellow', 'c-orange', 'c-red'],
                ['Menor (2)',         'c-green',  'c-green',  'c-yellow', 'c-yellow', 'c-orange'],
                ['Insignificante (1)','c-green',  'c-green',  'c-green',  'c-yellow', 'c-yellow'],
            ];
            foreach ($matriz as $fila): ?>
            <tr class="row-h">
                <td style="background: #f5f5f7; font-weight: bold;"><?= $fila[0] ?></td>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <td style="text-align: center;"><span class="cuadrante <?= $fila[$i] ?>"></span><?= array_search($fila[$i], ['c-green'=>1,'c-yellow'=>2,'c-orange'=>3,'c-red'=>4]) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p style="font-size: 8pt; color: #4A4B52;">Verde = Aceptar | Amarillo = Monitorear | Naranja = Mitigar | Rojo = Tratar urgentemente</p>

    <h2>3. Registro de Riesgos</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 17%;">Riesgo</th>
                <th style="width: 6%;">P</th>
                <th style="width: 6%;">I</th>
                <th style="width: 6%;">PxI</th>
                <th style="width: 13%;">Categoria</th>
                <th style="width: 18%;">Estrategia</th>
                <th style="width: 13%;">Responsable</th>
                <th style="width: 17%;">Plan de mitigacion</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 12; $i++): ?>
            <tr class="row-h">
                <td style="text-align: center;">R-<?= $i ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>[ ] Tec [ ] Neg [ ] Per [ ] Fin</td>
                <td>[ ] Evitar [ ] Mitigar [ ] Transferir [ ] Aceptar</td>
                <td></td>
                <td></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer">
        Matriz de Riesgos &middot; UPTP-LMR &middot; PNFI &middot; Plantilla version 1.0
    </div>
</body>
</html>
