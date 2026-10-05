<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Resultado de la Actividad</h2>
        <p><?= htmlspecialchars($actividad['titulo']) ?></p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
</div>

<?php if ($calificacion === null): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h5>Aún no has terminado esta actividad</h5>
                <p>Completa todas las partes para ver tu calificación.</p>
                <a href="<?= BASE_URL ?>actividad/ver/<?= $encodedId ?>" class="btn-primary-custom" style="margin-top: 16px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    Continuar
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 20px 0;">
                <div style="width: 84px; height: 84px; border-radius: 50%; background: rgba(94,106,210,0.15); color: #8B93F0; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; margin-bottom: 16px;">
                    <?= number_format((float)$calificacion, 1, '.', '') ?>
                </div>
                <h5 style="color: #FFFFFF; font-size: 16px;"><?= $aprobado ? '¡Aprobado!' : 'Sigue practicando' ?></h5>
                <p style="color: #8A8F98; font-size: 13px; margin: 8px 0 0;">Calificación final de la actividad</p>
            </div>

            <hr style="border-color: rgba(255,255,255,0.06); margin: 16px 0;">

            <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                Detalle por parte
            </h5>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($detalle as $d): ?>
                    <div style="display: flex; align-items: center; gap: 14px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px 14px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; flex-shrink: 0; background: rgba(94,106,210,0.15); color: #8B93F0;">
                            <?= (int)$d['orden'] ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: #FFFFFF; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($d['titulo']) ?></div>
                            <div style="color: #8A8F98; font-size: 12px; margin-top: 2px;">
                                <?= (int)$d['puntos_obtenidos'] ?> / <?= (int)$d['total_puntos'] ?> puntos
                            </div>
                        </div>
                        <?php
                            $pct = $d['total_puntos'] > 0 ? (100 * $d['puntos_obtenidos'] / $d['total_puntos']) : 0;
                        ?>
                        <div style="width: 100px; height: 6px; border-radius: 4px; background: rgba(255,255,255,0.08); overflow: hidden; flex-shrink: 0;">
                            <div style="width: <?= number_format($pct, 1, '.', '') ?>%; height: 100%; border-radius: 4px; background: <?= $pct >= 60 ? '#22C55E' : '#F59E0B' ?>;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <a href="<?= BASE_URL ?>actividad" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px;">
                    Volver a mis actividades
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>