<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Períodos Académicos</h4>
        <a href="<?= BASE_URL ?>periodo/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo Período
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($periodos)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <h5>No hay períodos registrados</h5>
                <p>Comienza creando el primer período académico</p>
                <a href="<?= BASE_URL ?>periodo/form" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Crear Período
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Secciones</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periodos as $p): ?>
                            <?php $encId = $this->encodeId($p['id']); ?>
                            <tr>
                                <td data-label="Nombre"><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                                <td data-label="Inicio"><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></td>
                                <td data-label="Fin"><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
                                <td data-label="Secciones">
                                    <span class="badge-rol badge-estudiante"><?= (int)$p['total_secciones'] ?></span>
                                </td>
                                <td data-label="Estado">
                                    <span class="badge-estado <?= $p['estado'] == 1 ? 'badge-activo' : 'badge-deshabilitado' ?>">
                                        <?= $p['estado'] == 1 ? 'Activo' : 'Cerrado' ?>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <div style="display: flex; gap: 6px;">
                                        <a href="<?= BASE_URL ?>periodo/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Editar" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="<?= BASE_URL ?>periodo/eliminar/<?= $encId ?>"
                                           class="btn-outline-danger btn-sm-custom btn-eliminar"
                                           title="Eliminar"
                                           data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                           data-con-secciones="<?= (int)$p['total_secciones'] ?>"
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
            var conSecciones = parseInt(this.getAttribute('data-con-secciones') || '0', 10);

            if (conSecciones > 0) {
                swalTheme.fire({
                    title: 'No se puede eliminar',
                    html: 'El período <strong>' + nombre + '</strong> tiene ' + conSecciones + ' sección(es) asociada(s).<br>Elimina primero las secciones.',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            swalTheme.fire({
                title: '¿Eliminar período?',
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
