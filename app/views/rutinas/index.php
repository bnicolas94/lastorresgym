<?php
// Mapeo de días para mostrar texto bonito
$nombresDias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];
?>

<div class="container pt-4 mb-5">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between animate-fade-in">
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-1">Tu Plan</h6>
            <h1 class="display-5 theme-text fw-bold mb-0 font-teko">RUTINA ACTUAL</h1>
        </div>
        <div class="pe-2">
            <?php
            // Lógica de Navegación de Semanas
            $currW = (int) $rutina['semana'];
            $currY = (int) $rutina['anio'];

            // Semana Anterior
            $prevW = $currW - 1;
            $prevY = $currY;
            if ($prevW < 1) {
                $prevW = 52; // Simplificado, idealmente chequear semanas exactas del año anterior
                $prevY--;
            }

            // Semana Siguiente
            $nextW = $currW + 1;
            $nextY = $currY;
            if ($nextW > 52) {
                $nextW = 1;
                $nextY++;
            }

            // Base URL para los links
            $queryUrl = isset($admin_mode_user_id) ? "rutinas/ver_cliente/$admin_mode_user_id" : "rutinas";
            ?>
            <div
                class="d-flex align-items-center card-custom border border-secondary border-opacity-25 rounded-pill p-1 shadow-sm">
                <a href="<?= BASE_URL . $queryUrl ?>?semana=<?= $prevW ?>&anio=<?= $prevY ?>"
                    class="btn btn-link theme-text p-2">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <div class="px-3 text-center" style="min-width: 100px;">
                    <span class="theme-text opacity-75 small text-uppercase d-block ls-1"
                        style="font-size: 0.6rem;">Semana</span>
                    <span class="text-primary fw-bold"><?= $currW ?></span>
                </div>
                <a href="<?= BASE_URL . $queryUrl ?>?semana=<?= $nextW ?>&anio=<?= $nextY ?>"
                    class="btn btn-link theme-text p-2">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <?php if (empty($rutina) || empty($rutina['dias'])): ?>
        <!-- Estado Vacío -->
        <div class="text-center py-5 animate-fade-in">
            <div class="mb-4 text-secondary opacity-50">
                <i class="fas fa-clipboard-list fa-4x"></i>
            </div>
            <h4 class="theme-text">No tenés rutina asignada</h4>
            <p class="theme-text opacity-75">Pedile a tu profe que te arme un plan.</p>
        </div>
    <?php else: ?>

        <!-- Lista de Días -->
        <div class="row g-3">
            <?php foreach ($rutina['dias'] as $numDia => $grupos): ?>
                <div class="col-12 animate-fade-in" style="animation-delay: <?= $numDia * 0.1 ?>s">
                    <?php
                    $diaLink = isset($admin_mode_user_id)
                        ? "rutinas/dia_cliente/$admin_mode_user_id/$numDia"
                        : "rutinas/dia/$numDia";
                    $diaLink .= "?semana=$currW&anio=$currY";
                    ?>
                    <a href="<?= BASE_URL . $diaLink ?>" class="text-decoration-none">
                        <div class="card-custom hover-scale p-0 overflow-hidden d-flex position-relative transition-all">

                            <!-- Banda lateral de color -->
                            <div class="bg-primary" style="width: 6px;"></div>

                            <div class="p-3 flex-grow-1 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h3 class="theme-text font-teko mb-0 me-3">DÍA
                                            <?= $numDia ?>
                                        </h3>
                                        <!-- <span class="badge bg-dark border border-secondary text-secondary">
                                            <?= $nombresDias[$numDia] ?? 'Día ' . $numDia ?>
                                        </span> -->
                                    </div>
                                    <p class="theme-text opacity-75 mb-0 small text-uppercase ls-1 fw-bold">
                                        <?= implode(' • ', $grupos) ?>
                                    </p>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>

                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <!-- FAB Button (Solo si no es Admin Mode) -->
    <?php if (!isset($admin_mode_user_id)): ?>
        <div class="position-fixed end-0 p-4" style="z-index: 100; bottom: calc(95px + env(safe-area-inset-bottom, 20px));">
            <a href="<?= BASE_URL ?>rutinas/crear"
                class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center hover-scale"
                style="width: 60px; height: 60px;">
                <i class="fas fa-plus fa-lg"></i>
            </a>
        </div>
    <?php endif; ?>

</div>