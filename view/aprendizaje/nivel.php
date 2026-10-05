<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>aprendizaje" style="color: #8A8F98; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Niveles
    </a>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= htmlspecialchars($nivel['nombre']) ?></h4>
        <p style="color:#8A8F98; font-size:13px; margin: 6px 0 0;">
            <?= htmlspecialchars($nivel['descripcion'] ?? '') ?>
        </p>
    </div>
    <div class="card-body-custom">
        <?php if (empty($unidades)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2A2B32; margin-bottom: 16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h5>No hay unidades en este nivel</h5>
                <p>El docente aún no ha publicado contenido para este nivel.</p>
            </div>
        <?php else: ?>
            <div style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; overflow: hidden;">
                <?php foreach ($unidades as $i => $u):
                    $encU = $this->encodeId($u['id']);
                    $completa = in_array((int)$u['id'], array_map('intval', $completadas), true);
                ?>
                    <a href="<?= BASE_URL ?>aprendizaje/unidad/<?= $encU ?>"
                       style="display: flex; align-items: center; gap: 12px; padding: 16px; <?= $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.04);' : '' ?> text-decoration: none; transition: background 0.15s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                       onmouseout="this.style.background='transparent'">
                        <div style="width: 32px; height: 32px; border-radius: 6px; background: <?= $completa ? 'rgba(40,167,69,0.15)' : 'rgba(94,106,210,0.15)' ?>; color: <?= $completa ? '#28A745' : '#5E6AD2' ?>; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                            <?php if ($completa): ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php else: ?>
                                <?= (int)$u['orden'] ?>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: #FFFFFF; font-size: 14px; font-weight: 500; margin-bottom: 2px;">
                                <?= htmlspecialchars($u['titulo']) ?>
                            </div>
                            <?php if (!empty($u['descripcion'])): ?>
                                <div style="color: #8A8F98; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($u['descripcion']) ?>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: 6px;">
                                <span style="background: rgba(94,106,210,0.12); color: #8B93F0; padding: 2px 8px; border-radius: 6px; font-size: 11px;">
                                    <?= htmlspecialchars($u['materia_nombre'] ?? '') ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($completa): ?>
                            <span class="badge-estado badge-activo">Completada</span>
                        <?php endif; ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #4A4B52; flex-shrink: 0;"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
