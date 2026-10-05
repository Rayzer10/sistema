<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($seccion) ? 'Editar Sección' : 'Nueva Sección' ?></h4>
    </div>
    <div class="card-body-custom">
        <?php if (!isset($seccion) && !$periodoVigente): ?>
            <div style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 8px; padding: 16px 18px; margin-bottom: 20px; display: flex; align-items: start; gap: 12px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #DC3545; flex-shrink: 0; margin-top: 2px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <div style="color: #DC3545; font-size: 14px; font-weight: 500; margin-bottom: 4px;">
                        No puedes registrar una sección sin un período académico activo
                    </div>
                    <div style="color: #8A8F98; font-size: 13px; line-height: 1.5;">
                        Primero crea y activa un período académico en
                        <a href="<?= BASE_URL ?>periodo" style="color: #5E6AD2; text-decoration: none; font-weight: 500;">Períodos Académicos</a>
                        y vuelve a esta página.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($seccion) || $periodoVigente): ?>
        <form action="<?= BASE_URL ?>seccion/form<?= $encodedId ? '/' . $encodedId : '' ?>" method="POST" id="formSeccion" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre de la sección</label>
                        <input type="text"
                               class="form-control"
                               name="nombre"
                               placeholder="Ej: Sección A, Grupo 1"
                               value="<?= htmlspecialchars($seccion['nombre'] ?? '') ?>"
                               maxlength="80"
                               required
                               data-minlength="2">
                        <div class="invalid-feedback">Ingrese un nombre (mínimo 2 caracteres).</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Materias de la sección</label>
                        <p style="color:#4A4B52; font-size:12px; margin: 0 0 12px;">
                            Marca las materias que se imparten en la sección y, para cada una, elige el docente que la dicta. Una sección puede tener varias materias impartidas por docentes distintos.
                        </p>
                        <?php if (empty($materias)): ?>
                            <div style="color: #8A8F98; font-size: 13px;">
                                No hay materias registradas. Crea materias primero en
                                <a href="<?= BASE_URL ?>materia" style="color: #5E6AD2; text-decoration: none;">Materias</a>.
                            </div>
                        <?php else: ?>
                            <?php
                                $idsSeccion = array_column($materiasSeccion, 'id');
                                $docenteDe = [];
                                foreach ($materiasSeccion as $ms) {
                                    $docenteDe[(int)$ms['id']] = (int)$ms['id_docente'];
                                }
                            ?>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach ($materias as $m): ?>
                                    <?php $idMat = (int)$m['id']; $marcada = in_array($idMat, $idsSeccion); ?>
                                    <div style="display: flex; align-items: center; gap: 12px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 12px; flex-wrap: wrap;">
                                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; user-select: none; flex: 1; min-width: 180px;">
                                            <input type="checkbox"
                                                   class="chk-materia"
                                                   name="materias[]"
                                                   value="<?= $idMat ?>"
                                                   style="width: 16px; height: 16px; accent-color: #5E6AD2; flex-shrink: 0;"
                                                   <?= $marcada ? 'checked' : '' ?>>
                                            <span><?= htmlspecialchars($m['nombre']) ?></span>
                                        </label>
                                        <select name="docentes_materia[<?= $idMat ?>]"
                                                class="form-select sel-docente"
                                                style="max-width: 280px;"
                                                <?= !$marcada ? 'disabled' : '' ?>>
                                            <option value="">Docente que la dicta</option>
                                            <?php foreach ($docentes as $d): ?>
                                                <?php if (in_array((int)$d['id'], $materiasDocentes[$idMat] ?? [], true)): ?>
                                                    <option value="<?= $d['id'] ?>"
                                                        <?= (isset($docenteDe[$idMat]) && $docenteDe[$idMat] === (int)$d['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($d['nombre']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($materiasDocentes[$idMat])): ?>
                                            <small style="color:#F59E0B;">Sin docentes que la impartan</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($seccion) ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>seccion" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Volver
                </a>
            </div>
        </form>
        <?php else: ?>
            <div style="display: flex; gap: 10px; margin-top: 8px;">
                <a href="<?= BASE_URL ?>seccion" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Volver
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formSeccion');
    if (form) FormValidation.init('#formSeccion');

    document.querySelectorAll('.chk-materia').forEach(function(chk) {
        chk.addEventListener('change', function() {
            var row = this.closest('div');
            var sel = row.querySelector('.sel-docente');
            if (sel) {
                sel.disabled = !this.checked;
                if (!this.checked) sel.value = '';
            }
        });
    });
});
</script>
