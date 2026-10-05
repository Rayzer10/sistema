<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Sistema de Gestión' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>resource/css/estilos.css">
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="wrapper">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="8" fill="#5E6AD2"/>
                    <path d="M9 16L14 21L23 11" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h3>Sistema</h3>
                <button class="sidebar-close-btn" id="sidebarCloseBtn" onclick="toggleSidebar()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= ($menuActivo ?? '') === 'home' ? 'active' : '' ?>" href="<?= BASE_URL ?>home">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Inicio</span>
                    </a>
                </li>

                <?php if (\Auth::isEstudiante()): ?>
                    <li class="nav-section">Estudiante</li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'panel' ? 'active' : '' ?>" href="<?= BASE_URL ?>panel">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                            <span>Mi Progreso</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'plantillas' ? 'active' : '' ?>" href="<?= BASE_URL ?>plantilla">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <span>Plantillas</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (\Auth::isDocente()): ?>
                    <li class="nav-section">Docente</li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'seguimiento' ? 'active' : '' ?>" href="<?= BASE_URL ?>seguimiento">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                            <span>Seguimiento</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'asignaciones' ? 'active' : '' ?>" href="<?= BASE_URL ?>asignacion">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Asignar Escenarios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'plantillas' ? 'active' : '' ?>" href="<?= BASE_URL ?>plantilla">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <span>Plantillas</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (\Auth::isAdmin()): ?>
                    <li class="nav-section">Administración</li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'periodo' ? 'active' : '' ?>" href="<?= BASE_URL ?>periodo">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Períodos Académicos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'materia' ? 'active' : '' ?>" href="<?= BASE_URL ?>materia">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            <span>Materias</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'seccion' ? 'active' : '' ?>" href="<?= BASE_URL ?>seccion">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>Secciones</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($menuActivo ?? '') === 'usuario' ? 'active' : '' ?>" href="<?= BASE_URL ?>usuario">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>Usuarios</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="sidebar-footer">
                &copy; <?= date('Y') ?> UPTP-LMR &middot; PNFI
            </div>
        </nav>

        <div class="main-content">
            <nav class="navbar-top">
                <button class="mobile-menu-btn" id="menuBtn" onclick="toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>

                <div class="dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span><?= htmlspecialchars(\Auth::nombre()) ?></span>
                        <span class="badge-rol <?= \Auth::isAdmin() ? 'badge-admin' : (\Auth::isDocente() ? 'badge-docente' : 'badge-estudiante') ?>" style="margin-left: 6px;">
                            <?= \Auth::rolLabel() ?>
                        </span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= BASE_URL ?>usuario/perfil">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>login/logout">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="page-content">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-success-custom">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert-danger-custom">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <?= $_SESSION['error'] ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($contenido)) echo $contenido; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const swalTheme = Swal.mixin({
            customClass: {
                popup: 'swal-dark',
                title: 'swal-title',
                htmlContainer: 'swal-text',
                confirmButton: 'swal-btn-confirm',
                cancelButton: 'swal-btn-cancel',
                actions: 'swal-actions'
            },
            buttonsStyling: false,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            toggleSidebar();
        });
    </script>
</body>
</html>
