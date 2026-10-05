<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Centro de Aprendizaje</h2>
        <p>Progresa a través de los 4 niveles completando todas las unidades.</p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    </div>
</div>

<?php
$colors = ['#5E6AD2', '#28A745', '#FF9800', '#DC3545'];
$icons = [
    '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
    '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
    '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'
];
?>

<?php if (!empty($sinMaterias)): ?>
    <div class="card-custom">
        <div class="card-body-custom">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <h5>No tienes materias asignadas</h5>
                <p>El administrador debe asignarte a una sección para ver el contenido del centro de aprendizaje.</p>
            </div>
        </div>
    </div>
<?php else: ?>
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
    <?php foreach ($niveles as $i => $d):
        $n = $d['nivel'];
        $color = $colors[$i] ?? '#5E6AD2';
        $encId = $this->encodeId($n['id']);
        $bloqueado = !$d['desbloqueado'];
        $completo = $d['total'] > 0 && $d['completadas'] === $d['total'];
    ?>
        <?php if ($bloqueado): ?>
            <div class="nivel-card nivel-bloqueado">
                <div class="nivel-card-overlay">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.08); color: #4A4B52; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px;">
                        <?= (int)$n['numero'] ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h5 style="color: #8A8F98; font-size: 15px; font-weight: 500; margin: 0;"><?= htmlspecialchars($n['nombre']) ?></h5>
                    </div>
                </div>
                <p style="color: #4A4B52; font-size: 13px; line-height: 1.5; margin: 0;">
                    <?= htmlspecialchars($n['descripcion'] ?? '') ?>
                </p>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 12px; color: #FFC107;">
                    Completa el nivel anterior al <?= (int)$n['porcentaje_desbloqueo'] ?>% para desbloquear
                </div>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>aprendizaje/nivel/<?= $encId ?>" class="nivel-card" style="text-decoration: none; display: block;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: <?= $color ?>; color: white; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $icons[$i] ?? '' ?></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h5 style="color: #FFFFFF; font-size: 15px; font-weight: 500; margin: 0;"><?= htmlspecialchars($n['nombre']) ?></h5>
                        <p style="color: #8A8F98; font-size: 12px; margin: 2px 0 0;">
                            <?= $d['completadas'] ?> / <?= $d['total'] ?> unidades
                        </p>
                    </div>
                    <?php if ($completo): ?>
                        <span class="badge-estado badge-activo">Completo</span>
                    <?php endif; ?>
                </div>
                <p style="color: #8A8F98; font-size: 13px; line-height: 1.5; margin: 0 0 12px;">
                    <?= htmlspecialchars($n['descripcion'] ?? '') ?>
                </p>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: <?= $d['porcentaje'] ?>%; background: <?= $color ?>;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 6px; font-size: 12px;">
                    <span style="color: #8A8F98;">Progreso</span>
                    <span style="color: <?= $color ?>; font-weight: 500;"><?= $d['porcentaje'] ?>%</span>
                </div>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.nivel-card {
    background: #1B1B1F;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 20px;
    transition: border-color 0.15s, transform 0.15s;
    position: relative;
    overflow: hidden;
}
.nivel-card:hover:not(.nivel-bloqueado) {
    border-color: rgba(255,255,255,0.12);
    transform: translateY(-2px);
}
.nivel-bloqueado {
    opacity: 0.5;
    cursor: not-allowed;
}
.nivel-card-overlay {
    position: absolute;
    top: 12px;
    right: 12px;
    color: #4A4B52;
}
.progress-bar-custom {
    width: 100%;
    height: 6px;
    background: rgba(255,255,255,0.06);
    border-radius: 3px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.4s ease;
}
</style>
