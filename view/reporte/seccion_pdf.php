<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 50px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1a1a1a; font-size: 11pt; line-height: 1.4; }
        h1 { color: #2A2B32; font-size: 18pt; margin: 0 0 4px; }
        h2 { color: #2A2B32; font-size: 13pt; margin: 24px 0 8px; border-bottom: 2px solid #5E6AD2; padding-bottom: 4px; }
        .header { text-align: center; border-bottom: 3px solid #5E6AD2; padding-bottom: 12px; margin-bottom: 20px; }
        .header .institucion { font-size: 13pt; font-weight: bold; color: #2A2B32; margin: 0; }
        .header .subtitulo { font-size: 10pt; color: #4A4B52; margin: 2px 0 0; }
        .header .departamento { font-size: 9pt; color: #8A8F98; margin: 2px 0 0; font-style: italic; }
        .info-box { background: #f5f5f7; border: 1px solid #d0d0d4; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 3px 6px; font-size: 10pt; }
        .info-box td.label { color: #4A4B52; width: 25%; }
        .info-box td.value { color: #1a1a1a; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        thead th { background: #5E6AD2; color: white; padding: 7px 10px; text-align: left; font-size: 9.5pt; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #d0d0d4; font-size: 9.5pt; }
        tbody tr:nth-child(even) { background: #f9f9fb; }
        .summary-row { background: #f0f0f4; font-weight: bold; }
        .summary-row td { border-top: 2px solid #5E6AD2; }
        .footer { position: fixed; bottom: 20px; left: 50px; right: 50px; text-align: center; font-size: 8pt; color: #8A8F98; border-top: 1px solid #d0d0d4; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="institucion">UPTP-LMR &middot; Departamento de Informática</p>
        <p class="subtitulo">Programa Nacional de Formación (PNFI)</p>
        <p class="departamento">Reporte de Sección</p>
    </div>

    <h1>Reporte de Sección: <?= htmlspecialchars($seccion['nombre']) ?></h1>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Sección:</td>
                <td class="value"><?= htmlspecialchars($seccion['nombre']) ?></td>
                <td class="label">Período:</td>
                <td class="value"><?= htmlspecialchars($seccion['periodo']) ?></td>
            </tr>
            <tr>
                <td class="label">Docente:</td>
                <td class="value"><?= htmlspecialchars($docente) ?></td>
                <td class="label">Estudiantes:</td>
                <td class="value"><?= count($estudiantes) ?></td>
            </tr>
        </table>
    </div>

    <h2>Desempeño de los Estudiantes</h2>

    <?php if (empty($estudiantes)): ?>
        <p style="color: #4A4B52; font-style: italic;">Esta sección no tiene estudiantes asignados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Estudiante</th>
                    <th style="width: 22%;">Correo</th>
                    <th style="width: 11%;">Unidades</th>
                    <th style="width: 11%;">Dinámicas</th>
                    <th style="width: 11%;">Prom. Din.</th>
                    <th style="width: 10%;">Esc. Asig.</th>
                    <th style="width: 10%;">Esc. Ent.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totUnidades = 0; $totDinamicas = 0; $totPromDin = []; $totAsig = 0; $totEnt = 0;
                foreach ($estudiantes as $e):
                    $unidades = (int)$e['unidades_completadas'];
                    $dinamicas = (int)$e['dinamicas_completadas'];
                    $promDin = $e['promedio_dinamicas'] !== null ? (float)$e['promedio_dinamicas'] : null;
                    $escAsig = (int)$e['escenarios_asignados'];
                    $escEnt = (int)$e['escenarios_entregados'];
                    $totUnidades += $unidades;
                    $totDinamicas += $dinamicas;
                    $totAsig += $escAsig;
                    $totEnt += $escEnt;
                    if ($promDin !== null) $totPromDin[] = $promDin;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td style="font-size: 8pt; color: #4A4B52;"><?= htmlspecialchars($e['correo']) ?></td>
                        <td style="text-align: center;"><?= $unidades ?></td>
                        <td style="text-align: center;"><?= $dinamicas ?></td>
                        <td style="text-align: center;"><?= $promDin !== null ? number_format($promDin, 1) . '%' : '—' ?></td>
                        <td style="text-align: center;"><?= $escAsig ?></td>
                        <td style="text-align: center;"><?= $escEnt ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="summary-row">
                    <td colspan="2" style="text-align: right;">Promedio / Total:</td>
                    <td style="text-align: center;"><?= number_format($totUnidades / max(count($estudiantes), 1), 1) ?></td>
                    <td style="text-align: center;"><?= number_format($totDinamicas / max(count($estudiantes), 1), 1) ?></td>
                    <td style="text-align: center;"><?= !empty($totPromDin) ? number_format(array_sum($totPromDin) / count($totPromDin), 1) . '%' : '—' ?></td>
                    <td style="text-align: center;"><?= $totAsig ?></td>
                    <td style="text-align: center;"><?= $totEnt ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        UPTP-LMR &middot; Departamento de Informática &middot; PNFI &middot; Reporte generado el <?= $fechaGeneracion ?>
    </div>
</body>
</html>
