<div class="welcome-banner">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="welcome-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
        </div>
        <div class="welcome-text">
            <h2>Mi Progreso</h2>
            <p>Resumen de tu avance en el Centro de Aprendizaje, Laboratorio de Dinámicas y Simulador de Escenarios.</p>
        </div>
    </div>
    <div style="margin-left: auto; align-self: center;">
        <a href="<?= BASE_URL ?>reporte/miReporte" target="_blank" class="btn-primary-custom" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Descargar PDF
        </a>
    </div>
</div>

<!-- KPIs -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
    <div class="kpi-card" style="--kpi-color: #5E6AD2;">
        <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Escenarios asignados</div>
        <div style="color: #FFFFFF; font-size: 28px; font-weight: 600; line-height: 1;"><?= (int)$totalAsignados ?></div>
    </div>
    <div class="kpi-card" style="--kpi-color: #FF9800;">
        <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Entregados</div>
        <div style="color: #FFFFFF; font-size: 28px; font-weight: 600; line-height: 1;"><?= (int)$totalEntregados ?></div>
    </div>
    <div class="kpi-card" style="--kpi-color: #5BCB7A;">
        <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Evaluados</div>
        <div style="color: #FFFFFF; font-size: 28px; font-weight: 600; line-height: 1;"><?= (int)$totalEvaluados ?></div>
    </div>
    <div class="kpi-card" style="--kpi-color: #DC3545;">
        <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Promedio escenarios</div>
        <div style="color: #FFFFFF; font-size: 28px; font-weight: 600; line-height: 1;"><?= $promedioCalif > 0 ? number_format($promedioCalif, 1) : '—' ?></div>
    </div>
</div>

<!-- Mapa de niveles -->
<div class="card-custom" style="margin-bottom: 24px;">
    <div class="card-header-custom">
        <h4>Mapa de Niveles</h4>
        <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">Tu avance en las unidades del Centro de Aprendizaje.</p>
    </div>
    <div class="card-body-custom">
        <?php
        $colors = ['#5E6AD2', '#28A745', '#FF9800', '#DC3545'];
        foreach ($niveles as $i => $nivel):
            $r = $porNivel[(int)$nivel['id']] ?? null;
            $color = $colors[$i] ?? '#5E6AD2';
            $desbloqueado = $r['desbloqueado'] ?? false;
            $porcentaje = $r['porcentaje'] ?? 0;
            $total = (int)($r['total_unidades'] ?? 0);
            $completadas = (int)($r['unidades_completadas'] ?? 0);
            $completo = $total > 0 && $completadas === $total;
        ?>
            <div class="nivel-progress-row" <?= !$desbloqueado ? 'data-bloqueado="1"' : '' ?>>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: <?= $desbloqueado ? $color : 'rgba(255,255,255,0.04)' ?>; color: <?= $desbloqueado ? 'white' : '#4A4B52' ?>; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; flex-shrink: 0;">
                    <?= (int)$nivel['numero'] ?>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 6px;">
                        <div style="color: <?= $desbloqueado ? '#FFFFFF' : '#8A8F98' ?>; font-size: 14px; font-weight: 500;">
                            <?= htmlspecialchars($nivel['nombre']) ?>
                            <?php if ($completo): ?>
                                <span class="badge-estado badge-activo" style="font-size: 10px; margin-left: 6px;">Completo</span>
                            <?php elseif (!$desbloqueado): ?>
                                <span class="badge-estado badge-deshabilitado" style="font-size: 10px; margin-left: 6px;">Bloqueado</span>
                            <?php endif; ?>
                        </div>
                        <div style="color: #8A8F98; font-size: 12px; white-space: nowrap;">
                            <?= $completadas ?> / <?= $total ?>
                        </div>
                    </div>
                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                        <div style="width: <?= $porcentaje ?>%; height: 100%; background: <?= $color ?>; transition: width 0.4s;"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
    <!-- Historial de dinámicas -->
    <div class="card-custom">
        <div class="card-header-custom">
            <h4 style="font-size: 14px;">Historial de Dinámicas</h4>
        </div>
        <div class="card-body-custom" style="padding: 0;">
            <?php if (empty($intentos)): ?>
                <div style="padding: 32px; text-align: center; color: #4A4B52;">
                    <p style="font-size: 13px;">Aún no has intentado ninguna dinámica</p>
                </div>
            <?php else: ?>
                <div style="max-height: 360px; overflow-y: auto;">
                    <?php foreach ($intentos as $i => $it): ?>
                        <div style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; <?= $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.04);' : '' ?>">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: <?= $it['mejor'] >= 70 ? 'rgba(40,167,69,0.15)' : 'rgba(255,152,0,0.15)' ?>; color: <?= $it['mejor'] >= 70 ? '#5BCB7A' : '#FF9800' ?>; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">
                                <?= number_format((float)$it['mejor'], 0) ?>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="color: #FFFFFF; font-size: 13px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($it['titulo']) ?></div>
                                <div style="color: #8A8F98; font-size: 11px;"><?= htmlspecialchars($it['nivel']) ?> &middot; <?= (int)$it['intentos'] ?> intento(s)</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historial de escenarios -->
    <div class="card-custom">
        <div class="card-header-custom">
            <h4 style="font-size: 14px;">Historial de Escenarios</h4>
        </div>
        <div class="card-body-custom" style="padding: 0;">
            <?php if (empty($asignaciones)): ?>
                <div style="padding: 32px; text-align: center; color: #4A4B52;">
                    <p style="font-size: 13px;">No tienes escenarios asignados</p>
                </div>
            <?php else: ?>
                <div style="max-height: 360px; overflow-y: auto;">
                    <?php foreach ($asignaciones as $i => $a):
                        $estado = (int)$a['estado'];
                        $bgEstado = $estado == 3 ? 'rgba(40,167,69,0.15)' : ($estado == 2 ? 'rgba(94,106,210,0.15)' : 'rgba(255,255,255,0.05)');
                        $colorEstado = $estado == 3 ? '#5BCB7A' : ($estado == 2 ? '#5E6AD2' : '#8A8F98');
                        $txtEstado = $estado == 3 ? '✓' : ($estado == 2 ? '↗' : '⏳');
                    ?>
                        <div style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; <?= $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.04);' : '' ?>">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: <?= $bgEstado ?>; color: <?= $colorEstado ?>; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                <?= $txtEstado ?>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="color: #FFFFFF; font-size: 13px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($a['titulo']) ?></div>
                                <div style="color: #8A8F98; font-size: 11px;">
                                    <?= htmlspecialchars($a['nivel']) ?>
                                    <?php if ($a['calificacion'] !== null): ?>
                                        &middot; <span style="color: #5BCB7A;">Calif: <?= number_format((float)$a['calificacion'], 1) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.kpi-card {
    background: #1B1B1F;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 16px;
    position: relative;
    overflow: hidden;
}
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--kpi-color);
}
.nivel-progress-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.nivel-progress-row:last-child {
    border-bottom: none;
}
.nivel-progress-row[data-bloqueado="1"] {
    opacity: 0.6;
}
</style>
