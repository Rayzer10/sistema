<?php
$esCorrecta = $opcion['es_correcta'] == 1;
$color = $esCorrecta ? '#5BCB7A' : '#DC3545';
$bgColor = $esCorrecta ? 'rgba(40, 167, 69, 0.08)' : 'rgba(220, 53, 69, 0.08)';
$borderColor = $esCorrecta ? 'rgba(40, 167, 69, 0.3)' : 'rgba(220, 53, 69, 0.3)';
$icon = $esCorrecta
    ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
    : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
$titulo = $esCorrecta ? '¡Correcto!' : 'Incorrecto';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>dinamica/jugar/<?= $encodedId ?>" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Continuar
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom" style="background: <?= $bgColor ?>; border-bottom: 1px solid <?= $borderColor ?>;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: <?= $color ?>; color: #FFFFFF; display: flex; align-items: center; justify-content: center;">
                <?= $icon ?>
            </div>
            <div>
                <h4 style="margin: 0; color: <?= $color ?>;"><?= $titulo ?></h4>
                <p style="color: #8A8F98; font-size: 13px; margin: 4px 0 0;">Tu respuesta fue registrada</p>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div style="background: rgba(94, 106, 210, 0.08); border-left: 3px solid #5E6AD2; padding: 14px 18px; border-radius: 0 8px 8px 0; margin-bottom: 16px;">
            <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Tu respuesta</div>
            <div style="color: #FFFFFF; font-size: 14px; font-weight: 500;">
                <?= htmlspecialchars($opcion['texto']) ?>
            </div>
        </div>

        <?php if (!empty($opcion['retroalimentacion'])): ?>
            <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
                <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Retroalimentación
                </div>
                <div style="color: #FFFFFF; font-size: 13px; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($opcion['retroalimentacion'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="<?= BASE_URL ?>dinamica/jugar/<?= $encodedId ?>" class="btn-primary-custom" style="text-decoration: none; padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px;">
                Siguiente pregunta
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</div>
