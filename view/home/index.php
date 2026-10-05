<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Bienvenido, <?= htmlspecialchars(\Auth::nombre()) ?></h2>
        <p>Sesión iniciada como <strong><?= \Auth::rolLabel() ?></strong></p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
</div>

<div class="home-grid">
    <?php if (\Auth::isEstudiante()): ?>
        <?php if (!empty($materiasEstudiante)): ?>
            <div style="grid-column: 1 / -1;">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h4>Mis materias</h4>
                    </div>
                    <div class="card-body-custom" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($materiasEstudiante as $mat): ?>
                            <div style="display: flex; align-items: center; gap: 10px; background: #0A0A0B; border: 1px solid rgba(94,106,210,0.25); border-radius: 10px; padding: 12px 16px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5E6AD2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($mat['nombre']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>actividad" class="home-card">
            <div class="home-card-icon purple">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </div>
            <h5>Actividades</h5>
            <p>Resuelve tus actividades asignadas, parte por parte</p>
        </a>
        <a href="<?= BASE_URL ?>panel" class="home-card">
            <div class="home-card-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
            </div>
            <h5>Mi Progreso</h5>
            <p>Tu mapa de niveles, historial y reportes</p>
        </a>
        <a href="<?= BASE_URL ?>plantilla" class="home-card">
            <div class="home-card-icon gray">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h5>Plantillas</h5>
            <p>ERS, stakeholders, backlog y riesgos en PDF</p>
        </a>
    <?php elseif (\Auth::isDocente()): ?>
        <a href="<?= BASE_URL ?>actividad" class="home-card">
            <div class="home-card-icon purple">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </div>
            <h5>Actividades</h5>
            <p>Crea actividades, partes y asigna secciones</p>
        </a>
        <a href="<?= BASE_URL ?>seguimiento" class="home-card">
            <div class="home-card-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
            </div>
            <h5>Seguimiento</h5>
            <p>Progreso de los estudiantes de tu sección</p>
        </a>
        <a href="<?= BASE_URL ?>asignacion" class="home-card">
            <div class="home-card-icon green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <h5>Asignar Escenarios</h5>
            <p>Asigna escenarios a los estudiantes</p>
        </a>
        <a href="<?= BASE_URL ?>evaluacion" class="home-card">
            <div class="home-card-icon orange">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <h5>Evaluar Entregas</h5>
            <p>Revisa y califica entregas de escenarios</p>
        </a>
        <a href="<?= BASE_URL ?>plantilla" class="home-card">
            <div class="home-card-icon gray">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h5>Plantillas</h5>
            <p>Plantillas PDF para tus proyectos</p>
        </a>
    <?php elseif (\Auth::isAdmin()): ?>
        <a href="<?= BASE_URL ?>periodo" class="home-card">
            <div class="home-card-icon purple">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h5>Períodos Académicos</h5>
            <p>Gestionar períodos y aperturar secciones</p>
        </a>
        <a href="<?= BASE_URL ?>seccion" class="home-card">
            <div class="home-card-icon green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h5>Secciones</h5>
            <p>Crear secciones, asignar docentes y estudiantes</p>
        </a>
        <a href="<?= BASE_URL ?>materia" class="home-card">
            <div class="home-card-icon purple">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h5>Materias</h5>
            <p>Catalogo de materias del PNFI</p>
        </a>
        <a href="<?= BASE_URL ?>unidad" class="home-card">
            <div class="home-card-icon orange">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h5>Gestor de Contenido</h5>
            <p>Unidades por nivel con video, PDF, PPTX y HTML</p>
        </a>
        <a href="<?= BASE_URL ?>usuario" class="home-card">
            <div class="home-card-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <h5>Usuarios</h5>
            <p>Administrar usuarios del sistema</p>
        </a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>usuario/perfil" class="home-card">
        <div class="home-card-icon gray">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h5>Mi Perfil</h5>
        <p>Ver y editar mi información</p>
    </a>
</div>
