<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Laboratorio de Dinámicas</h2>
        <p>Responde las preguntas y recibe retroalimentación automática al instante.</p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
    </div>
</div>

<?php
$colors = ['#5E6AD2', '#28A745', '#FF9800', '#DC3545'];
?>

<?php if (empty($porNivel)): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                <h5>No hay dinámicas disponibles</h5>
                <p>El docente aún no ha publicado dinámicas.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($niveles as $nivel):
        $idNivel = (int)$nivel['id'];
        if (empty($porNivel[$idNivel])) continue;
        $color = $colors[($nivel['numero'] - 1)] ?? '#5E6AD2';
        $dinamicas = $porNivel[$idNivel];
    ?>
        <div style="margin-bottom: 24px;">
            <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; width: 24px; height: 24px; border-radius: 6px; background: <?= $color ?>; color: white; text-align: center; line-height: 24px; font-size: 12px; font-weight: 600;"><?= (int)$nivel['numero'] ?></span>
                <?= htmlspecialchars($nivel['nombre']) ?>
            </h5>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <?php foreach ($dinamicas as $d):
                    $encId = $this->encodeId($d['id']);
                    $bloqueado = !$d['desbloqueado'];
                    $intentos = $d['intentos'];
                ?>
                    <?php if ($bloqueado): ?>
                        <div class="dinamica-card-bloqueada">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 6px; background: rgba(255,255,255,0.04); color: #4A4B52; display: flex; align-items: center; justify-content: center;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="color: #4A4B52; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($d['titulo']) ?></div>
                                </div>
                            </div>
                            <p style="color: #4A4B52; font-size: 12px; margin: 8px 0 0;">Completa el nivel anterior para desbloquear</p>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>dinamica/jugar/<?= $encId ?>" class="dinamica-card" style="text-decoration: none;">
                            <div style="display: flex; align-items: start; justify-content: space-between; gap: 10px; margin-bottom: 8px;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="color: #FFFFFF; font-size: 14px; font-weight: 500; margin-bottom: 2px;"><?= htmlspecialchars($d['titulo']) ?></div>
                                    <?php if (!empty($d['descripcion'])): ?>
                                        <div style="color: #8A8F98; font-size: 12px; line-height: 1.4;"><?= htmlspecialchars($d['descripcion']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($intentos && $intentos['mejor_calificacion'] !== null): ?>
                                    <?php $cal = (float)$intentos['mejor_calificacion']; ?>
                                    <span class="badge-estado <?= $cal >= 70 ? 'badge-activo' : 'badge-deshabilitado' ?>" style="font-size: 11px;">
                                        <?= number_format($cal, 0) ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($intentos && $intentos['total_intentos'] > 0): ?>
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.04); font-size: 11px; color: #8A8F98;">
                                    <?= (int)$intentos['total_intentos'] ?> intento(s) &middot; último <?= date('d/m/Y', strtotime($intentos['ultimo_intento'])) ?>
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
.dinamica-card {
    background: #1B1B1F;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 14px;
    display: block;
    transition: border-color 0.15s, transform 0.15s;
}
.dinamica-card:hover {
    border-color: rgba(94,106,210,0.4);
    transform: translateY(-1px);
}
.dinamica-card-bloqueada {
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 8px;
    padding: 14px;
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
