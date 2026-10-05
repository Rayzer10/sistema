<?php
$estado = (int)$asignacion['estado'];
$entrega = $asignacion['entrega'];
$evaluacion = $asignacion['evaluacion'] ?? null;
$entregado = !empty($entrega);
$evaluado = $estado == 3;
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>escenario" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Escenarios
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <div style="display: flex; align-items: start; justify-content: space-between; gap: 16px;">
            <div style="flex: 1;">
                <h4><?= htmlspecialchars($asignacion['titulo']) ?></h4>
                <p style="color:#8A8F98; font-size:12px; margin: 6px 0 0;">
                    Asignado el <?= date('d/m/Y H:i', strtotime($asignacion['fecha_asignacion'])) ?>
                    <?php if (!empty($asignacion['fecha_limite'])): ?>
                        &middot; Límite: <?= date('d/m/Y', strtotime($asignacion['fecha_limite'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <span class="badge-rol badge-docente"><?= htmlspecialchars($asignacion['nivel']) ?></span>
        </div>
    </div>
    <div class="card-body-custom">
        <!-- Contexto -->
        <div style="margin-bottom: 20px;">
            <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Contexto</h5>
            <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; color: #FFFFFF; font-size: 14px; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($asignacion['contexto'])) ?>
            </div>
        </div>

        <!-- Problema -->
        <div style="margin-bottom: 20px;">
            <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Planteamiento del problema</h5>
            <div style="background: rgba(94, 106, 210, 0.08); border-left: 3px solid #5E6AD2; padding: 14px 18px; border-radius: 0 8px 8px 0; color: #FFFFFF; font-size: 14px; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($asignacion['problema'])) ?>
            </div>
        </div>

        <!-- Entregables esperados -->
        <?php if (!empty($asignacion['entregables_esperados'])): ?>
            <div style="margin-bottom: 20px;">
                <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Entregables esperados</h5>
                <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; color: #FFFFFF; font-size: 14px; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($asignacion['entregables_esperados'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Observaciones del docente -->
        <?php if (!empty($asignacion['observaciones_docente'])): ?>
            <div style="margin-bottom: 20px;">
                <h5 style="font-size: 12px; font-weight: 600; color: #FFC107; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Indicaciones del docente
                </h5>
                <div style="background: rgba(255, 193, 7, 0.08); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 8px; padding: 12px 16px; color: #FFFFFF; font-size: 13px; line-height: 1.5;">
                    <?= nl2br(htmlspecialchars($asignacion['observaciones_docente'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

        <!-- Estado de la entrega -->
        <?php if ($evaluado && $evaluacion): ?>
            <div style="background: rgba(40, 167, 69, 0.08); border: 1px solid rgba(40, 167, 69, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #5BCB7A; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px;">
                        <?= number_format((float)$evaluacion['calificacion'], 0) ?>
                    </div>
                    <div>
                        <h5 style="color: #FFFFFF; font-size: 16px; font-weight: 500; margin: 0;">Evaluado por el docente</h5>
                        <p style="color: #8A8F98; font-size: 12px; margin: 2px 0 0;">Calificación sobre 100 &middot; <?= date('d/m/Y H:i', strtotime($evaluacion['fecha_evaluacion'])) ?></p>
                    </div>
                </div>
                <?php if (!empty($evaluacion['retroalimentacion'])): ?>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(40,167,69,0.2);">
                        <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Retroalimentación</div>
                        <div style="color: #FFFFFF; font-size: 14px; line-height: 1.6;"><?= nl2br(htmlspecialchars($evaluacion['retroalimentacion'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Tu respuesta</h5>
            <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; color: #FFFFFF; font-size: 13px; line-height: 1.6; margin-bottom: 12px;">
                <?= nl2br(htmlspecialchars($entrega['contenido'])) ?>
            </div>
            <?php if (!empty($entrega['archivo_nombre'])): ?>
                <a href="<?= BASE_URL . htmlspecialchars($entrega['archivo_ruta']) ?>" target="_blank" class="archivo-link" style="text-decoration: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#5E6AD2;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                    <div style="flex:1; min-width:0;">
                        <div style="color:#FFFFFF; font-size:13px; font-weight:500;">Archivo adjunto</div>
                        <div style="color:#8A8F98; font-size:11px;"><?= htmlspecialchars($entrega['archivo_nombre']) ?></div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#8A8F98;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </a>
            <?php endif; ?>

        <?php elseif ($entregado): ?>
            <div style="background: rgba(94, 106, 210, 0.08); border: 1px solid rgba(94, 106, 210, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; color: #FFFFFF; font-size: 14px; margin-bottom: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#5E6AD2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <strong>Entrega registrada</strong>
                </div>
                <p style="color: #8A8F98; font-size: 13px; margin: 0;">
                    Entregado el <?= date('d/m/Y H:i', strtotime($entrega['fecha_entrega'])) ?>.
                    Espera la evaluación del docente.
                </p>
            </div>

            <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Tu respuesta</h5>
            <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; color: #FFFFFF; font-size: 13px; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($entrega['contenido'])) ?>
            </div>
            <?php if (!empty($entrega['archivo_nombre'])): ?>
                <p style="color: #8A8F98; font-size: 12px; margin: 8px 0 0;">
                    📎 Adjunto: <?= htmlspecialchars($entrega['archivo_nombre']) ?>
                </p>
            <?php endif; ?>

        <?php else: ?>
            <!-- Formulario de entrega -->
            <form action="<?= BASE_URL ?>escenario/entregar/<?= $encodedId ?>" method="POST" enctype="multipart/form-data" id="formEntrega">
                <h5 style="font-size: 12px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Tu respuesta</h5>
                <div class="form-group">
                    <textarea class="form-control"
                              name="contenido"
                              rows="10"
                              required
                              data-minlength="20"
                              placeholder="Desarrolla tu solución al escenario aquí..."></textarea>
                    <div class="invalid-feedback">La respuesta debe tener al menos 20 caracteres.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Archivo adjunto <span style="color:#4A4B52;font-weight:400;">opcional (PDF, DOC, IMG, ZIP - máx 20 MB)</span></label>
                    <input type="file" class="form-control" name="archivo_adjunto" accept=".pdf,.doc,.docx,.odt,.txt,.rtf,.jpg,.jpeg,.png,.zip,.rar">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Enviar entrega
                    </button>
                </div>
            </form>
        <?php endif; ?>
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
    var form = document.getElementById('formEntrega');
    if (form) {
        FormValidation.init('#formEntrega');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            swalTheme.fire({
                title: '¿Enviar entrega?',
                html: 'Una vez enviada, no podrás modificar tu respuesta hasta que el docente la evalúe.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then(function(r) {
                if (r.isConfirmed) {
                    var btn = form.querySelector('button[type="submit"]');
                    if (btn) { btn.disabled = true; btn.textContent = 'Enviando...'; }
                    form.submit();
                }
            });
        });
    }
});
</script>
