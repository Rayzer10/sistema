<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($periodo) ? 'Editar Período' : 'Nuevo Período' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>periodo/form<?= $encodedId ? '/' . $encodedId : '' ?>" method="POST" id="formPeriodo" novalidate>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Nombre del período</label>
                        <input type="text"
                               class="form-control"
                               name="nombre"
                               placeholder="Ej: 2026-I, Enero-Abril 2026"
                               value="<?= htmlspecialchars($periodo['nombre'] ?? '') ?>"
                               maxlength="80"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese un nombre (mínimo 3 caracteres).</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Fecha de inicio</label>
                        <input type="date"
                               class="form-control"
                               name="fecha_inicio"
                               value="<?= htmlspecialchars($periodo['fecha_inicio'] ?? '') ?>"
                               required>
                        <div class="invalid-feedback">Seleccione la fecha de inicio.</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Fecha de fin</label>
                        <input type="date"
                               class="form-control"
                               name="fecha_fin"
                               value="<?= htmlspecialchars($periodo['fecha_fin'] ?? '') ?>"
                               required>
                        <div class="invalid-feedback">La fecha de fin debe ser posterior a la fecha de inicio.</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="1" <?= (!isset($periodo) || $periodo['estado'] == 1) ? 'selected' : '' ?>>Activo</option>
                            <option value="2" <?= (isset($periodo) && $periodo['estado'] == 2) ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($periodo) ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>periodo" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
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
    FormValidation.init('#formPeriodo');

    var fechaInicio = document.querySelector('input[name="fecha_inicio"]');
    var fechaFin = document.querySelector('input[name="fecha_fin"]');

    function validarRangoFechas() {
        if (fechaInicio.value && fechaFin.value) {
            if (fechaFin.value <= fechaInicio.value) {
                fechaFin.classList.add('is-invalid');
                return false;
            }
            fechaFin.classList.remove('is-invalid');
        }
        return true;
    }

    fechaFin.addEventListener('change', validarRangoFechas);
    fechaInicio.addEventListener('change', validarRangoFechas);
});
</script>
