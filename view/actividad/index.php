<div class="welcome-banner">
    <div class="welcome-text">
        <h2><?= $esDocente ? 'Mis Actividades' : 'Actividades' ?></h2>
        <p><?= $esDocente ? 'Crea escenarios de evaluación y asigna las secciones que tendrán acceso.' : 'Resuelve tus actividades asignadas, parte por parte.' ?></p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
</div>

<?php if (empty($materias)): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                <h5><?= $esDocente ? 'No tienes materias asignadas' : 'No tienes materias asignadas' ?></h5>
                <p><?= $esDocente ? 'Contacta al administrador para que te asigne materias.' : 'Pide al docente que te inscriba en una sección.' ?></p>
            </div>
        </div>
    </div>
<?php else: ?>

    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <label style="color: #8A8F98; font-size: 13px;">Materia:</label>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php foreach ($materias as $m): ?>
                <a href="<?= BASE_URL ?>actividad?materia=<?= (int)$m['id'] ?>"
                   style="text-decoration: none; padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid <?= (int)$m['id'] === $materiaSel ? '#5E6AD2' : 'rgba(255,255,255,0.1)' ?>; color: <?= (int)$m['id'] === $materiaSel ? '#FFFFFF' : '#8A8F98' ?>; background: <?= (int)$m['id'] === $materiaSel ? 'rgba(94,106,210,0.25)' : 'transparent' ?>;">
                    <?= htmlspecialchars($m['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ($esDocente): ?>
            <a href="<?= BASE_URL ?>actividad/form" style="margin-left: auto; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;" class="btn-primary-custom">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nueva Actividad
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($actividades)): ?>
        <div class="card-custom">
            <div class="card-body-custom">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    <h5><?= $esDocente ? 'No hay actividades para esta materia' : 'No tienes actividades en esta materia' ?></h5>
                    <p><?= $esDocente ? 'Crea la primera actividad y asígnala a una sección.' : 'Cuando el docente te asigne una actividad aparecerá aquí.' ?></p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card-custom">
            <div class="card-body-custom" style="padding: 0;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px; padding: 16px;">
                    <?php foreach ($actividades as $a): ?>
                        <?php $encId = $this->encodeId($a['id']); ?>
                        <div style="background: #1B1B1F; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <h5 style="color: #FFFFFF; font-size: 14px; font-weight: 600; margin: 0; flex: 1;"><?= htmlspecialchars($a['titulo']) ?></h5>
                                    <?php if (isset($a['estado'])): ?>
                                        <?php if ($a['estado'] == 1): ?>
                                            <span class="badge-estado badge-activo">Publicada</span>
                                        <?php else: ?>
                                            <span class="badge-estado badge-deshabilitado">Borrador</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($a['descripcion'])): ?>
                                    <p style="color: #8A8F98; font-size: 12px; line-height: 1.5; margin: 8px 0 0;"><?= htmlspecialchars($a['descripcion']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 12px; color: #8A8F98;">
                                <?php if (!empty($a['unidad'])): ?>
                                    <span style="background: rgba(94,106,210,0.12); color: #8B93F0; padding: 3px 8px; border-radius: 6px;"><?= htmlspecialchars($a['unidad']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($a['nivel'])): ?>
                                    <span style="background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 6px;"><?= htmlspecialchars($a['nivel']) ?></span>
                                <?php endif; ?>
                                <span style="background: rgba(94,106,210,0.12); color: #8B93F0; padding: 3px 8px; border-radius: 6px;"><?= (int)$a['total_partes'] ?> parte(s)</span>
                                <?php if ($esDocente): ?>
                                    <span style="background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 6px;"><?= (int)$a['total_secciones'] ?> sección(es)</span>
                                    <span style="background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 6px;"><?= (int)$a['total_entregas'] ?> entrega(s)</span>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; gap: 6px; flex-wrap: wrap; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 12px;">
                                <?php if ($esDocente): ?>
                                    <a href="<?= BASE_URL ?>actividad/partes/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Partes</a>
                                    <a href="<?= BASE_URL ?>actividad/entregas/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Entregas</a>
                                    <a href="<?= BASE_URL ?>actividad/form/<?= $encId ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Editar</a>
                                    <a href="<?= BASE_URL ?>actividad/eliminar/<?= $encId ?>" class="btn-outline-danger btn-sm-custom btn-eliminar" data-nombre="<?= htmlspecialchars($a['titulo']) ?>" style="text-decoration: none;">Eliminar</a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>actividad/ver/<?= $encId ?>" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
                                        Ver Actividad
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            var nombre = this.getAttribute('data-nombre');
            swalTheme.fire({
                title: '¿Eliminar actividad?',
                html: 'Se eliminará la actividad <strong>' + nombre + '</strong> y todas sus partes y entregas asociadas.<br>Esta acción no se puede deshacer.',
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