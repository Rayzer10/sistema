<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($unidad) ? 'Editar Unidad' : 'Nueva Unidad' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>unidad/form<?= $encodedId ? '/' . $encodedId : '' ?>"
              method="POST"
              enctype="multipart/form-data"
              id="formUnidad"
              novalidate>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">Título de la unidad</label>
                        <input type="text"
                               class="form-control"
                               name="titulo"
                               placeholder="Ej: Introducción a la Ingeniería de Requisitos"
                               value="<?= htmlspecialchars($unidad['titulo'] ?? '') ?>"
                               maxlength="150"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese un título (mínimo 3 caracteres).</div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number"
                               class="form-control"
                               name="orden"
                               min="1"
                               value="<?= (int)($unidad['orden'] ?? 1) ?>"
                               required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="1" <?= (!isset($unidad) || $unidad['estado'] == 1) ? 'selected' : '' ?>>Publicado</option>
                            <option value="2" <?= (isset($unidad) && $unidad['estado'] == 2) ? 'selected' : '' ?>>Borrador</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Materia</label>
                        <select class="form-select" name="id_materia" required>
                            <option value="">Seleccione una materia</option>
                            <?php foreach ($materias as $ma): ?>
                                <option value="<?= $ma['id'] ?>" <?= (isset($unidad) && $unidad['id_materia'] == $ma['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ma['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione una materia.</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nivel</label>
                        <select class="form-select" name="id_nivel" required>
                            <option value="">Seleccione un nivel</option>
                            <?php foreach ($niveles as $n): ?>
                                <option value="<?= $n['id'] ?>" <?= (isset($unidad) && $unidad['id_nivel'] == $n['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($n['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione un nivel.</div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control"
                                  name="descripcion"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Breve descripción del contenido de la unidad"><?= htmlspecialchars($unidad['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

            <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">
                Archivos de la unidad
            </h5>

            <div class="row">
                <?php
                $archivosPorTipo = [];
                foreach ($archivos as $a) {
                    $archivosPorTipo[$a['tipo']] = $a;
                }
                ?>

                <!-- MP4 -->
                <div class="col-md-6">
                    <div class="form-group file-upload-box">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#5E6AD2;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                            Video (MP4) <span style="color:#4A4B52;font-weight:400;">opcional</span>
                        </label>
                        <?php if (!empty($archivosPorTipo['mp4'])): ?>
                            <div class="archivo-actual">
                                <span><?= htmlspecialchars($archivosPorTipo['mp4']['nombre_original']) ?></span>
                                <a href="<?= BASE_URL ?>unidad/eliminarArchivo/<?= $encodedId ?>/<?= $this->encodeId($archivosPorTipo['mp4']['id']) ?>" class="btn-quitar-archivo" title="Quitar">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            </div>
                            <small style="color:#8A8F98;font-size:12px;display:block;margin-top:6px;">Reemplazar:</small>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="archivo_mp4" accept="video/mp4,.mp4">
                        <small style="color:#8A8F98;font-size:11px;">Máx. 100 MB</small>
                    </div>
                </div>

                <!-- PDF -->
                <div class="col-md-6">
                    <div class="form-group file-upload-box">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#DC3545;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Documento (PDF) <span style="color:#4A4B52;font-weight:400;">opcional</span>
                        </label>
                        <?php if (!empty($archivosPorTipo['pdf'])): ?>
                            <div class="archivo-actual">
                                <span><?= htmlspecialchars($archivosPorTipo['pdf']['nombre_original']) ?></span>
                                <a href="<?= BASE_URL ?>unidad/eliminarArchivo/<?= $encodedId ?>/<?= $this->encodeId($archivosPorTipo['pdf']['id']) ?>" class="btn-quitar-archivo" title="Quitar">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            </div>
                            <small style="color:#8A8F98;font-size:12px;display:block;margin-top:6px;">Reemplazar:</small>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="archivo_pdf" accept="application/pdf,.pdf">
                    </div>
                </div>

                <!-- PPTX -->
                <div class="col-md-6">
                    <div class="form-group file-upload-box">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#FF9800;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/></svg>
                            Presentación (PPTX) <span style="color:#4A4B52;font-weight:400;">opcional</span>
                        </label>
                        <?php if (!empty($archivosPorTipo['pptx'])): ?>
                            <div class="archivo-actual">
                                <span><?= htmlspecialchars($archivosPorTipo['pptx']['nombre_original']) ?></span>
                                <a href="<?= BASE_URL ?>unidad/eliminarArchivo/<?= $encodedId ?>/<?= $this->encodeId($archivosPorTipo['pptx']['id']) ?>" class="btn-quitar-archivo" title="Quitar">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            </div>
                            <small style="color:#8A8F98;font-size:12px;display:block;margin-top:6px;">Reemplazar:</small>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="archivo_pptx" accept=".pptx,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                    </div>
                </div>

                <!-- HTML dinámico -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;color:#28A745;"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                            Texto HTML dinámico <span style="color:#4A4B52;font-weight:400;">opcional</span>
                        </label>
                        <textarea class="form-control"
                                  name="contenido_html"
                                  id="contenido_html"
                                  rows="8"
                                  style="font-family: 'Courier New', monospace; font-size: 12px;"
                                  placeholder="<p>Contenido HTML enriquecido...</p>"><?= htmlspecialchars($unidad['contenido_html'] ?? '') ?></textarea>
                        <small style="color:#8A8F98;font-size:11px;">Se sanitiza antes de guardar (sin scripts ni eventos).</small>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($unidad) ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>unidad" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.file-upload-box {
    background: #0A0A0B;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 20px;
}
.archivo-actual {
    background: rgba(94, 106, 210, 0.08);
    border: 1px solid rgba(94, 106, 210, 0.2);
    border-radius: 6px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 12px;
    color: #FFFFFF;
}
.btn-quitar-archivo {
    color: #DC3545;
    text-decoration: none;
    padding: 2px;
    display: flex;
    align-items: center;
    transition: opacity 0.15s;
}
.btn-quitar-archivo:hover { opacity: 0.7; }
</style>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    FormValidation.init('#formUnidad');

    document.querySelectorAll('.btn-quitar-archivo').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            swalTheme.fire({
                title: '¿Quitar archivo?',
                html: 'El archivo será eliminado del servidor.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then(function(r) {
                if (r.isConfirmed) window.location.href = url;
            });
        });
    });
});
</script>
