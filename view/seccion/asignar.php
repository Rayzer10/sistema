<div class="card-custom">
    <div class="card-header-custom">
        <h4>Asignar Estudiantes &mdash; <?= htmlspecialchars($seccion['nombre']) ?> <small style="color:#8A8F98;font-weight:400;">(<?= htmlspecialchars($seccion['periodo']) ?>)</small></h4>
    </div>
    <div class="card-body-custom">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Estudiantes disponibles -->
            <div>
                <h5 style="font-size: 13px; font-weight: 500; color: #8A8F98; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Estudiantes disponibles
                </h5>
                <form action="<?= BASE_URL ?>seccion/asignar/<?= $encodedId ?>" method="POST" id="formAsignar">
                    <div style="max-height: 480px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px; background: #0A0A0B; margin-bottom: 12px;">
                        <?php if (empty($disponibles)): ?>
                            <p style="text-align:center; color: #4A4B52; padding: 20px; font-size: 13px;">No hay estudiantes disponibles</p>
                        <?php else: ?>
                            <?php foreach ($disponibles as $est): ?>
                                <label style="display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="estudiantes[]" value="<?= $est['id'] ?>" class="chk-estudiante">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($est['nombre']) ?></div>
                                        <div style="color: #8A8F98; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($est['correo']) ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn-outline-secondary" onclick="seleccionarTodos()" style="flex: 1; padding: 8px; font-size: 12px; justify-content: center;">
                            Seleccionar todos
                        </button>
                        <button type="submit" class="btn-primary-custom" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px; font-size: 12px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Agregar a la sección
                        </button>
                    </div>
                </form>
            </div>

            <!-- Estudiantes asignados -->
            <div>
                <h5 style="font-size: 13px; font-weight: 500; color: #8A8F98; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Estudiantes en la sección (<?= count($asignados) ?>)
                </h5>
                <div style="max-height: 480px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px; background: #0A0A0B;">
                    <?php if (empty($asignados)): ?>
                        <p style="text-align:center; color: #4A4B52; padding: 20px; font-size: 13px;">No hay estudiantes asignados</p>
                    <?php else: ?>
                        <?php foreach ($asignados as $est): ?>
                            <?php $encEst = $this->encodeId($est['id']); ?>
                            <div style="display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($est['nombre']) ?></div>
                                    <div style="color: #8A8F98; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($est['correo']) ?></div>
                                </div>
                                <a href="<?= BASE_URL ?>seccion/quitarEstudiante/<?= $encodedId ?>/<?= $encEst ?>"
                                   class="btn-outline-danger btn-sm-custom btn-quitar"
                                   data-nombre="<?= htmlspecialchars($est['nombre']) ?>"
                                   style="text-decoration: none; padding: 4px 8px;"
                                   title="Quitar de la sección">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <a href="<?= BASE_URL ?>seccion" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Volver a Secciones
            </a>
        </div>
    </div>
</div>

<script>
function seleccionarTodos() {
    var checks = document.querySelectorAll('.chk-estudiante');
    var algunoDesmarcado = Array.from(checks).some(function(c) { return !c.checked; });
    checks.forEach(function(c) { c.checked = algunoDesmarcado; });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-quitar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            var nombre = this.getAttribute('data-nombre');
            swalTheme.fire({
                title: '¿Quitar estudiante?',
                html: '¿Remover a <strong>' + nombre + '</strong> de la sección?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then(function(r) {
                if (r.isConfirmed) window.location.href = url;
            });
        });
    });
});
</script>
