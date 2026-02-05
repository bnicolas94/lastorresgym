<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>
        <?= APP_NAME ?>
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Teko:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap & Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">

    <style>
        /* SEAMLESS GLASS BOTTOM NAV */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(14, 14, 18, 0.85);
            /* Glass Background */
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: space-around;
            align-items: center;
            /* Height auto para adaptarse al contenido y padding */
            height: auto;
            min-height: 70px;
            /* Padding seguro: máximo entre 15px o el área segura del dispositivo */
            padding-bottom: max(15px, env(safe-area-inset-bottom));
            padding-top: 12px;
            z-index: 2000;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.4);
        }

        .nav-item-mobile {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: color 0.3s;
            flex: 1;
        }

        .nav-item-mobile i {
            font-size: 1.4rem;
            margin-bottom: 4px;
        }

        .nav-item-mobile.active {
            color: var(--primary);
        }

        /* Desktop Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .nav-link-desktop {
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .nav-link-desktop:hover,
        .nav-link-desktop.active {
            background-color: rgba(139, 92, 246, 0.1);
            color: var(--primary);
        }

        .nav-link-desktop i {
            width: 30px;
        }

        /* Adjust content for navs */
        .content-wrapper {
            padding-bottom: 100px;
            /* Espacio para que el Fixed Nav no tape nada en mobile */
        }

        @media (min-width: 768px) {
            .content-wrapper {
                margin-left: 250px;
                padding-bottom: 2rem;
            }
        }
    </style>
</head>

<body class="min-vh-100">

    <!-- Desktop Sidebar (Visible on MD+) -->
    <nav class="sidebar d-none d-md-flex">
        <div class="mb-5 px-2">
            <h3 class="fw-bold text-white mb-0" style="font-family: 'Teko'">LAS TORRES <span
                    class="text-primary-gradient">GYM</span></h3>
        </div>

        <div class="flex-grow-1">
            <a href="<?= BASE_URL ?>dashboard"
                class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Inicio
            </a>

            <?php if (isset($_SESSION['usuario_rol_id']) && $_SESSION['usuario_rol_id'] <= 4): ?>
                <a href="<?= BASE_URL ?>alumnos"
                    class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'alumnos') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Alumnos
                </a>
                <a href="<?= BASE_URL ?>rutinas/plantillas"
                    class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'rutinas/plantillas') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Plantillas
                </a>
                <a href="<?= BASE_URL ?>anuncios/gestion"
                    class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'anuncios/gestion') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-bullhorn"></i> Anuncios
                </a>
                <a href="<?= BASE_URL ?>finanzas"
                    class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'finanzas') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i> Finanzas
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>rutinas"
                class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'rutinas') !== false) ? 'active' : '' ?>">
                <i class="fas fa-dumbbell"></i> Rutinas
            </a>
            <a href="<?= BASE_URL ?>progreso"
                class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'progreso') !== false) ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Progreso
            </a>
            <a href="<?= BASE_URL ?>perfil"
                class="nav-link-desktop <?= (strpos($_GET['url'] ?? '', 'perfil') !== false) ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Perfil
            </a>
        </div>

        <div class="mt-auto">
            <a href="<?= BASE_URL ?>auth/logout" class="nav-link-desktop text-danger hover-danger">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <?php require_once '../app/views/' . $contentView . '.php'; ?>
    </div>

    <!-- Bottom Navigation (Visible on Mobile) -->
    <nav class="bottom-nav d-md-none">
        <a href="<?= BASE_URL ?>dashboard"
            class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <?php if (isset($_SESSION['usuario_rol_id']) && $_SESSION['usuario_rol_id'] <= 4): ?>
            <a href="<?= BASE_URL ?>alumnos"
                class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'alumnos') !== false) ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Alumnos</span>
            </a>
            <a href="<?= BASE_URL ?>rutinas/plantillas"
                class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'rutinas/plantillas') !== false) ? 'active' : '' ?>">
                <i class="fas fa-layer-group"></i>
                <span>Plantillas</span>
            </a>
            <a href="<?= BASE_URL ?>anuncios/gestion"
                class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'anuncios/gestion') !== false) ? 'active' : '' ?>">
                <span>Anuncios</span>
            </a>
            <a href="<?= BASE_URL ?>finanzas"
                class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'finanzas') !== false) ? 'active' : '' ?>">
                <i class="fas fa-wallet"></i>
                <span>Finanzas</span>
            </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>rutinas"
            class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'rutinas') !== false && strpos($_GET['url'] ?? '', 'plantillas') === false) ? 'active' : '' ?>">
            <i class="fas fa-dumbbell"></i>
            <span>Rutina</span>
        </a>
        <a href="<?= BASE_URL ?>progreso"
            class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'progreso') !== false) ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Progreso</span>
        </a>
        <a href="<?= BASE_URL ?>perfil"
            class="nav-item-mobile <?= (strpos($_GET['url'] ?? '', 'perfil') !== false) ? 'active' : '' ?>">
            <i class="fas fa-user"></i>
            <span>Perfil</span>
        </a>
    </nav>

    <!-- Theme Toggle FAB -->
    <button id="themeToggle"
        class="btn btn-dark rounded-circle border border-secondary shadow-lg d-flex align-items-center justify-content-center cursor-pointer"
        style="display: none !important; position: fixed; bottom: calc(100px + env(safe-area-inset-bottom, 20px)); left: 20px; width: 45px; height: 45px; z-index: 1050; transition: all 0.3s;">
        <i class="fas fa-sun text-warning"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Logic
        const toggleBtn = document.getElementById('themeToggle');
        const icon = toggleBtn.querySelector('i');
        const html = document.documentElement;

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                icon.className = 'fas fa-sun text-warning';
                toggleBtn.className = 'btn btn-dark rounded-circle border border-secondary shadow-lg d-flex align-items-center justify-content-center';
            } else {
                icon.className = 'fas fa-moon text-primary';
                toggleBtn.className = 'btn btn-light rounded-circle border border-secondary shadow-lg d-flex align-items-center justify-content-center';
            }
        }
    </script>
</body>

</html>