<div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <a href="<?= BASE_URL ?>actividad" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver
    </a>
    <span style="color: #4A4B52; font-size: 13px;">&middot;</span>
    <span style="color: #8A8F98; font-size: 13px;"><?= htmlspecialchars($actividad['titulo']) ?></span>
</div>

<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4>Partes de la Actividad</h4>
            <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">
                Cada parte tiene una sección introductoria y una evaluación con preguntas de opción múltiple y contador de tiempo.
            </p>
        </div>
        <a href="<?= BASE_URL ?>actividad/parteForm/<?= $encodedId ?>" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Parte
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($partes)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h5>No hay partes todavía</h5>
                <p>Crea la primera parte de la actividad. Se resolverán en orden secuencial.</p>
                <a href="<?= BASE_URL ?>actividad/parteForm/<?= $encodedId ?>" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Crear Parte
                </a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($partes as $i => $p): ?>
                    <?php $encP = $this->encodeId($p['id']); ?>
                    <div style="display: flex; align-items: center; gap: 14px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 14px 16px;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(94,106,210,0.15); color: #8B93F0; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                            <?= (int)$p['orden'] ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="color: #FFFFFF; font-size: 14px; font-weight: 500;"><?= htmlspecialchars($p['titulo']) ?></span>
                                <?php if ($p['estado'] == 1): ?>
                                    <span class="badge-estado badge-activo" style="font-size: 10px;">Activa</span>
                                <?php else: ?>
                                    <span class="badge-estado badge-deshabilitado" style="font-size: 10px;">Inactiva</span>
                                <?php endif; ?>
                            </div>
                            <div style="color: #8A8F98; font-size: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 4px;">
                                <span><?= (int)$p['total_preguntas'] ?> pregunta(s)</span>
                                <span>&middot;</span>
                                <span><?= (int)$p['total_puntos'] ?> pts</span>
                                <?php if ($p['tiempo_limite_min']): ?>
                                    <span>&middot;</span>
                                    <span><?= (int)$p['tiempo_limite_min'] ?> min de evaluación</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <a href="<?= BASE_URL ?>actividad/parteForm/<?= $encodedId ?>/<?= $encP ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Editar</a>
                            <a href="<?= BASE_URL ?>actividad/parteEliminar/<?= $encodedId ?>/<?= $encP ?>" class="btn-outline-danger btn-sm-custom btn-eliminar" data-nombre="<?= htmlspecialchars($p['titulo']) ?>" style="text-decoration: none;">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                title: '¿Eliminar parte?',
                html: 'Se eliminará la parte <strong>' + nombre + '</strong> y sus preguntas.<br>Esta acción no se puede deshacer.',
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