<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 50px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1a1a1a; font-size: 11pt; line-height: 1.4; }
        h1 { color: #2A2B32; font-size: 18pt; margin: 0 0 4px; }
        h2 { color: #2A2B32; font-size: 13pt; margin: 24px 0 8px; border-bottom: 2px solid #5E6AD2; padding-bottom: 4px; }
        h3 { color: #4A4B52; font-size: 11pt; margin: 14px 0 6px; }
        .header { text-align: center; border-bottom: 3px solid #5E6AD2; padding-bottom: 12px; margin-bottom: 20px; }
        .header .institucion { font-size: 13pt; font-weight: bold; color: #2A2B32; margin: 0; }
        .header .subtitulo { font-size: 10pt; color: #4A4B52; margin: 2px 0 0; }
        .header .departamento { font-size: 9pt; color: #8A8F98; margin: 2px 0 0; font-style: italic; }
        .info-box { background: #f5f5f7; border: 1px solid #d0d0d4; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 3px 6px; font-size: 10pt; }
        .info-box td.label { color: #4A4B52; width: 35%; }
        .info-box td.value { color: #1a1a1a; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        thead th { background: #5E6AD2; color: white; padding: 7px 10px; text-align: left; font-size: 10pt; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #d0d0d4; font-size: 10pt; }
        tbody tr:nth-child(even) { background: #f9f9fb; }
        .stat-grid { width: 100%; border-collapse: collapse; margin: 8px 0 16px; }
        .stat-grid td { width: 33%; padding: 10px; text-align: center; background: #f5f5f7; border: 1px solid #d0d0d4; }
        .stat-grid td .num { font-size: 22pt; font-weight: bold; color: #5E6AD2; line-height: 1; }
        .stat-grid td .lbl { font-size: 9pt; color: #4A4B52; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9pt; font-weight: bold; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-gray { background: #e2e3e5; color: #383d41; }
        .footer { position: fixed; bottom: 20px; left: 50px; right: 50px; text-align: center; font-size: 8pt; color: #8A8F98; border-top: 1px solid #d0d0d4; padding-top: 6px; }
        .progress-bar { width: 100%; height: 8px; background: #e2e3e5; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: #5E6AD2; }
    </style>
</head>
<body>
    <div class="header">
        <p class="institucion">UPTP-LMR &middot; Departamento de Informática</p>
        <p class="subtitulo">Programa Nacional de Formación (PNFI)</p>
        <p class="departamento">Reporte de Desempeño Estudiantil</p>
    </div>

    <h1>Reporte de Desempeño Académico</h1>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Estudiante:</td>
                <td class="value"><?= htmlspecialchars($estudiante['nombre']) ?></td>
                <td class="label">Fecha del reporte:</td>
                <td class="value"><?= $fechaGeneracion ?></td>
            </tr>
            <tr>
                <td class="label">Correo:</td>
                <td class="value"><?= htmlspecialchars($estudiante['correo']) ?></td>
                <td class="label">ID:</td>
                <td class="value">#<?= (int)$estudiante['id'] ?></td>
            </tr>
        </table>
    </div>

    <h2>Indicadores Generales</h2>
    <table class="stat-grid">
        <tr>
            <td>
                <div class="num"><?= $promedioDinamicas !== null ? number_format($promedioDinamicas, 1) . '%' : '—' ?></div>
                <div class="lbl">Promedio Dinámicas</div>
            </td>
            <td>
                <div class="num"><?= $promedioEscenarios !== null ? number_format($promedioEscenarios, 1) : '—' ?></div>
                <div class="lbl">Promedio Escenarios (sobre 100)</div>
            </td>
            <td>
                <div class="num"><?= count(array_filter($asignaciones, fn($a) => (int)$a['estado'] === 3)) ?>/<?= count($asignaciones) ?></div>
                <div class="lbl">Escenarios Evaluados</div>
            </td>
        </tr>
    </table>

    <h2>Progreso por Nivel (Centro de Aprendizaje)</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Nivel</th>
                <th style="width: 42%;">Nombre</th>
                <th style="width: 15%;">Unidades Completadas</th>
                <th style="width: 15%;">Progreso</th>
                <th style="width: 20%;">Barra</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resumenNiveles as $r):
                $total = (int)$r['total_unidades'];
                $completadas = (int)$r['unidades_completadas'];
                $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
            ?>
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #5E6AD2;"><?= (int)$r['numero'] ?></td>
                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                    <td style="text-align: center;"><?= $completadas ?> / <?= $total ?></td>
                    <td style="text-align: center;"><?= $porcentaje ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $porcentaje ?>%;"></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Detalle de Escenarios Asignados</h2>
    <?php if (empty($asignaciones)): ?>
        <p style="color: #4A4B52; font-style: italic;">No hay escenarios asignados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 38%;">Escenario</th>
                    <th style="width: 15%;">Nivel</th>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 14%;">Estado</th>
                    <th style="width: 18%;">Calificación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $a):
                    $estado = (int)$a['estado'];
                    $estadoLabel = [1 => 'Pendiente', 2 => 'Entregado', 3 => 'Evaluado'][$estado] ?? '—';
                    $estadoClass = [1 => 'badge-yellow', 2 => 'badge-gray', 3 => 'badge-green'][$estado] ?? 'badge-gray';
                ?>
                    <tr>
                        <td><?= htmlspecialchars($a['titulo']) ?></td>
                        <td><?= htmlspecialchars($a['nivel']) ?></td>
                        <td><?= date('d/m/Y', strtotime($a['fecha_asignacion'])) ?></td>
                        <td><span class="badge <?= $estadoClass ?>"><?= $estadoLabel ?></span></td>
                        <td style="text-align: center;"><?= $a['calificacion'] !== null ? number_format((float)$a['calificacion'], 1) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        UPTP-LMR &middot; Departamento de Informática &middot; PNFI &middot; Reporte generado el <?= $fechaGeneracion ?>
    </div>
</body>
</html>
