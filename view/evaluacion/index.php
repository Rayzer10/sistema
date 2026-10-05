<div class="card-custom">
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <h4>Evaluar Entregas</h4>
            <div style="display: flex; gap: 6px;">
                <a href="<?= BASE_URL ?>evaluacion?filtro=pendientes" class="btn-sm-custom <?= $filtro !== 'evaluados' ? 'btn-primary-custom' : 'btn-outline-secondary' ?>" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">Pendientes</a>
                <a href="<?= BASE_URL ?>evaluacion?filtro=evaluados" class="btn-sm-custom <?= $filtro === 'evaluados' ? 'btn-primary-custom' : 'btn-outline-secondary' ?>" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">Evaluados</a>
            </div>
        </div>
    </div>
    <div class="card-body-custom" style="padding: 0;">
        <?php if (empty($entregas)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <h5><?= $filtro === 'evaluados' ? 'No has evaluado entregas aún' : 'No hay entregas pendientes' ?></h5>
                <p><?= $filtro === 'evaluados' ? 'Tus evaluaciones aparecerán aquí.' : 'Cuando los estudiantes entreguen escenarios, aparecerán aquí para evaluar.' ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Escenario</th>
                            <th>Entregado</th>
                            <th>Calificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entregas as $e):
                            $encId = $this->encodeId($e['id']);
                            $estado = (int)$e['estado'];
                        ?>
                            <tr>
                                <td data-label="Estudiante">
                                    <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($e['estudiante']) ?></div>
                                    <div style="color: #8A8F98; font-size: 11px;"><?= htmlspecialchars($e['correo']) ?></div>
                                </td>
                                <td data-label="Escenario">
                                    <div style="color: #FFFFFF; font-size: 13px;"><?= htmlspecialchars($e['escenario']) ?></div>
                                    <span class="badge-rol badge-docente" style="font-size: 10px;"><?= htmlspecialchars($e['nivel']) ?></span>
                                </td>
                                <td data-label="Entregado"><?= $e['fecha_entrega'] ? date('d/m/Y H:i', strtotime($e['fecha_entrega'])) : '—' ?></td>
                                <td data-label="Calificación">
                                    <?php if ($e['calificacion'] !== null): ?>
                                        <span style="color: #5BCB7A; font-weight: 500;"><?= number_format((float)$e['calificacion'], 1) ?></span>
                                    <?php else: ?>
                                        <span class="badge-rol badge-deshabilitado">Sin evaluar</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Acciones">
                                    <a href="<?= BASE_URL ?>evaluacion/evaluar/<?= $encId ?>" class="<?= $estado == 2 ? 'btn-primary-custom' : 'btn-outline-primary btn-sm-custom' ?>" style="text-decoration: none; padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <?php if ($estado == 2): ?>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            Evaluar
                                        <?php else: ?>
                                            Ver / Editar
                                        <?php endif; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
