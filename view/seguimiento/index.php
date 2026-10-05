<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Seguimiento de Estudiantes</h2>
        <p>Visualiza el progreso de los estudiantes de tus secciones.</p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
    </div>
</div>

<?php if (empty($secciones)): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <h5>No tienes secciones asignadas</h5>
                <p>Pide al administrador que te asigne a una sección para empezar a ver estudiantes.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($secciones as $bloque):
        $s = $bloque['seccion'];
        $estudiantes = $bloque['estudiantes'];
    ?>
        <div class="card-custom" style="margin-bottom: 16px;">
            <div class="card-header-custom">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4><?= htmlspecialchars($s['nombre']) ?></h4>
                        <p style="color:#8A8F98; font-size:13px; margin: 4px 0 0;"><?= htmlspecialchars($s['periodo']) ?> &middot; <?= count($estudiantes) ?> estudiante(s)</p>
                    </div>
                    <?php if (!empty($estudiantes)): ?>
                        <a href="<?= BASE_URL ?>reporte/reporteSeccion/<?= $this->encodeId($s['id']) ?>" target="_blank" class="btn-outline-primary btn-sm-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            PDF
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body-custom" style="padding: 0;">
                <?php if (empty($estudiantes)): ?>
                    <p style="text-align:center; color:#4A4B52; padding: 32px; font-size: 13px;">Esta sección aún no tiene estudiantes asignados.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Unidades</th>
                                    <th>Dinámicas</th>
                                    <th>Escenarios</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $e):
                                    $unidades = (int)$e['unidades_completadas'];
                                    $dinamicas = (int)$e['dinamicas_completadas'];
                                    $escAsig = (int)$e['escenarios_asignados'];
                                    $escEnt = (int)$e['escenarios_entregados'];
                                    $promDin = $e['promedio_dinamicas'] !== null ? (float)$e['promedio_dinamicas'] : null;
                                    $promEsc = $e['promedio_escenarios'] !== null ? (float)$e['promedio_escenarios'] : null;
                                ?>
                                    <tr>
                                        <td data-label="Estudiante">
                                            <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($e['nombre']) ?></div>
                                            <div style="color: #8A8F98; font-size: 11px;"><?= htmlspecialchars($e['correo']) ?></div>
                                        </td>
                                        <td data-label="Unidades">
                                            <span class="badge-rol badge-estudiante"><?= $unidades ?> unidades</span>
                                        </td>
                                        <td data-label="Dinámicas">
                                            <div style="color: #FFFFFF; font-size: 13px;"><?= $dinamicas ?> completadas</div>
                                            <?php if ($promDin !== null): ?>
                                                <div style="color: <?= $promDin >= 70 ? '#5BCB7A' : '#FF9800' ?>; font-size: 11px;">Prom: <?= number_format($promDin, 1) ?>%</div>
                                            <?php else: ?>
                                                <div style="color: #4A4B52; font-size: 11px;">Sin intentos</div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Escenarios">
                                            <div style="color: #FFFFFF; font-size: 13px;"><?= $escEnt ?> / <?= $escAsig ?> entregados</div>
                                            <?php if ($promEsc !== null): ?>
                                                <div style="color: #5BCB7A; font-size: 11px;">Prom: <?= number_format($promEsc, 1) ?></div>
                                            <?php elseif ($escAsig > 0): ?>
                                                <div style="color: #FFC107; font-size: 11px;">Pendiente entregas</div>
                                            <?php else: ?>
                                                <div style="color: #4A4B52; font-size: 11px;">Sin asignar</div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
