<!-- Header Saludo -->
<div class="container pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="theme-text opacity-75 mb-0 text-uppercase">Hola,</h6>
            <h2 class="display-6 fw-bold theme-text mb-0" style="font-family: 'Teko'">
                <?= htmlspecialchars(explode(' ', trim($usuario))[0]) ?> <span class="text-primary">!</span>
            </h2>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>auth/logout"
                class="btn btn-dark btn-sm rounded-circle d-flex align-items-center justify-content-center border border-secondary"
                style="width: 42px; height: 42px;">
                <i class="fas fa-power-off text-danger"></i>
            </a>
            <div class="rounded-circle overflow-hidden border border-secondary" style="width: 50px; height: 50px;">
                <?php
                $fotoUrl = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                if (!empty($_SESSION['usuario_foto'])) {
                    $f = $_SESSION['usuario_foto'];
                    if (strpos($f, 'http') === 0) {
                        $fotoUrl = $f;
                    } else {
                        $fotoUrl = BASE_URL . 'img/foto-perfil/' . $f;
                    }
                }
                ?>
                <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Perfil" class="w-100 h-100 object-fit-cover shadow-sm"
                    onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom position-relative overflow-hidden">
                <!-- Decorative Circle -->
                <div class="position-absolute top-0 end-0 translate-middle p-5 rounded-circle bg-primary opacity-10"
                    style="filter: blur(40px);"></div>

                <div class="position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="theme-text mb-1">PROGRESO MENSUAL</h5>
                            <h3 class="display-4 fw-bold theme-text mb-0" style="font-family: 'Teko'">
                                <?= $asistencias ?> <span
                                    class="fs-6 fw-normal theme-text opacity-75">Asistencias</span>
                            </h3>
                        </div>
                        <div class="text-end">
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">ACTIVO</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="progress" style="height: 6px; background-color: rgba(128,128,128,0.2);">
                            <!-- Example progress based on 12 visits/month goal -->
                            <?php $porcentaje = min(($asistencias / 12) * 100, 100); ?>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $porcentaje ?>%">
                            </div>
                        </div>
                        <small class="theme-text opacity-75 mt-2 d-block">¡Seguí así! Cada día cuenta.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Anuncios / Comunitario -->
    <?php if (!empty($anuncios)): ?>
        <div class="mb-5 animate-fade-in">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-grow-1 h-px bg-secondary opacity-20"></div>
                <h6 class="mx-3 mb-0 text-secondary text-uppercase ls-2 font-teko">Comunidad Las Torres</h6>
                <div class="flex-grow-1 h-px bg-secondary opacity-20"></div>
            </div>

            <!-- Scroll Horizontal de Anuncios -->
            <div class="d-inline-flex gap-3 overflow-auto pb-3 no-scrollbar w-100 scroll-mask-right"
                style="scroll-snap-type: x mandatory;">
                <?php foreach ($anuncios as $anuncio): ?>
                    <div class="flex-shrink-0" style="width: 280px; scroll-snap-align: start;">
                        <div
                            class="card-custom h-100 position-relative overflow-hidden group p-0 border-secondary border-opacity-10">
                            <!-- Glow Dinámico según Prioridad -->
                            <div
                                class="position-absolute top-0 start-0 w-100 h-100 border-glow transition-all
                                <?= $anuncio['prioridad'] === 'alta' ? 'shadow-danger-glow opacity-30 border-danger' : ($anuncio['prioridad'] === 'media' ? 'shadow-warning-glow opacity-20 border-warning' : 'opacity-10') ?>">
                            </div>

                            <div class="card-body p-3 position-relative z-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="badge bg-dark border border-secondary border-opacity-25 <?= $anuncio['prioridad'] === 'alta' ? 'text-danger' : ($anuncio['prioridad'] === 'media' ? 'text-warning' : 'text-primary') ?> font-teko p-1 px-2 small">
                                        <?= strtoupper($anuncio['prioridad']) ?>
                                    </span>
                                    <span
                                        class="text-muted small font-teko"><?= date('d M', strtotime($anuncio['fecha_creacion'])) ?></span>
                                </div>
                                <h5 class="theme-text font-teko text-uppercase ls-1 mb-2">
                                    <?= htmlspecialchars($anuncio['titulo']) ?>
                                </h5>
                                <p class="text-muted small mb-0"
                                    style="font-size: 0.85rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($anuncio['contenido']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <h5 class="theme-text mb-3">ACCESO RÁPIDO</h5>
    <div class="row g-3">
        <!-- Mi Rutina -->
        <div class="col-6">
            <a href="<?= BASE_URL ?>rutinas" class="text-decoration-none">
                <div class="card-custom h-100 text-center py-4 border-0">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-dumbbell fa-2x"></i>
                    </div>
                    <h6 class="theme-text mb-1">MI RUTINA</h6>
                    <small class="theme-text opacity-75 d-block">
                        <?php if ($plan['hay_plan']): ?>
                            <?php if ($plan['completo']): ?>
                                Semana Completada
                            <?php else: ?>
                                <?= htmlspecialchars($plan['texto']) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            Sin asignar
                        <?php endif; ?>
                    </small>
                </div>
            </a>
        </div>

        <!-- QR -->
        <div class="col-6">
            <a href="#" class="text-decoration-none">
                <div class="card-custom h-100 text-center py-4 border-0">
                    <div class="mb-3 text-secondary">
                        <i class="fas fa-qrcode fa-2x"></i>
                    </div>
                    <h6 class="theme-text mb-1">INGRESO QR</h6>
                    <small class="theme-text opacity-75 d-block">Acceso Rápido</small>
                </div>
            </a>
        </div>

        <!-- Progreso (Future) -->
        <div class="col-12">
            <a href="<?= BASE_URL ?>progreso" class="text-decoration-none w-100">
                <div class="card-custom p-3 d-flex align-items-center justify-content-between border-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-warning">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="theme-text mb-0">ESTADÍSTICAS DE FUERZA</h6>
                            <small class="theme-text opacity-75">Mirá tu evolución en gráficos.</small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
        </div>
    </div>
</div>