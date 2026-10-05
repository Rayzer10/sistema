<div class="card-custom">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4>Gestor de Contenido</h4>
            <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">
                Carga las unidades por nivel. Cada unidad puede tener video (MP4), presentación (PPTX), documento (PDF) y/o HTML dinámico.
            </p>
        </div>
        <a href="<?= BASE_URL ?>unidad/form" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Unidad
        </a>
    </div>
    <div class="card-body-custom">
        <?php
        $colors = ['#5E6AD2', '#28A745', '#FF9800', '#DC3545'];
        $hayUnidades = false;
        foreach ($niveles as $b) {
            if (!empty($b['unidades'])) { $hayUnidades = true; break; }
        }
        ?>

        <?php if (!$hayUnidades): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h5>No hay unidades creadas</h5>
                <p>Comienza creando la primera unidad de contenido</p>
                <a href="<?= BASE_URL ?>unidad/form" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Crear Unidad
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($niveles as $bloque):
                $n = $bloque['nivel'];
                $unidades = $bloque['unidades'];
                $color = $colors[($n['numero'] - 1)] ?? '#5E6AD2';
            ?>
                <div style="margin-bottom: 24px;">
                    <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 24px; height: 24px; border-radius: 6px; background: <?= $color ?>; color: white; text-align: center; line-height: 24px; font-size: 12px; font-weight: 600;"><?= (int)$n['numero'] ?></span>
                        <?= htmlspecialchars($n['nombre']) ?>
                    </h5>

                    <?php if (empty($unidades)): ?>
                        <p style="color: #4A4B52; font-size: 13px; padding: 12px; background: #0A0A0B; border-radius: 8px;">
                            Sin unidades todavía.
                        </p>
                    <?php else: ?>
                        <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; overflow: hidden;">
                            <?php foreach ($unidades as $i => $u):
                                $encId = $this->encodeId($u['id']);
                            ?>
                                <div style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; <?= $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.04);' : '' ?>">
                                    <div style="width: 32px; height: 32px; border-radius: 6px; background: rgba(255,255,255,0.04); color: #8A8F98; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                        <?= (int)$u['orden'] ?>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="color: #FFFFFF; font-size: 13px; font-weight: 500; margin-bottom: 2px;">
                                            <?= htmlspecialchars($u['titulo']) ?>
                                        </div>
                                        <div style="color: #8A8F98; font-size: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <span><?= (int)$u['total_archivos'] ?> archivo(s)</span>
                                            <span>&middot;</span>
                                            <span style="background: rgba(94,106,210,0.12); color: #8B93F0; padding: 2px 8px; border-radius: 6px;"><?= htmlspecialchars($u['materia_nombre']) ?></span>
                                            <?php if (!empty($u['descripcion'])): ?>
                                                <span>&middot;</span>
                                                <span style="overflow:hidden; text-overflow:ellipsis; max-width:300px; white-space:nowrap;"><?= htmlspecialchars($u['descripcion']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($u['estado'] == 1): ?>
                                            <span class="badge-estado badge-activo">Publicado</span>
                                        <?php else: ?>
                                            <span class="badge-estado badge-deshabilitado">Borrador</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="<?= BASE_URL ?>unidad/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" title="Editar" style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <a href="<?= BASE_URL ?>unidad/eliminar/<?= $encId ?>"
                                           class="btn-outline-danger btn-sm-custom btn-eliminar"
                                           title="Eliminar"
                                           data-nombre="<?= htmlspecialchars($u['titulo']) ?>"
                                           style="text-decoration: none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
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
                title: '¿Eliminar unidad?',
                html: 'Se eliminará la unidad <strong>' + nombre + '</strong> y todos sus archivos asociados.<br>Esta acción no se puede deshacer.',
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
