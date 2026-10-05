<div class="welcome-banner">
    <div class="welcome-text">
        <h2><?= htmlspecialchars($actividad['titulo']) ?></h2>
        <p>Parte <?= (int)$parte['orden'] ?> &middot; <?= htmlspecialchars($parte['titulo']) ?></p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
</div>

<div id="timerBar" style="position: sticky; top: 10px; z-index: 5; background: #1B1B1F; border: 1px solid rgba(94,106,210,0.4); border-radius: 10px; padding: 12px 18px; margin-bottom: 20px; display: <?= ($tiempoRestante ?? 0) > 0 ? 'flex' : 'none' ?>; align-items: center; justify-content: space-between;">
    <span style="color: #8A8F98; font-size: 13px;">Tiempo restante</span>
    <span id="timerDisplay" style="font-size: 20px; font-weight: 700; color: #8B93F0; font-variant-numeric: tabular-nums;">--:--</span>
</div>

<?php if (!empty($parte['contenido_html'])): ?>
    <div class="card-custom" style="margin-bottom: 20px;">
        <div class="card-header-custom">
            <h4 style="font-size: 14px;">Contenido introductorio</h4>
        </div>
        <div class="card-body-custom" style="font-size: 14px; line-height: 1.7; color: #D1D3D8;">
            <?= $parte['contenido_html'] ?>
        </div>
    </div>
<?php endif; ?>

<form action="<?= BASE_URL ?>actividad/entregar/<?= $encodedActividad ?>/<?= $encodedParte ?>" method="POST" id="formEvaluacion">
    <input type="hidden" name="tiempo_usado" id="tiempoUsado" value="0">

    <div class="card-custom">
        <div class="card-header-custom">
            <h4>Evaluación</h4>
            <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">Responde todas las preguntas antes de enviar.</p>
        </div>
        <div class="card-body-custom" style="padding: 0;">
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($preguntas as $i => $q): ?>
                    <div style="padding: 20px; <?= $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.06);' : '' ?>">
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <span style="width: 26px; height: 26px; border-radius: 6px; background: rgba(94,106,210,0.15); color: #8B93F0; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;"><?= $i + 1 ?></span>
                            <div style="flex: 1;">
                                <div style="color: #FFFFFF; font-size: 14px; font-weight: 500; line-height: 1.5;"><?= htmlspecialchars($q['enunciado']) ?></div>
                                <div style="color: #4A4B52; font-size: 11px; margin-top: 4px;"><?= (int)$q['puntos'] ?> punto(s)</div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 14px; margin-left: 36px;">
                            <?php foreach ($q['opciones'] as $op): ?>
                                <label style="display: flex; align-items: center; gap: 10px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 14px; cursor: pointer; font-size: 13px; user-select: none; transition: border-color 0.15s;">
                                    <input type="radio"
                                           name="respuestas[<?= (int)$q['id'] ?>]"
                                           value="<?= (int)$op['id'] ?>"
                                           style="width: 16px; height: 16px; accent-color: #5E6AD2; flex-shrink: 0;"
                                           required>
                                    <span style="color: #D1D3D8;"><?= htmlspecialchars($op['texto']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; font-size: 14px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Enviar evaluación
        </button>
        <button type="button" id="btnCancelarEval" class="btn-outline-secondary" style="padding: 10px 18px; font-size: 14px;">
            Salir sin guardar
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formEvaluacion');
    var btnCancelar = document.getElementById('btnCancelarEval');
    var cancelado = false;

    btnCancelar.addEventListener('click', function() {
        swalTheme.fire({
            title: '¿Salir de la evaluación?',
            text: 'Las respuestas de esta parte no se guardarán.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Seguir'
        }).then(function(r) {
            if (r.isConfirmed) {
                cancelado = true;
                window.location.href = '<?= BASE_URL ?>actividad/ver/<?= $encodedActividad ?>';
            }
        });
    });

    form.addEventListener('submit', function() {
        var usado = document.getElementById('tiempoUsado');
        if (!usado.value || usado.value < 0) usado.value = 0;
    });

    var total = <?= (int)($tiempoRestante ?? 0) ?>;
    if (total > 0) {
        var inicio = Date.now();
        var display = document.getElementById('timerDisplay');

        function tick() {
            var restante = total - Math.floor((Date.now() - inicio) / 1000);
            document.getElementById('tiempoUsado').value = Math.max(0, total - restante);
            if (restante <= 0) {
                display.textContent = '00:00';
                if (!cancelado) {
                    swalTheme.fire({
                        title: 'Tiempo agotado',
                        text: 'Tu evaluación se enviará automáticamente.',
                        icon: 'info',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        form.submit();
                    });
                }
                return;
            }
            var m = Math.floor(restante / 60);
            var s = restante % 60;
            display.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            if (restante <= 60) display.style.color = '#EF4444';
            setTimeout(tick, 500);
        }
        tick();
    }
});
</script>