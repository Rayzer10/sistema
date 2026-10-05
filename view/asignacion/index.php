<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Asignaciones de Escenarios</h4>
        <a href="<?= BASE_URL ?>asignacion/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Asignación
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($asignaciones)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <h5>No has asignado escenarios</h5>
                <p>Crea tu primera asignación para empezar a evaluar entregas.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Escenario</th>
                            <th>Asignado</th>
                            <th>Estado</th>
                            <th>Calificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $estadosLabel = [
                            1 => ['label' => 'Pendiente', 'class' => 'badge-deshabilitado'],
                            2 => ['label' => 'Entregado', 'class' => 'badge-docente'],
                            3 => ['label' => 'Evaluado', 'class' => 'badge-activo']
                        ];
                        foreach ($asignaciones as $a):
                            $encId = $this->encodeId($a['id']);
                            $estado = (int)$a['estado'];
                            $info = $estadosLabel[$estado] ?? $estadosLabel[1];
                        ?>
                            <tr>
                                <td data-label="Estudiante">
                                    <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($a['estudiante']) ?></div>
                                </td>
                                <td data-label="Escenario">
                                    <div style="color: #FFFFFF; font-size: 13px;"><?= htmlspecialchars($a['escenario']) ?></div>
                                    <span class="badge-rol badge-docente" style="font-size: 10px;"><?= htmlspecialchars($a['nivel']) ?></span>
                                </td>
                                <td data-label="Asignado"><?= date('d/m/Y', strtotime($a['fecha_asignacion'])) ?></td>
                                <td data-label="Estado">
                                    <span class="badge-rol <?= $info['class'] ?>"><?= $info['label'] ?></span>
                                </td>
                                <td data-label="Calificación">
                                    <?php if ($a['calificacion'] !== null): ?>
                                        <span style="color: #5BCB7A; font-weight: 500;"><?= number_format((float)$a['calificacion'], 1) ?></span>
                                    <?php else: ?>
                                        <span style="color: #4A4B52;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Acciones">
                                    <div style="display: flex; gap: 6px;">
                                        <?php if ($estado == 2): ?>
                                            <a href="<?= BASE_URL ?>evaluacion/evaluar/<?= $encId ?>" class="btn-primary-custom" style="text-decoration: none; padding: 6px 10px; font-size: 12px;">Evaluar</a>
                                        <?php elseif ($estado == 3): ?>
                                            <a href="<?= BASE_URL ?>evaluacion/evaluar/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Ver</a>
                                        <?php endif; ?>
                                        <?php if ($estado == 1): ?>
                                            <a href="<?= BASE_URL ?>asignacion/eliminar/<?= $encId ?>" class="btn-outline-danger btn-sm-custom btn-eliminar" title="Eliminar" data-nombre="<?= htmlspecialchars($a['escenario']) ?>" style="text-decoration: none;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            var nombre = this.getAttribute('data-nombre');
            swalTheme.fire({
                title: '¿Eliminar asignación?',
                html: 'Se eliminará la asignación de <strong>' + nombre + '</strong>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function(r) {
                if (r.isConfirmed) window.location.href = url;
            });
        });
    });
});
</script>
