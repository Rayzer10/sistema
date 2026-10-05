<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Secciones</h4>
        <a href="<?= BASE_URL ?>seccion/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Sección
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($secciones)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <h5>No hay secciones registradas</h5>
                <p>Primero crea un período académico, luego crea secciones</p>
                <a href="<?= BASE_URL ?>seccion/form" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Crear Sección
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Sección</th>
                            <th>Período</th>
                            <th>Materias (Docente)</th>
                            <th>Estudiantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($secciones as $s): ?>
                            <?php $encId = $this->encodeId($s['id']); ?>
                            <tr>
                                <td data-label="Sección"><strong><?= htmlspecialchars($s['nombre']) ?></strong></td>
                                <td data-label="Período"><?= htmlspecialchars($s['periodo']) ?></td>
                                <td data-label="Materias">
                                    <?php if (!empty($s['materias'])): ?>
                                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                            <?php foreach ($s['materias'] as $mat): ?>
                                                <div style="background: rgba(94,106,210,0.12); color: #8B93F0; border: 1px solid rgba(94,106,210,0.25); border-radius: 20px; padding: 3px 10px; font-size: 11.5px; font-weight: 500; white-space: nowrap;">
                                                    <?= htmlspecialchars($mat['nombre']) ?>
                                                    <span style="color: #8A8F98; font-weight: 400;"><?= $mat['docente'] ? '· ' . htmlspecialchars($mat['docente']) : '' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #4A4B52;">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Estudiantes">
                                    <span class="badge-rol badge-estudiante"><?= (int)$s['total_estudiantes'] ?></span>
                                </td>
                                <td data-label="Acciones">
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <a href="<?= BASE_URL ?>seccion/asignar/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Asignar estudiantes" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                            Estudiantes
                                        </a>
                                        <a href="<?= BASE_URL ?>seccion/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Editar" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="<?= BASE_URL ?>seccion/eliminar/<?= $encId ?>"
                                           class="btn-outline-danger btn-sm-custom btn-eliminar"
                                           title="Eliminar"
                                           data-nombre="<?= htmlspecialchars($s['nombre']) ?>"
                                           data-estudiantes="<?= (int)$s['total_estudiantes'] ?>"
                                           style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </a>
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
            var est = parseInt(this.getAttribute('data-estudiantes') || '0', 10);

            var html = '¿Estás seguro de eliminar la sección <strong>' + nombre + '</strong>?';
            if (est > 0) {
                html += '<br><br><small style="color: #FFC107;">⚠ Esta sección tiene ' + est + ' estudiante(s) que también serán desvinculados.</small>';
            }

            swalTheme.fire({
                title: '¿Eliminar sección?',
                html: html,
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
