<div class="welcome-banner">
    <div class="welcome-text">
        <h2><?= htmlspecialchars($actividad['titulo']) ?></h2>
        <p>
            <?= htmlspecialchars($actividad['materia']) ?>
            <?= !empty($actividad['nivel']) ? ' &middot; ' . htmlspecialchars($actividad['nivel']) : '' ?>
        </p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
</div>

<?php if (!empty($actividad['descripcion'])): ?>
    <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; color: #8A8F98; font-size: 13px; line-height: 1.6;">
        <?= htmlspecialchars($actividad['descripcion']) ?>
    </div>
<?php endif; ?>

<?php if ($finalizada): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h5>¡Actividad completada!</h5>
                <p style="color: #8A8F98; font-size: 14px; margin: 10px 0;">
                    Calificación final:
                    <span style="color: #8B93F0; font-weight: 700; font-size: 20px;"><?= number_format((float)$calificacion, 1, '.', '') ?></span>
                </p>
                <div style="display: flex; gap: 10px; margin-top: 12px;">
                    <a href="<?= BASE_URL ?>actividad/resultado/<?= $encodedId ?>" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px;">
                        Ver detalle
                    </a>
                    <a href="<?= BASE_URL ?>actividad" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px;">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
        <h5 style="color: #FFFFFF; font-size: 14px; font-weight: 600; margin: 0;">Tu progreso</h5>
        <span style="color: #8A8F98; font-size: 13px;"><?= $partesResueltas ?> de <?= $totalPartes ?> partes completadas</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
        <?php foreach ($partes as $i => $p): ?>
            <?php
                $estado = 'pendiente';
                if (in_array((int)$p['id'], $partesCompletadas)) $estado = 'completada';
                elseif ($i === $indiceActual) $estado = 'actual';
            ?>
            <div style="display: flex; align-items: center; gap: 14px; background: #0A0A0B; border: 1px solid <?= $estado === 'actual' ? 'rgba(94,106,210,0.5)' : 'rgba(255,255,255,0.06)' ?>; border-radius: 10px; padding: 14px 16px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; flex-shrink: 0;
                    <?= $estado === 'completada' ? 'background: rgba(34,197,94,0.15); color: #22C55E;' : ($estado === 'actual' ? 'background: rgba(94,106,210,0.2); color: #8B93F0;' : 'background: rgba(255,255,255,0.05); color: #4A4B52;') ?>">
                    <?= $estado === 'completada' ? '&#10003;' : (int)$p['orden'] ?>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="color: <?= $estado === 'pendiente' ? '#8A8F98' : '#FFFFFF' ?>; font-size: 14px; font-weight: 500;"><?= htmlspecialchars($p['titulo']) ?></div>
                    <div style="color: #4A4B52; font-size: 12px; margin-top: 2px;">
                        <?= (int)$p['total_preguntas'] ?> pregunta(s) &middot; <?= (int)$p['total_puntos'] ?> pts
                        <?= $p['tiempo_limite_min'] ? ' &middot; ' . (int)$p['tiempo_limite_min'] . ' min' : '' ?>
                    </div>
                </div>
                <?php if ($estado === 'actual'): ?>
                    <a href="<?= BASE_URL ?>actividad/evaluacion/<?= $encodedId ?>/<?= $this->encodeId($p['id']) ?>" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px;">
                        Continuar
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                <?php elseif ($estado === 'completada'): ?>
                    <span style="color: #22C55E; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Completada
                    </span>
                <?php else: ?>
                    <span style="color: #4A4B52; font-size: 12px;">Bloqueada</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="color: #4A4B52; font-size: 12px; margin-bottom: 24px;">
        Completa cada parte en orden. Al terminar la evaluación de una parte, la siguiente se desbloqueará.
    </div>

    <a href="<?= BASE_URL ?>actividad" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a mis actividades
    </a>

<?php endif; ?>