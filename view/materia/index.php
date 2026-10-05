<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Materias</h4>
        <a href="<?= BASE_URL ?>materia/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Materia
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($materias)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                <h5>No hay materias registradas</h5>
                <p>Comienza creando el catalogo de materias del PNFI</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Secciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materias as $m):
                            $encId = $this->encodeId($m['id']);
                        ?>
                            <tr>
                                <td data-label="Nombre"><strong><?= htmlspecialchars($m['nombre']) ?></strong></td>
                                <td data-label="Secciones">
                                    <span class="badge-rol badge-estudiante"><?= (int)$m['total_secciones'] ?></span>
                                </td>
                                <td data-label="Acciones">
                                    <div style="display: flex; gap: 6px;">
                                        <a href="<?= BASE_URL ?>materia/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Editar" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="<?= BASE_URL ?>materia/eliminar/<?= $encId ?>"
                                           class="btn-outline-danger btn-sm-custom btn-eliminar"
                                           title="Eliminar"
                                           data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                                           data-secciones="<?= (int)$m['total_secciones'] ?>"
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
            var secciones = parseInt(this.getAttribute('data-secciones') || '0', 10);

            if (secciones > 0) {
                swalTheme.fire({
                    title: 'No se puede eliminar',
                    html: 'La materia <strong>' + nombre + '</strong> tiene ' + secciones + ' sección(es) asociada(s).',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            swalTheme.fire({
                title: '¿Eliminar materia?',
                html: '¿Estás seguro de eliminar <strong>' + nombre + '</strong>?',
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
