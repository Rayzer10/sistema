<div class="card-custom">
    <div class="card-header-custom">
        <h4><?= isset($materia) ? 'Editar Materia' : 'Nueva Materia' ?></h4>
    </div>
    <div class="card-body-custom">
        <form action="<?= BASE_URL ?>materia/form<?= $encodedId ? '/' . $encodedId : '' ?>" method="POST" id="formMateria" novalidate>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Nombre de la materia</label>
                        <input type="text"
                               class="form-control"
                               name="nombre"
                               placeholder="Ej: Ingenieria de Requisitos"
                               value="<?= htmlspecialchars($materia['nombre'] ?? '') ?>"
                               maxlength="120"
                               required
                               data-minlength="3">
                        <div class="invalid-feedback">Ingrese un nombre (mínimo 3 caracteres).</div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?= isset($materia) ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>materia" class="btn-outline-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
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
    FormValidation.init('#formMateria');
});
</script>
