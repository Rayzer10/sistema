<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($actividad) ? 'Editar Actividad' : 'Nueva Actividad' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>actividad/form<?= $encodedId ? '/' . $encodedId : '' ?>" method="POST" id="formActividad" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Título de la actividad</label>
                        <input type="text"
                               class="form-control"
                               name="titulo"
                               placeholder="Ej: Actividad 1 - Requisitos Funcionales"
                               value="<?= htmlspecialchars($actividad['titulo'] ?? '') ?>"
                               maxlength="150"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese un título (mínimo 3 caracteres).</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Materia</label>
                        <select class="form-select" name="id_materia" id="idMateria" required>
                            <option value="">Seleccione una materia</option>
                            <?php foreach ($materias as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= (isset($actividad) && $actividad['id_materia'] == $m['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione una materia.</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Unidad <span style="color:#4A4B52;font-weight:400;">(creadas por el administrador)</span></label>
                        <select class="form-select" name="id_unidad" id="idUnidad" required>
                            <option value="">Primero seleccione una materia</option>
                            <?php foreach ($unidades as $u): ?>
                                <option value="<?= (int)$u['id'] ?>" <?= (isset($actividad) && $actividad['id_unidad'] == $u['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione una unidad.</div>
                    </div>
                    <div id="unidadHint" style="display:none; background: rgba(220,120,70,0.08); border: 1px dashed rgba(220,120,70,0.35); border-radius: 8px; padding: 10px 14px; color: #C97A4A; font-size: 13px; margin-bottom: 12px;"></div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Nivel <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                        <select class="form-select" name="id_nivel">
                            <option value="">Sin nivel</option>
                            <?php foreach ($niveles as $n): ?>
                                <option value="<?= $n['id'] ?>" <?= (isset($actividad) && $actividad['id_nivel'] == $n['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($n['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="1" <?= (!isset($actividad) || $actividad['estado'] == 1) ? 'selected' : '' ?>>Publicada</option>
                            <option value="2" <?= (isset($actividad) && $actividad['estado'] == 2) ? 'selected' : '' ?>>Borrador</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control"
                                  name="descripcion"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Indicaciones generales de la actividad"><?= htmlspecialchars($actividad['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.06); margin: 24px 0;">

            <h5 style="font-size: 13px; font-weight: 600; color: #8A8F98; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                Secciones con acceso
            </h5>
            <p style="color: #4A4B52; font-size: 12px; margin: 0 0 12px;">
                Solo los estudiantes de las secciones seleccionadas podrán ver y resolver esta actividad.
            </p>

            <div id="seccionesHint" style="background: rgba(94,106,210,0.08); border: 1px dashed rgba(94,106,210,0.35); border-radius: 8px; padding: 10px 14px; color: #8A8F98; font-size: 13px; margin-bottom: 12px;">
                Selecciona una materia para ver las secciones disponibles.
            </div>

            <?php if (empty($secciones)): ?>
                <div style="color: #8A8F98; font-size: 13px;">
                    No hay secciones a tu cargo con esta materia. Asegúrate de tener secciones asignadas con esta materia.
                </div>
            <?php else: ?>
                <div id="seccionesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 8px;">
                    <?php foreach ($secciones as $s): ?>
                        <label style="display: flex; align-items: center; gap: 10px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 12px; cursor: pointer; font-size: 13px; user-select: none;">
                            <input type="checkbox"
                                   name="secciones[]"
                                   value="<?= (int)$s['id'] ?>"
                                   style="width: 16px; height: 16px; accent-color: #5E6AD2; flex-shrink: 0;"
                                   <?= in_array((int)$s['id'], $seccionesAsignadas) ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($s['nombre']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" id="btnGuardarActividad" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($actividad) ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>actividad" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formActividad');
    if (form) FormValidation.init('#formActividad');

    var selMateria = document.getElementById('idMateria');
    var grid = document.getElementById('seccionesGrid');
    var hint = document.getElementById('seccionesHint');

    var selUnidad = document.getElementById('idUnidad');
    var unidadHint = document.getElementById('unidadHint');
    var btnSubmit = document.getElementById('btnGuardarActividad');

    if (hint && grid && grid.children.length > 0) hint.style.display = 'none';

    function renderSecciones(secciones, marcadas) {
        if (!grid) return;
        if (!secciones.length) {
            grid.innerHTML = '<div style="color: #8A8F98; font-size: 13px;">' +
                'No hay secciones a tu cargo con esta materia. Asegúrate de tener secciones asignadas con esta materia.</div>';
            return;
        }
        grid.innerHTML = '';
        secciones.forEach(function(s) {
            var label = document.createElement('label');
            label.style.cssText = 'display: flex; align-items: center; gap: 10px; background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 12px; cursor: pointer; font-size: 13px; user-select: none;';
            var input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'secciones[]';
            input.value = s.id;
            input.style.cssText = 'width: 16px; height: 16px; accent-color: #5E6AD2; flex-shrink: 0;';
            if (marcadas.indexOf(s.id) !== -1) input.checked = true;
            var span = document.createElement('span');
            span.textContent = s.nombre;
            label.appendChild(input);
            label.appendChild(span);
            grid.appendChild(label);
        });
    }

    function setSubmitBloqueado(bloqueado, mensaje) {
        if (unidadHint) {
            unidadHint.style.display = mensaje ? 'block' : 'none';
            unidadHint.textContent = mensaje || '';
        }
        if (btnSubmit) {
            btnSubmit.disabled = bloqueado;
            btnSubmit.style.opacity = bloqueado ? '0.5' : '';
            btnSubmit.style.cursor = bloqueado ? 'not-allowed' : '';
        }
    }

    function renderUnidades(unidades, marcadaId) {
        if (!selUnidad) return;
        selUnidad.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Seleccione una unidad';
        selUnidad.appendChild(opt);

        if (!unidades.length) {
            setSubmitBloqueado(true, 'No hay unidades creadas para esta materia. El administrador debe crear unidades antes de crear actividades.');
            return;
        }
        setSubmitBloqueado(false, '');
        unidades.forEach(function(u) {
            var o = document.createElement('option');
            o.value = u.id;
            o.textContent = u.titulo;
            if (marcadaId && String(u.id) === String(marcadaId)) o.selected = true;
            selUnidad.appendChild(o);
        });
    }

    function loadUnidades(materiaId, marcadaId) {
        if (!selUnidad) return;
        if (!materiaId) {
            setSubmitBloqueado(true, 'Selecciona una materia para cargar sus unidades.');
            return;
        }
        fetch('<?= BASE_URL ?>actividad/unidadesJson?materia=' + materiaId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { renderUnidades(data.unidades || [], marcadaId || 0); })
        .catch(function() {
            setSubmitBloqueado(true, 'Error al cargar las unidades.');
        });
    }

    var marcadasIniciales = [];
    if (grid) {
        grid.querySelectorAll('input[name="secciones[]"]:checked').forEach(function(i) {
            marcadasIniciales.push(parseInt(i.value, 10));
        });
    }

    if (selMateria && grid) {
        selMateria.addEventListener('change', function() {
            var id = parseInt(this.value, 10) || 0;
            loadUnidades(id, 0);
            if (hint) hint.style.display = 'block';
            if (!id) {
                if (hint) hint.textContent = 'Selecciona una materia para ver las secciones disponibles.';
                if (grid) grid.innerHTML = '<div style="color: #8A8F98; font-size: 13px;">Selecciona una materia.</div>';
                return;
            }
            fetch('<?= BASE_URL ?>actividad/seccionesJson?materia=' + id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (hint) hint.style.display = 'none';
                renderSecciones(data.secciones || [], marcadasIniciales);
            })
            .catch(function() {
                if (hint) hint.textContent = 'Error al cargar las secciones.';
            });
        });
    }

    // Estado inicial de unidades
    if (selUnidad) {
        var tieneOpciones = Array.prototype.some.call(selUnidad.options, function(o) { return o.value !== ''; });
        var materiaInicial = selMateria ? (parseInt(selMateria.value, 10) || 0) : 0;
        if (tieneOpciones) {
            // Edición: las unidades ya vienen del servidor
            setSubmitBloqueado(false, '');
        } else if (materiaInicial) {
            loadUnidades(materiaInicial, 0);
        } else {
            setSubmitBloqueado(true, 'Selecciona una materia para cargar sus unidades.');
        }
    }
});
</script>