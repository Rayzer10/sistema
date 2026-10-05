<div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <a href="<?= BASE_URL ?>actividad/parteForm/<?= $encodedActividad ?>/<?= $encodedParte ?>" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a la Parte
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($pregunta) ? 'Editar Pregunta' : 'Nueva Pregunta' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>actividad/preguntaForm/<?= $encodedParte ?><?= $encodedPregunta ? '/' . $encodedPregunta : '' ?>" method="POST" id="formPregunta" novalidate>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">Enunciado</label>
                        <input type="text"
                               class="form-control"
                               name="enunciado"
                               placeholder="Ej: ¿Cuál es la técnica más usada para recolectar requisitos?"
                               value="<?= htmlspecialchars($pregunta['enunciado'] ?? '') ?>"
                               maxlength="255"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese el enunciado (mínimo 3 caracteres).</div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number" class="form-control" name="orden" min="1" value="<?= (int)($pregunta['orden'] ?? $siguienteOrden) ?>" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Puntos</label>
                        <input type="number" class="form-control" name="puntos" min="1" max="100" value="<?= (int)($pregunta['puntos'] ?? 1) ?>" required>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.06); margin: 20px 0;">

            <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                Opciones (marca la correcta)
            </h5>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php $op = $opciones[$i - 1] ?? null; ?>
                    <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none; font-size: 12px; color: #8A8F98; min-width: 118px; flex-shrink: 0;">
                                <input type="radio"
                                       name="correcta"
                                       value="<?= $i ?>"
                                       style="width: 15px; height: 15px; accent-color: #5E6AD2;"
                                       <?= ($op && $op['es_correcta'] == 1) ? 'checked' : '' ?>
                                       <?= ($op === null && $i === 1) ? 'checked' : '' ?>>
                                Correcta
                            </label>
                            <input type="text"
                                   class="form-control"
                                   name="opcion_<?= $i ?>"
                                   placeholder="Opción <?= $i ?>"
                                   value="<?= htmlspecialchars($op['texto'] ?? '') ?>"
                                   maxlength="255"
                                   required>
                        </div>
                        <div style="margin-left: 128px; margin-top: 8px;">
                            <input type="text"
                                   class="form-control"
                                   name="retro_<?= $i ?>"
                                   placeholder="Retroalimentación para esta opción (opcional)"
                                   value="<?= htmlspecialchars($op['retroalimentacion'] ?? '') ?>"
                                   maxlength="500"
                                   style="font-size: 12px;">
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($pregunta) ? 'Actualizar Pregunta' : 'Guardar Pregunta' ?>
                </button>
                <a href="<?= BASE_URL ?>actividad/parteForm/<?= $encodedActividad ?>/<?= $encodedParte ?>" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formPregunta');
    if (form) FormValidation.init('#formPregunta');
});
</script>