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
        .field { margin-bottom: 8px; }
        .field-label { font-size: 9pt; color: #4A4B52; font-weight: bold; margin-bottom: 2px; }
        .line { border-bottom: 1px solid #4A4B52; height: 18px; margin-bottom: 6px; }
        .box { border: 1px solid #4A4B52; border-radius: 3px; height: 60px; margin-bottom: 6px; }
        .box-large { border: 1px solid #4A4B52; border-radius: 3px; height: 120px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        thead th { background: #5E6AD2; color: white; padding: 6px 8px; text-align: left; font-size: 9pt; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #d0d0d4; font-size: 9pt; vertical-align: top; }
        .footer { position: fixed; bottom: 20px; left: 50px; right: 50px; text-align: center; font-size: 8pt; color: #8A8F98; border-top: 1px solid #d0d0d4; padding-top: 6px; }
        .rf-row td { height: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="institucion">UPTP-LMR &middot; Departamento de Informática &middot; PNFI</p>
        <p class="subtitulo">Plantilla de Especificacion de Requisitos de Software (ERS)</p>
    </div>

    <h1>Especificacion de Requisitos de Software</h1>

    <div class="meta">
        <strong>Proyecto:</strong> <?= str_repeat('_', 60) ?><br>
        <strong>Estudiante:</strong> <?= htmlspecialchars($nombreEstudiante) ?><br>
        <strong>Fecha:</strong> <?= $fecha ?>
    </div>

    <h2>1. Introduccion</h2>
    <h3>1.1 Proposito</h3>
    <div class="box-large"></div>

    <h3>1.2 Alcance</h3>
    <div class="box-large"></div>

    <h3>1.3 Definiciones, acronimos y abreviaturas</h3>
    <div class="box"></div>

    <h3>1.4 Referencias</h3>
    <div class="box"></div>

    <h2>2. Descripcion general</h2>
    <h3>2.1 Perspectiva del producto</h3>
    <div class="box"></div>

    <h3>2.2 Funciones del producto</h3>
    <div class="box"></div>

    <h3>2.3 Caracteristicas de los usuarios</h3>
    <div class="box"></div>

    <h3>2.4 Restricciones</h3>
    <div class="box"></div>

    <h3>2.5 Suposiciones y dependencias</h3>
    <div class="box"></div>

    <h2>3. Requisitos especificos</h2>

    <h3>3.1 Requisitos funcionales (RF)</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">ID</th>
                <th style="width: 50%;">Descripcion</th>
                <th style="width: 18%;">Prioridad</th>
                <th style="width: 20%;">Estabilidad</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 8; $i++): ?>
            <tr class="rf-row">
                <td>RF-<?= $i ?></td>
                <td></td>
                <td>[ ] Alta / [ ] Media / [ ] Baja</td>
                <td>[ ] Estable / [ ] Cambiante</td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <h3>3.2 Requisitos no funcionales (RNF)</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">ID</th>
                <th style="width: 33%;">Categoria</th>
                <th style="width: 55%;">Descripcion / Criterio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cats = ['Rendimiento', 'Seguridad', 'Usabilidad', 'Disponibilidad', 'Mantenibilidad', 'Portabilidad'];
            foreach ($cats as $c): ?>
            <tr class="rf-row">
                <td>RNF-<?= array_search($c, $cats) + 1 ?></td>
                <td><?= $c ?></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>4. Apendices</h2>
    <h3>4.1 Glosario</h3>
    <div class="box"></div>

    <h3>4.2 Historial de cambios</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Version</th>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 25%;">Autor</th>
                <th style="width: 45%;">Descripcion del cambio</th>
            </tr>
        </thead>
        <tbody>
            <tr class="rf-row"><td>1.0</td><td></td><td></td><td></td></tr>
            <tr class="rf-row"><td></td><td></td><td></td><td></td></tr>
            <tr class="rf-row"><td></td><td></td><td></td><td></td></tr>
        </tbody>
    </table>

    <div class="footer">
        ERS Simplificada &middot; UPTP-LMR &middot; PNFI &middot; Plantilla version 1.0
    </div>
</body>
</html>
