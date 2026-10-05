<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>asignacion" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Asignaciones
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h4>Nueva Asignación de Escenario</h4>
        <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">Selecciona la sección, el escenario y los estudiantes a asignar.</p>
    </div>
    <div class="card-body-custom">
        <?php if (empty($secciones)): ?>
            <div class="empty-state">
                <h5>No tienes secciones asignadas</h5>
                <p>Pide al administrador que te asigne a una sección.</p>
            </div>
        <?php else: ?>
            <form action="<?= BASE_URL ?>asignacion/form" method="POST" id="formAsignar">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Sección</label>
                            <select class="form-select" name="id_seccion" id="id_seccion" required>
                                <option value="">Seleccione una sección</option>
                                <?php foreach ($secciones as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?> (<?= htmlspecialchars($s['periodo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Seleccione una sección.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Escenario</label>
                            <select class="form-select" name="id_escenario" id="id_escenario" required>
                                <option value="">Seleccione un escenario</option>
                                <?php foreach ($escenarios as $e): ?>
                                    <option value="<?= $e['id'] ?>">[<?= htmlspecialchars($e['nivel']) ?>] <?= htmlspecialchars($e['titulo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Seleccione un escenario.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Fecha límite <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                            <input type="date" class="form-control" name="fecha_limite">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Indicaciones para el estudiante <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                    <textarea class="form-control" name="observaciones_docente" rows="2" maxlength="1000" placeholder="Instrucciones o aclaraciones adicionales..."></textarea>
                </div>

                <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

                <div class="form-group">
                    <label class="form-label">Estudiantes a asignar <span style="color:#DC3545;">*</span></label>
                    <div id="estudiantes-container" style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 16px; min-height: 100px;">
                        <p style="color: #8A8F98; font-size: 13px; text-align: center; margin: 16px 0;">
                            Primero selecciona una sección para ver los estudiantes disponibles
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 24px;">
                    <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;" id="btnSubmit" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Asignar
                    </button>
                    <a href="<?= BASE_URL ?>asignacion" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectSeccion = document.getElementById('id_seccion');
    var container = document.getElementById('estudiantes-container');
    var btnSubmit = document.getElementById('btnSubmit');

    if (!selectSeccion) return;

    selectSeccion.addEventListener('change', function() {
        var idSeccion = this.value;
        if (!idSeccion) {
            container.innerHTML = '<p style="color: #8A8F98; font-size: 13px; text-align: center; margin: 16px 0;">Selecciona una sección</p>';
            btnSubmit.disabled = true;
            return;
        }

        fetch('<?= BASE_URL ?>asignacion/estudiantesSeccionAjax?seccion=' + idSeccion, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                container.innerHTML = '<p style="color: #DC3545; font-size: 13px; text-align: center; margin: 16px 0;">' + data.error + '</p>';
                btnSubmit.disabled = true;
                return;
            }
            if (!data.estudiantes || data.estudiantes.length === 0) {
                container.innerHTML = '<p style="color: #8A8F98; font-size: 13px; text-align: center; margin: 16px 0;">Esta sección no tiene estudiantes asignados</p>';
                btnSubmit.disabled = true;
                return;
            }

            var html = '<div style="display: flex; justify-content: flex-end; margin-bottom: 8px;">';
            html += '<button type="button" onclick="seleccionarTodos()" class="btn-outline-secondary" style="padding: 4px 10px; font-size: 11px;">Seleccionar todos</button>';
            html += '</div>';

            data.estudiantes.forEach(function(est) {
                html += '<label style="display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background=\'rgba(255,255,255,0.04)\'" onmouseout="this.style.background=\'transparent\'">';
                html += '<input type="checkbox" name="estudiantes[]" value="' + est.id + '" class="chk-estudiante" onchange="actualizarSubmit()">';
                html += '<div style="flex: 1; min-width: 0;">';
                html += '<div style="color: #FFFFFF; font-size: 13px; font-weight: 500;">' + est.nombre + '</div>';
                html += '<div style="color: #8A8F98; font-size: 12px;">' + est.correo + '</div>';
                html += '</div></label>';
            });

            container.innerHTML = html;
            btnSubmit.disabled = false;
        })
        .catch(function() {
            container.innerHTML = '<p style="color: #DC3545; font-size: 13px; text-align: center; margin: 16px 0;">Error al cargar estudiantes</p>';
            btnSubmit.disabled = true;
        });
    });
});

function seleccionarTodos() {
    var checks = document.querySelectorAll('.chk-estudiante');
    var algunoDesmarcado = Array.from(checks).some(function(c) { return !c.checked; });
    checks.forEach(function(c) { c.checked = algunoDesmarcado; });
    actualizarSubmit();
}

function actualizarSubmit() {
    var checks = document.querySelectorAll('.chk-estudiante');
    var algunoMarcado = Array.from(checks).some(function(c) { return c.checked; });
    document.getElementById('btnSubmit').disabled = !algunoMarcado;
}
</script>
