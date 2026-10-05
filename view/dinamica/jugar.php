<?php
// Buscar la primera pregunta sin responder
$db = \Database::getInstance();
$respondidasRows = $db->fetchAll("SELECT id_pregunta FROM respuesta_intento WHERE id_intento = ?", [$intento['id']]);
$respondidas = array_map(fn($r) => (int)$r['id_pregunta'], $respondidasRows);

$preguntaActual = null;
foreach ($dinamica['preguntas'] as $p) {
    if (!in_array((int)$p['id'], $respondidas, true)) {
        $preguntaActual = $p;
        break;
    }
}

$todasRespondidas = $preguntaActual === null;
$numeroRespondidas = count($respondidas);
$totalPreguntas = count($dinamica['preguntas']);
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>dinamica" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Dinámicas
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <div style="display: flex; align-items: start; justify-content: space-between; gap: 16px;">
            <div style="flex: 1;">
                <h4><?= htmlspecialchars($dinamica['titulo']) ?></h4>
                <?php if (!empty($dinamica['descripcion'])): ?>
                    <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;"><?= htmlspecialchars($dinamica['descripcion']) ?></p>
                <?php endif; ?>
            </div>
            <div style="text-align: right;">
                <span class="badge-rol badge-docente"><?= htmlspecialchars($dinamica['nivel']) ?></span>
            </div>
        </div>
        <div style="margin-top: 12px;">
            <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.06); border-radius: 2px; overflow: hidden;">
                <div style="width: <?= $totalPreguntas > 0 ? round(($numeroRespondidas / $totalPreguntas) * 100) : 0 ?>%; height: 100%; background: #5E6AD2; transition: width 0.3s;"></div>
            </div>
            <div style="color: #8A8F98; font-size: 12px; margin-top: 6px;">Pregunta <?= $todasRespondidas ? $totalPreguntas : ($numeroRespondidas + 1) ?> de <?= $totalPreguntas ?></div>
        </div>
    </div>
    <div class="card-body-custom">
        <?php if ($todasRespondidas): ?>
            <div style="text-align: center; padding: 20px 0;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #5BCB7A; margin-bottom: 12px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h5 style="color: #FFFFFF; font-size: 16px; font-weight: 500; margin-bottom: 6px;">Has respondido todas las preguntas</h5>
                <p style="color: #8A8F98; font-size: 13px; margin-bottom: 24px;">Finaliza para ver tu calificación.</p>

                <form action="<?= BASE_URL ?>dinamica/finalizar/<?= $encodedId ?>" method="POST">
                    <input type="hidden" name="id_intento" value="<?= (int)$intento['id'] ?>">
                    <button type="submit" class="btn-primary-custom" style="padding: 12px 24px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Finalizar y ver resultado
                    </button>
                </form>
            </div>
        <?php else: ?>
            <form action="<?= BASE_URL ?>dinamica/responder/<?= $encodedId ?>" method="POST" id="formDinamica">
                <input type="hidden" name="id_intento" value="<?= (int)$intento['id'] ?>">
                <input type="hidden" name="id_pregunta" value="<?= (int)$preguntaActual['id'] ?>">

                <div style="background: rgba(94, 106, 210, 0.08); border-left: 3px solid #5E6AD2; padding: 14px 18px; border-radius: 0 8px 8px 0; margin-bottom: 20px;">
                    <div style="color: #8A8F98; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Pregunta</div>
                    <div style="color: #FFFFFF; font-size: 15px; font-weight: 500; line-height: 1.5;">
                        <?= nl2br(htmlspecialchars($preguntaActual['enunciado'])) ?>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    <?php foreach ($preguntaActual['opciones'] as $o): ?>
                        <label class="opcion-dinamica">
                            <input type="radio" name="id_opcion" value="<?= (int)$o['id'] ?>" required>
                            <span><?= htmlspecialchars($o['texto']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px;">
                        Responder
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<style>
.opcion-dinamica {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
}
.opcion-dinamica:hover {
    border-color: rgba(94, 106, 210, 0.4);
    background: rgba(94, 106, 210, 0.04);
}
.opcion-dinamica input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #5E6AD2;
    cursor: pointer;
    flex-shrink: 0;
}
.opcion-dinamica input[type="radio"]:checked + span {
    color: #FFFFFF;
    font-weight: 500;
}
.opcion-dinamica:has(input:checked) {
    border-color: #5E6AD2;
    background: rgba(94, 106, 210, 0.08);
}
.opcion-dinamica span {
    color: #FFFFFF;
    font-size: 14px;
    flex: 1;
    line-height: 1.4;
}
</style>
