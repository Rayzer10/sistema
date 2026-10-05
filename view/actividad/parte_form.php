<div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <a href="<?= BASE_URL ?>actividad/partes/<?= $encodedActividad ?>" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Partes
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($parte) ? 'Editar Parte' : 'Nueva Parte' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>actividad/parteForm/<?= $encodedActividad ?><?= $encodedParte ? '/' . $encodedParte : '' ?>" method="POST" id="formParte" novalidate>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">Título de la parte</label>
                        <input type="text"
                               class="form-control"
                               name="titulo"
                               placeholder="Ej: Parte 1 - Fundamentos de Requisitos"
                               value="<?= htmlspecialchars($parte['titulo'] ?? '') ?>"
                               maxlength="150"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese un título (mínimo 3 caracteres).</div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number" class="form-control" name="orden" min="1" value="<?= (int)($parte['orden'] ?? $siguienteOrden) ?>" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="1" <?= (!isset($parte) || $parte['estado'] == 1) ? 'selected' : '' ?>>Activa</option>
                            <option value="2" <?= (isset($parte) && $parte['estado'] == 2) ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tiempo límite de evaluación (min) <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                        <input type="number" class="form-control" name="tiempo_limite_min" min="1" max="600" placeholder="Ej: 15" value="<?= htmlspecialchars($parte['tiempo_limite_min'] ?? '') ?>">
                        <small style="color:#8A8F98;font-size:11px;">Si se deja vacío, no hay contador de tiempo.</small>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Sección introductoria (HTML)</label>
                        <textarea class="form-control"
                                  name="contenido_html"
                                  rows="6"
                                  style="font-family: 'Courier New', monospace; font-size: 12px;"
                                  placeholder="<p>Explica el contenido que el estudiante debe revisar antes de la evaluación...</p>"><?= htmlspecialchars($parte['contenido_html'] ?? '') ?></textarea>
                        <small style="color:#8A8F98;font-size:11px;">Se sanitiza antes de guardar (sin scripts ni eventos).</small>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($parte) ? 'Actualizar Parte' : 'Guardar Parte' ?>
                </button>
                <a href="<?= BASE_URL ?>actividad/partes/<?= $encodedActividad ?>" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($parte)): ?>
<div class="card-custom" style="margin-top: 20px;">
    <div class="card-header-custom" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4>Preguntas de la evaluación</h4>
            <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">La evaluación de esta parte se compone de preguntas de opción múltiple.</p>
        </div>
        <a href="<?= BASE_URL ?>actividad/preguntaForm/<?= $encodedParte ?>" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Pregunta
        </a>
    </div>
    <div class="card-body-custom">
        <?php if (empty($preguntas)): ?>
            <div class="empty-state">
                <h5>Sin preguntas</h5>
                <p>Agrega preguntas para que los estudiantes puedan completar la evaluación.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($preguntas as $i => $p): ?>
                    <?php $encP = $this->encodeId($p['id']); ?>
                    <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px 14px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,0.05); color: #8A8F98; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;"><?= (int)$p['orden'] ?></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($p['enunciado']) ?></div>
                                <div style="color: #8A8F98; font-size: 11px; margin-top: 2px;"><?= (int)$p['puntos'] ?> punto(s) &middot; <?= count($p['opciones']) ?> opciones</div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <a href="<?= BASE_URL ?>actividad/preguntaForm/<?= $encodedParte ?>/<?= $encP ?>" class="btn-outline-primary btn-sm-custom" style="text-decoration: none;">Editar</a>
                                <a href="<?= BASE_URL ?>actividad/preguntaEliminar/<?= $encodedParte ?>/<?= $encP ?>" class="btn-outline-danger btn-sm-custom btn-eliminar-pregunta" style="text-decoration: none;">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar-pregunta').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            swalTheme.fire({
                title: '¿Eliminar pregunta?',
                html: 'Se eliminará la pregunta y sus opciones. Esta acción no se puede deshacer.',
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
<?php endif; ?>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formParte');
    if (form) FormValidation.init('#formParte');
});
</script>