<?php
$calificacion = (float)$intento['calificacion'];
$aprobado = $calificacion >= 70;
$color = $aprobado ? '#5BCB7A' : '#DC3545';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>dinamica" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Dinámicas
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom" style="text-align: center; padding: 32px;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: <?= $color ?>20; color: <?= $color ?>; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <?php if ($aprobado): ?>
                    <polyline points="20 6 9 17 4 12"/>
                <?php else: ?>
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                <?php endif; ?>
            </svg>
        </div>
        <h4 style="margin: 0 0 8px; font-size: 20px; color: #FFFFFF;"><?= htmlspecialchars($intento['dinamica_titulo']) ?></h4>
        <div style="color: <?= $color ?>; font-size: 36px; font-weight: 600; margin: 12px 0;"><?= number_format($calificacion, 0) ?>%</div>
        <p style="color: #8A8F98; font-size: 14px; margin: 0;">
            <?= (int)$intento['total_correctas'] ?> de <?= (int)$intento['total_preguntas'] ?> respuestas correctas
        </p>
    </div>
    <div class="card-body-custom">
        <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
            Revisión de respuestas
        </h5>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($intento['respuestas'] as $i => $r): ?>
                <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px 14px;">
                    <div style="display: flex; align-items: start; gap: 10px;">
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: <?= $r['correcta'] ? 'rgba(40,167,69,0.15)' : 'rgba(220,53,69,0.15)' ?>; color: <?= $r['correcta'] ? '#5BCB7A' : '#DC3545' ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 600;">
                            <?= $i + 1 ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: #FFFFFF; font-size: 13px; font-weight: 500; margin-bottom: 6px;">
                                <?= htmlspecialchars($r['enunciado']) ?>
                            </div>
                            <div style="color: <?= $r['correcta'] ? '#5BCB7A' : '#DC3545' ?>; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                                <?php if ($r['correcta']): ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                <?php else: ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                <?php endif; ?>
                                <?= htmlspecialchars($r['opcion_texto']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 24px;">
            <a href="<?= BASE_URL ?>dinamica/jugar/<?= $this->encodeId($intento['id_dinamica']) ?>" class="btn-primary-custom" style="text-decoration: none; padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Intentar de nuevo
            </a>
            <a href="<?= BASE_URL ?>dinamica" class="btn-outline-secondary" style="text-decoration: none; padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px;">
                Ver más dinámicas
            </a>
        </div>
    </div>
</div>
