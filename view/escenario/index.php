<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Simulador de Escenarios</h2>
        <p>Resuelve los escenarios asignados por tu docente y recibe retroalimentación.</p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
</div>

<?php
$estadosLabel = [
    1 => ['label' => 'Pendiente', 'class' => 'badge-deshabilitado', 'icon' => '⏳'],
    2 => ['label' => 'Entregado', 'class' => 'badge-docente', 'icon' => '📤'],
    3 => ['label' => 'Evaluado', 'class' => 'badge-activo', 'icon' => '✓']
];
?>

<?php if (empty($asignaciones)): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                <h5>No tienes escenarios asignados</h5>
                <p>Cuando el docente te asigne un escenario aparecerá aquí.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
        <?php foreach ($asignaciones as $a):
            $encId = $this->encodeId($a['id']);
            $estado = (int)$a['estado'];
            $info = $estadosLabel[$estado] ?? $estadosLabel[1];
        ?>
            <a href="<?= BASE_URL ?>escenario/ver/<?= $encId ?>" class="escenario-card" style="text-decoration: none;">
                <div style="display: flex; align-items: start; justify-content: space-between; gap: 10px; margin-bottom: 10px;">
                    <h5 style="color: #FFFFFF; font-size: 14px; font-weight: 500; margin: 0; flex: 1;">
                        <?= htmlspecialchars($a['titulo']) ?>
                    </h5>
                    <span class="badge-rol <?= $info['class'] ?>"><?= $info['icon'] ?> <?= $info['label'] ?></span>
                </div>
                <p style="color: #8A8F98; font-size: 12px; line-height: 1.5; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?= htmlspecialchars(mb_substr($a['contexto'], 0, 150)) ?>...
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.04);">
                    <span style="color: #8A8F98; font-size: 11px;">
                        <span class="badge-rol badge-docente" style="font-size: 10px;"><?= htmlspecialchars($a['nivel']) ?></span>
                    </span>
                    <span style="color: #8A8F98; font-size: 11px;">
                        Asignado <?= date('d/m/Y', strtotime($a['fecha_asignacion'])) ?>
                    </span>
                </div>
                <?php if ($estado == 3 && $a['calificacion'] !== null): ?>
                    <div style="margin-top: 10px; padding: 8px 10px; background: rgba(40,167,69,0.08); border-radius: 6px; border-left: 2px solid #5BCB7A;">
                        <div style="color: #5BCB7A; font-size: 12px; font-weight: 500;">
                            Calificación: <?= number_format((float)$a['calificacion'], 1) ?> / 100
                        </div>
                    </div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.escenario-card {
    background: #1B1B1F;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 14px;
    display: block;
    transition: border-color 0.15s, transform 0.15s;
}
.escenario-card:hover {
    border-color: rgba(94,106,210,0.4);
    transform: translateY(-1px);
}
</style>
