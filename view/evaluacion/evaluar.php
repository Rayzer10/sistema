<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>evaluacion" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Evaluaciones
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <div style="display: flex; align-items: start; justify-content: space-between; gap: 16px;">
            <div style="flex: 1;">
                <h4><?= htmlspecialchars($asignacion['titulo']) ?></h4>
                <p style="color:#8A8F98; font-size:12px; margin: 6px 0 0;">
                    Estudiante: <strong style="color:#FFFFFF;"><?= htmlspecialchars($asignacion['estudiante']) ?></strong>
                    &middot; <?= htmlspecialchars($asignacion['nivel']) ?>
                    &middot; Entregado el <?= date('d/m/Y H:i', strtotime($asignacion['fecha_entrega'])) ?>
                </p>
            </div>
            <span class="badge-rol badge-docente"><?= htmlspecialchars($asignacion['nivel']) ?></span>
        </div>
    </div>
    <div class="card-body-custom">
        <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Planteamiento del problema</h5>
        <div style="background: rgba(94, 106, 210, 0.08); border-left: 3px solid #5E6AD2; padding: 12px 16px; border-radius: 0 8px 8px 0; color: #FFFFFF; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
            <?= nl2br(htmlspecialchars($asignacion['problema'])) ?>
        </div>

        <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Entregables esperados</h5>
        <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px 16px; color: #FFFFFF; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
            <?= nl2br(htmlspecialchars($asignacion['entregables_esperados'])) ?>
        </div>

        <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

        <h5 style="font-size: 12px; font-weight: 600; color: #5BCB7A; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Respuesta del estudiante</h5>
        <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; color: #FFFFFF; font-size: 13px; line-height: 1.6; max-height: 300px; overflow-y: auto; margin-bottom: 16px;">
            <?= nl2br(htmlspecialchars($asignacion['contenido'])) ?>
        </div>

        <?php if (!empty($asignacion['archivo_nombre'])): ?>
            <a href="<?= BASE_URL . htmlspecialchars($asignacion['archivo_ruta']) ?>" target="_blank" class="archivo-link" style="text-decoration: none; margin-bottom: 20px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#5E6AD2;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                <div style="flex:1; min-width:0;">
                    <div style="color:#FFFFFF; font-size:13px; font-weight:500;">Archivo adjunto</div>
                    <div style="color:#8A8F98; font-size:11px;"><?= htmlspecialchars($asignacion['archivo_nombre']) ?></div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#8A8F98;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
        <?php endif; ?>

        <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

        <form action="<?= BASE_URL ?>evaluacion/evaluar/<?= $encodedId ?>" method="POST" id="formEvaluar">
            <h5 style="font-size: 13px; font-weight: 600; color: #FFFFFF; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">
                Tu Evaluación
            </h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Calificación (0-100) <span style="color:#DC3545;">*</span></label>
                        <input type="number"
                               class="form-control"
                               name="calificacion"
                               min="0"
                               max="100"
                               step="0.1"
                               value="<?= $asignacion['calificacion'] !== null ? htmlspecialchars($asignacion['calificacion']) : '' ?>"
                               required
                               style="font-size: 24px; font-weight: 600; text-align: center;">
                        <div class="invalid-feedback">Ingrese una calificación entre 0 y 100.</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Retroalimentación <span style="color:#DC3545;">*</span></label>
                <textarea class="form-control"
                          name="retroalimentacion"
                          rows="6"
                          required
                          data-minlength="10"
                          placeholder="Explica los puntos fuertes, areas de mejora y sugerencias..."><?= htmlspecialchars($asignacion['retroalimentacion'] ?? '') ?></textarea>
                <div class="invalid-feedback">La retroalimentación es obligatoria (mínimo 10 caracteres).</div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= $asignacion['calificacion'] !== null ? 'Actualizar evaluación' : 'Guardar evaluación' ?>
                </button>
                <a href="<?= BASE_URL ?>evaluacion" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<style>
.archivo-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    transition: border-color 0.15s;
}
.archivo-link:hover { border-color: rgba(255,255,255,0.15); }
</style>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    FormValidation.init('#formEvaluar');
});
</script>
