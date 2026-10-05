<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Listado de Usuarios</h4>
        <a href="<?= BASE_URL ?>usuario/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
                <h5>No hay usuarios registrados</h5>
                <p>Comienza agregando tu primer usuario</p>
                <a href="<?= BASE_URL ?>usuario/form" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agregar Usuario
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <?php $encId = $this->encodeId($user['id']); ?>
                            <tr>
                                <td data-label="ID"><?= $user['id'] ?></td>
                                <td data-label="Nombre"><?= htmlspecialchars($user['nombre']) ?></td>
                                <td data-label="Correo"><?= htmlspecialchars($user['correo']) ?></td>
                                <td data-label="Teléfono"><?= !empty($user['telefono']) ? htmlspecialchars($user['telefono']) : '—' ?></td>
                                <td data-label="Rol">
<?php
$rolClass = 'badge-operador';
if ($user['rol'] == 1) $rolClass = 'badge-admin';
elseif ($user['rol'] == 2) $rolClass = 'badge-docente';
elseif ($user['rol'] == 3) $rolClass = 'badge-estudiante';

// Cargar materias si es docente
$materiasDocente = [];
if ((int)$user['rol'] === 2) {
    $db = \Database::getInstance();
    $rows = $db->fetchAll(
        "SELECT m.nombre FROM materia m
         INNER JOIN docente_materia dm ON dm.id_materia = m.id
         WHERE dm.id_docente = ? ORDER BY m.nombre",
        [$user['id']]
    );
    foreach ($rows as $r) $materiasDocente[] = $r['nombre'];
}
?>
                                    <span class="badge-rol <?= $rolClass ?>">
                                        <?= \Auth::rolLabel($user['rol']) ?>
                                    </span>
                                    <?php if ((int)$user['rol'] === 2 && !empty($materiasDocente)): ?>
                                        <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                            <?php foreach ($materiasDocente as $mn): ?>
                                                <span style="background: rgba(94,106,210,0.12); color: #8A8FE0; padding: 2px 8px; border-radius: 4px; font-size: 10px;">
                                                    <?= htmlspecialchars($mn) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Estado">
                                    <span class="badge-estado <?= $user['estado'] == 1 ? 'badge-activo' : 'badge-deshabilitado' ?>">
                                        <?= $user['estado'] == 1 ? 'Activo' : 'Deshabilitado' ?>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <div style="display: flex; gap: 6px;">
                                        <a href="<?= BASE_URL ?>usuario/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Editar" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="<?= BASE_URL ?>usuario/eliminar/<?= $encId ?>"
                                           class="btn-outline-danger btn-sm-custom btn-eliminar"
                                           title="Eliminar"
                                           data-nombre="<?= htmlspecialchars($user['nombre']) ?>"
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
            <div style="margin-top: 16px; font-size: 12px; color: #4A4B52;">
                Total: <strong><?= count($usuarios) ?></strong> usuario(s)
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnsEliminar = document.querySelectorAll('.btn-eliminar');

    btnsEliminar.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const nombre = this.getAttribute('data-nombre');

            swalTheme.fire({
                title: '¿Eliminar usuario?',
                html: '¿Estás seguro de eliminar a <strong>' + nombre + '</strong>?<br>Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#1B1B1F',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>