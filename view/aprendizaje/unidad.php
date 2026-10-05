<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>aprendizaje/nivel/<?= $this->encodeId($unidad['id_nivel']) ?>" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a <?= htmlspecialchars($unidad['nivel']) ?>
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <div style="display: flex; align-items: start; justify-content: space-between; gap: 16px;">
            <div style="flex: 1;">
                <h4><?= htmlspecialchars($unidad['titulo']) ?></h4>
                <?php if (!empty($unidad['descripcion'])): ?>
                    <p style="color:#8A8F98; font-size:13px; margin: 8px 0 0;">
                        <?= htmlspecialchars($unidad['descripcion']) ?>
                    </p>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($completada): ?>
                    <span class="badge-estado badge-activo" style="font-size: 13px; padding: 6px 12px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align: middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        Completada
                    </span>
                <?php else: ?>
                    <span class="badge-estado badge-deshabilitado" style="font-size: 13px; padding: 6px 12px;">Pendiente</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <?php if (empty($archivos) && empty($unidad['contenido_html'])): ?>
            <div class="empty-state">
                <h5 style="color:#8A8F98;">Esta unidad no tiene contenido</h5>
                <p>Pídele al docente que suba el material correspondiente.</p>
            </div>
        <?php else: ?>

            <?php
            $archivosPorTipo = [];
            foreach ($archivos as $a) {
                $archivosPorTipo[$a['tipo']] = $a;
            }
            ?>

            <?php if (!empty($archivosPorTipo['mp4'])): ?>
                <div style="margin-bottom: 24px;">
                    <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#5E6AD2;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        Video
                    </h5>
                    <video controls style="width: 100%; max-height: 480px; border-radius: 8px; background: #000;">
                        <source src="<?= BASE_URL . htmlspecialchars($archivosPorTipo['mp4']['ruta']) ?>" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>
                </div>
            <?php endif; ?>

            <?php if (!empty($unidad['contenido_html'])): ?>
                <div style="margin-bottom: 24px;">
                    <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#28A745;"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Contenido
                    </h5>
                    <div class="contenido-html">
                        <?= $unidad['contenido_html'] ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($archivosPorTipo['pdf']) || !empty($archivosPorTipo['pptx'])): ?>
                <div style="margin-bottom: 24px;">
                    <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                        Archivos descargables
                    </h5>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <?php if (!empty($archivosPorTipo['pdf'])): ?>
                            <a href="<?= BASE_URL . htmlspecialchars($archivosPorTipo['pdf']['ruta']) ?>" target="_blank" class="archivo-link" style="text-decoration: none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#DC3545;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <div style="flex:1;min-width:0;">
                                    <div style="color:#FFFFFF;font-size:13px;font-weight:500;">Documento PDF</div>
                                    <div style="color:#8A8F98;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($archivosPorTipo['pdf']['nombre_original']) ?></div>
                                </div>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#8A8F98;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($archivosPorTipo['pptx'])): ?>
                            <a href="<?= BASE_URL . htmlspecialchars($archivosPorTipo['pptx']['ruta']) ?>" target="_blank" class="archivo-link" style="text-decoration: none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#FF9800;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/></svg>
                                <div style="flex:1;min-width:0;">
                                    <div style="color:#FFFFFF;font-size:13px;font-weight:500;">Presentación</div>
                                    <div style="color:#8A8F98;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($archivosPorTipo['pptx']['nombre_original']) ?></div>
                                </div>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#8A8F98;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; gap: 10px; align-items: center; justify-content: space-between;">
            <div style="color: #8A8F98; font-size: 13px;">
                <?php if ($completada): ?>
                    Marca esta unidad como completada al haber revisado todo el material.
                <?php else: ?>
                    Al terminar de revisar el material, marca la unidad como completada.
                <?php endif; ?>
            </div>
            <form action="<?= BASE_URL ?>aprendizaje/completar/<?= $encodedId ?>" method="POST" style="display: inline;">
                <?php if ($completada): ?>
                    <input type="hidden" name="accion" value="desmarcar">
                    <button type="submit" class="btn-outline-secondary" style="padding: 10px 18px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/></svg>
                        Desmarcar
                    </button>
                <?php else: ?>
                    <input type="hidden" name="accion" value="completar">
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 18px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        Marcar como completada
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<style>
.contenido-html {
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 20px 24px;
    color: #FFFFFF;
    line-height: 1.6;
}
.contenido-html h1, .contenido-html h2, .contenido-html h3 { color: #FFFFFF; margin: 16px 0 8px; }
.contenido-html p { margin: 0 0 12px; }
.contenido-html a { color: #5E6AD2; }
.contenido-html code { background: #1B1B1F; padding: 2px 6px; border-radius: 4px; font-size: 12px; color: #FF9800; }
.contenido-html pre { background: #1B1B1F; padding: 12px; border-radius: 6px; overflow-x: auto; }
.contenido-html img { max-width: 100%; border-radius: 6px; }
.contenido-html table { width: 100%; border-collapse: collapse; }
.contenido-html th, .contenido-html td { padding: 8px 12px; border: 1px solid rgba(255,255,255,0.06); }
.contenido-html th { background: rgba(255,255,255,0.03); }
.archivo-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    transition: border-color 0.15s;
}
.archivo-link:hover { border-color: rgba(255,255,255,0.15); }
</style>
