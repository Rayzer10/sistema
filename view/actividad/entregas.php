<div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <a href="<?= BASE_URL ?>actividad" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver
    </a>
    <span style="color: #4A4B52; font-size: 13px;">&middot;</span>
    <span style="color: #8A8F98; font-size: 13px;"><?= htmlspecialchars($actividad['titulo']) ?></span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h4>Entregas</h4>
        <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">
            Progreso y calificaciones de los estudiantes en esta actividad.
        </p>
    </div>
    <div class="card-body-custom" style="padding: 0;">
        <?php if (empty($entregas)): ?>
            <div class="empty-state" style="padding: 40px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <h5>Sin entregas todavía</h5>
                <p>Cuando los estudiantes comiencen la actividad aparecerán aquí.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="padding: 16px;">
                <table class="table table-striped table-hover" id="tablaEntregas">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Sección</th>
                            <th>Estado</th>
                            <th>Partes completadas</th>
                            <th>Calificación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entregas as $e): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 500;"><?= htmlspecialchars($e['nombre']) ?></div>
                                </td>
                                <td style="color: #8A8F98; font-size: 13px;"><?= htmlspecialchars($e['seccion_nombre'] ?? '—') ?></td>
                                <td>
                                    <?php if ($e['estado'] == 2): ?>
                                        <span class="badge-estado badge-activo">Completada</span>
                                    <?php else: ?>
                                        <span class="badge-estado badge-deshabilitado">En progreso</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int)$e['partes_completadas'] ?> / <?= (int)$e['total_partes'] ?></td>
                                <td>
                                    <?php if ($e['calificacion'] !== null): ?>
                                        <span style="color: #8B93F0; font-weight: 600;"><?= number_format((float)$e['calificacion'], 1, '.', '') ?></span>
                                    <?php else: ?>
                                        <span style="color: #4A4B52;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>actividad/ver/<?= $encodedId ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>