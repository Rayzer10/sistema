<div class="card-custom">
    <div class="card-header-custom">
        <h4>Niveles de Contenido</h4>
        <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">
            Los 4 niveles son fijos. El desbloqueo del nivel N+1 requiere completar al menos
            el <strong style="color:#FFFFFF;"><?= (int)$niveles[1]['porcentaje_desbloqueo'] ?? 80 ?>%</strong> del nivel anterior.
        </p>
    </div>
    <div class="card-body-custom">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
            <?php
            $colors = ['#5E6AD2', '#28A745', '#FF9800', '#DC3545'];
            foreach ($niveles as $i => $n):
                $color = $colors[$i] ?? '#5E6AD2';
            ?>
                <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 20px; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: <?= $color ?>; opacity: 0.08; border-radius: 0 12px 0 100%;"></div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; position: relative;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: <?= $color ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px;">
                            <?= (int)$n['numero'] ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h5 style="color: #FFFFFF; font-size: 15px; font-weight: 500; margin: 0;"><?= htmlspecialchars($n['nombre']) ?></h5>
                            <p style="color: #8A8F98; font-size: 12px; margin: 2px 0 0;">
                                <?= (int)$n['total_unidades'] ?> unidad(es) publicada(s)
                            </p>
                        </div>
                    </div>
                    <p style="color: #8A8F98; font-size: 13px; line-height: 1.5; margin: 0;">
                        <?= htmlspecialchars($n['descripcion'] ?? '') ?>
                    </p>
                    <?php if ($n['numero'] > 1): ?>
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 12px; color: #8A8F98;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; color: #FFC107;">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Desbloqueo al <?= (int)$n['porcentaje_desbloqueo'] ?>% del nivel anterior
                        </div>
                    <?php else: ?>
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 12px; color: #5BCB7A;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Nivel inicial (siempre desbloqueado)
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 24px; padding: 16px; background: rgba(94, 106, 210, 0.08); border: 1px solid rgba(94, 106, 210, 0.2); border-radius: 8px;">
            <p style="color: #FFFFFF; font-size: 13px; margin: 0; display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #5E6AD2;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>Los niveles son fijos del sistema. Para gestionar las unidades de cada nivel, usa el <a href="<?= BASE_URL ?>unidad" style="color: #5E6AD2; text-decoration: none; font-weight: 500;">Gestor de Contenido</a>.</span>
            </p>
        </div>
    </div>
</div>
