<div class="card-custom">
    <div class="card-header-custom">
        <h4>
            <?= (isset($esPerfil) && $esPerfil) ? 'Mi Perfil' : (isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario') ?>
        </h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>usuario/<?= (isset($esPerfil) && $esPerfil) ? 'perfil' : ('form' . ($encodedId ? '/' . $encodedId : '')) ?>" method="POST" id="formUsuario" novalidate>
            <div class="row">
                <!-- Nombre -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre completo</label>
                        <input type="text"
                               class="form-control"
                               id="nombre"
                               name="nombre"
                               placeholder="Ej: Juan Pérez"
                               value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                               maxlength="100"
                               required
                               data-minlength="2"
                               data-pattern="^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$"
                               data-pattern-msg="El nombre solo debe contener letras.">
                        <div class="invalid-feedback">El nombre es obligatorio y debe tener al menos 2 caracteres.</div>
                    </div>
                </div>

                <!-- Correo -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email"
                               class="form-control"
                               id="correo"
                               name="correo"
                               placeholder="ejemplo@dominio.com"
                               value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>"
                               maxlength="100"
                               required>
                        <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Teléfono (celular) <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                        <input type="tel"
                               class="form-control"
                               id="telefono"
                               name="telefono"
                               placeholder="0412-1234567"
                               value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                               maxlength="12"
                               data-pattern="^(04\d{2}-?\d{7})?$"
                               data-pattern-msg="Formato: 04XX-XXXXXXX (11 dígitos).">
                        <div class="invalid-feedback">Formato: 04XX-XXXXXXX (11 dígitos) o dejar vacío.</div>
                    </div>
                </div>

                <!-- Rol (solo visible si NO es perfil) -->
                <?php if (!isset($esPerfil)): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol" required id="rol">
                            <option value="">Seleccione un rol</option>
                            <option value="1" <?= (isset($usuario) && $usuario['rol'] == 1) ? 'selected' : '' ?>>Administrador</option>
                            <option value="2" <?= (isset($usuario) && $usuario['rol'] == 2) ? 'selected' : '' ?>>Docente</option>
                            <option value="3" <?= (isset($usuario) && $usuario['rol'] == 3) ? 'selected' : '' ?>>Estudiante</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un rol.</div>
                    </div>
                </div>
                <?php else: ?>
                    <input type="hidden" name="rol" value="<?= $usuario['rol'] ?>">
                <?php endif; ?>

                <!-- Estado (solo visible si NO es perfil) -->
                <?php if (!isset($esPerfil)): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required id="estado">
                            <option value="">Seleccione un estado</option>
                            <option value="1" <?= (!isset($usuario) || $usuario['estado'] == 1) ? 'selected' : '' ?>>Activo</option>
                            <option value="2" <?= (isset($usuario) && $usuario['estado'] == 2) ? 'selected' : '' ?>>Deshabilitado</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un estado.</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Materias que imparte el docente -->
            <?php $esDocente = isset($usuario) && (int)$usuario['rol'] === 2; ?>

            <?php if (isset($esPerfil) && $esPerfil && $esDocente): ?>
                <!-- Vista de solo lectura en el perfil del docente -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Materias que impartes</label>
                            <?php
                            $materiasVisibles = !empty($materiasAsignadas)
                                ? array_filter($materias, fn($m) => in_array((int)$m['id'], $materiasAsignadas, true))
                                : [];
                            ?>
                            <?php if (empty($materiasVisibles)): ?>
                                <div style="background: rgba(255,193,7,0.08); border: 1px solid rgba(255,193,7,0.2); border-radius: 8px; padding: 12px 16px; color: #FFC107; font-size: 13px;">
                                    Aún no tienes materias asignadas. Pídele al administrador que te asigne las materias que impartes.
                                </div>
                            <?php else: ?>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    <?php foreach ($materiasVisibles as $m): ?>
                                        <span style="background: rgba(94,106,210,0.15); color: #5E6AD2; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                            <?= htmlspecialchars($m['nombre']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php elseif (!isset($esPerfil) && ($esDocente || !isset($usuario))): ?>
            <div class="row" id="bloqueMaterias" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Materias que imparte <span style="color:#4A4B52;font-weight:400;">opcional</span></label>
                        <div id="listaMaterias" style="background: #0A0A0B; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px; max-height: 240px; overflow-y: auto;">
                            <?php if (empty($materias)): ?>
                                <p style="color: #4A4B52; text-align: center; font-size: 13px; padding: 12px;">
                                    No hay materias registradas. Crea primero el catálogo de materias.
                                </p>
                            <?php else: ?>
                                <?php foreach ($materias as $m): ?>
                                    <?php $checked = in_array((int)$m['id'], $materiasAsignadas ?? [], true); ?>
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 6px 8px; border-radius: 6px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="materias[]" value="<?= (int)$m['id'] ?>" class="chk-materia" <?= $checked ? 'checked' : '' ?>>
                                        <span style="color: #FFFFFF; font-size: 13px;"><?= htmlspecialchars($m['nombre']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function toggleBloqueMaterias() {
                    var sel = document.querySelector('select[name="rol"]');
                    var bloque = document.getElementById('bloqueMaterias');
                    if (!sel || !bloque) return;
                    bloque.style.display = (sel.value === '2') ? 'flex' : 'none';
                }
                document.addEventListener('DOMContentLoaded', function() {
                    toggleBloqueMaterias();
                    var sel = document.querySelector('select[name="rol"]');
                    if (sel) sel.addEventListener('change', toggleBloqueMaterias);
                });
            </script>
            <?php endif; ?>

                <!-- Contraseña -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <?= isset($usuario) ? 'Nueva contraseña' : 'Contraseña' ?>
                        </label>
                        <div style="position: relative;">
                            <input type="password"
                                   class="form-control"
                                   id="clave"
                                   name="clave"
                                   placeholder="<?= isset($usuario) ? 'Dejar vacío para mantener actual' : 'Mínimo 8 caracteres' ?>"
                                   maxlength="32"
                                   style="padding-right: 40px;"
                                   <?= isset($usuario) ? '' : 'required data-alphanumeric' ?>>
                            <button type="button" id="toggleClave" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #8A8F98; cursor: pointer; padding: 4px;">
                                <svg id="eyeOff" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <svg id="eyeOn" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="invalid-feedback">Mínimo 8 caracteres, solo letras y números.</div>
                    </div>
                </div>

                <!-- Repetir contraseña -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Repetir contraseña</label>
                        <div style="position: relative;">
                            <input type="password"
                                   class="form-control"
                                   id="clave_confirmar"
                                   name="clave_confirmar"
                                   placeholder="<?= isset($usuario) ? 'Confirmar nueva contraseña' : 'Repetir contraseña' ?>"
                                   maxlength="32"
                                   style="padding-right: 40px;"
                                   <?= isset($usuario) ? '' : 'required data-match="clave"' ?>>
                            <button type="button" id="toggleClaveConfirmar" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #8A8F98; cursor: pointer; padding: 4px;">
                                <svg class="eyeOffConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <svg class="eyeOnConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= (isset($esPerfil) && $esPerfil) ? 'Actualizar Perfil' : (isset($usuario) ? 'Actualizar' : 'Guardar') ?>
                </button>
                <a href="<?= BASE_URL ?>usuario" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
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
    var form = document.getElementById('formUsuario');
    var clave = document.getElementById('clave');
    var claveConfirmar = document.getElementById('clave_confirmar');

    var toggleClave = document.getElementById('toggleClave');
    var eyeOff = document.getElementById('eyeOff');
    var eyeOn = document.getElementById('eyeOn');

    toggleClave.addEventListener('click', function() {
        var esPassword = clave.type === 'password';
        clave.type = esPassword ? 'text' : 'password';
        eyeOff.style.display = esPassword ? 'none' : 'block';
        eyeOn.style.display = esPassword ? 'block' : 'none';
    });

    var toggleClaveConfirmar = document.getElementById('toggleClaveConfirmar');
    var eyeOffConfirm = document.querySelector('.eyeOffConfirm');
    var eyeOnConfirm = document.querySelector('.eyeOnConfirm');

    toggleClaveConfirmar.addEventListener('click', function() {
        var esPassword = claveConfirmar.type === 'password';
        claveConfirmar.type = esPassword ? 'text' : 'password';
        eyeOffConfirm.style.display = esPassword ? 'none' : 'block';
        eyeOnConfirm.style.display = esPassword ? 'block' : 'none';
    });

    var telefono = document.getElementById('telefono');
    telefono.addEventListener('input', function(e) {
        var value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        if (value.length > 4) value = value.substring(0, 4) + '-' + value.substring(4);
        e.target.value = value;
    });

    var esPerfil = <?= isset($esPerfil) && $esPerfil ? 'true' : 'false' ?>;
    var esEdicion = <?= isset($usuario) ? 'true' : 'false' ?>;

    if (esPerfil || esEdicion) {
        clave.removeAttribute('required');
        clave.removeAttribute('data-alphanumeric');
        claveConfirmar.removeAttribute('required');
        claveConfirmar.removeAttribute('data-match');
    }

    FormValidation.init('#formUsuario');

    var selects = form.querySelectorAll('select[required]');
    selects.forEach(function(sel) {
        sel.addEventListener('change', function() {
            if (this.value !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>