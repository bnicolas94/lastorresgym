<div class="container pt-4 pb-5">

    <!-- Header Navegación Glow -->
    <div class="d-flex align-items-center mb-5 animate-fade-in position-relative z-2">
        <?php
        $backUrl = isset($admin_mode) ? "rutinas/ver_cliente/$admin_mode_user_id" : "rutinas";
        $backUrl .= "?semana=$semana&anio=$anio";
        ?>
        <a href="<?= BASE_URL . $backUrl ?>"
            class="btn btn-icon btn-dark border border-secondary theme-text shadow-lg me-3 rounded-circle"
            style="width: 45px; height: 45px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="position-relative">
            <h6 class="text-primary text-uppercase ls-3 mb-0 fw-bold glow-text-sm" style="font-size: 0.7rem;">DÍA
                <?= $dia ?>
            </h6>
            <h1 class="theme-text fw-bold font-teko mb-0 lh-1 display-4 text-shadow-neon">TU ENTRENAMIENTO</h1>
            <div class="position-absolute top-50 start-0 translate-middle-y w-100 h-100 bg-primary opacity-25 blur-xl z-n1"
                style="filter: blur(40px);"></div>
        </div>
    </div>

    <!-- Navigation Pills (Days 1-7) -->
    <!-- Grid Container with relative positioning for scroll indicator -->
    <div class="position-relative animate-fade-in mb-4">
        <div class="d-flex overflow-auto py-3 gap-2 no-scrollbar scroll-mask-right" style="white-space: nowrap;">
            <?php for ($i = 1; $i <= 7; $i++):
                $isActive = ($i == $dia);
                $hasRoutine = isset($rutinaFull['dias'][$i]);
                $muscles = $hasRoutine ? implode('/', $rutinaFull['dias'][$i]) : '-';
                ?>
                <?php
                $diaUrl = isset($admin_mode) ? "rutinas/dia_cliente/$admin_mode_user_id/$i" : "rutinas/dia/$i";
                $diaUrl .= "?semana=$semana&anio=$anio";
                ?>
                <a href="<?= BASE_URL . $diaUrl ?>" class="btn rounded-pill px-4 py-2 d-flex flex-column align-items-center justify-content-center transition-all
                        <?= $isActive
                            ? 'btn-primary text-white shadow-lg scale-110 border-0'
                            : 'btn-outline-secondary theme-text border-opacity-25 hover-scale-105' ?>"
                    style="min-width: 100px; height: 60px; backdrop-filter: blur(5px);">
                    <span class="font-teko lh-1 small text-uppercase <?= $isActive ? 'fw-bold' : '' ?>">Día <?= $i ?></span>
                    <span class="small <?= $isActive ? 'opacity-100' : 'opacity-50' ?>"
                        style="font-size: 0.7rem;"><?= substr($muscles, 0, 15) ?><?= strlen($muscles) > 15 ? '..' : '' ?></span>
                </a>
            <?php endfor; ?>
        </div>
        <!-- Right Arrow Indicator -->
        <div class="position-absolute top-50 end-0 translate-middle-y pe-2 pointer-events-none d-none d-md-flex align-items-center justify-content-center"
            style="height: 100%; width: 40px; background: linear-gradient(to left, var(--bg-body), transparent);">
            <i class="fas fa-chevron-right text-primary animate-pulse"></i>
        </div>
        <!-- Mobile Hint (Simple Arrow overlay that fades via JS or just static) -->
        <div class="position-absolute top-50 end-0 translate-middle-y pe-1 pointer-events-none d-md-none">
            <i class="fas fa-chevron-right text-primary opacity-50 animate-pulse small"></i>
        </div>
    </div>

    <style>
        .scale-110 {
            transform: scale(1.1);
        }

        .hover-scale-105:hover {
            transform: scale(1.05);
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary) !important;
        }
    </style>

    <!-- Empty State Cyberpunk -->
    <?php if (empty($ejercicios)): ?>
        <div
            class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center animate-fade-in text-muted opacity-50">
            <i class="fas fa-ghost fa-4x mb-3 text-secondary"></i>
            <h3 class="font-teko text-uppercase theme-text">Nada por aquí...</h3>
            <p class="small text-uppercase ls-1">Descanso o falta asignar.</p>
        </div>
    <?php else: ?>

        <!-- Lista Ejercicios -->
        <div class="d-flex flex-column gap-3 position-relative z-1 mb-5">
            <?php foreach ($ejercicios as $index => $ej): ?>
                <!-- CARD CYBERPUNK WRAPPER -->
                <div class="animate-slide-up group" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <div style="height: 10px; width: 100%;"></div>

                    <div class="card-custom bg-black position-relative overflow-hidden rounded-4 cursor-pointer p-0"
                        id="card-<?= $ej['rutina_id'] ?>">
                        <!-- Glow Border Effect -->
                        <div
                            class="absolute-fill border-glow opacity-0 group-hover-opacity-100 transition-all pointer-events-none">
                        </div>

                        <!-- Check Overlay (CON BOTÓN DESHACER) -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-80 d-flex align-items-center justify-content-center backdrop-blur-sm transition-all z-3 cursor-pointer"
                            id="overlay-<?= $ej['rutina_id'] ?>" onclick="toggleEjercicio(<?= $ej['rutina_id'] ?>)"
                            style="opacity: <?= $ej['completado'] ? '1' : '0' ?>; transform: <?= $ej['completado'] ? 'scale(1)' : 'scale(1.1)' ?>; pointer-events: <?= $ej['completado'] ? 'auto' : 'none' ?>;">
                            <div class="text-center">
                                <h2 class="text-success font-teko display-3 mb-0 text-shadow-green">COMPLETADO</h2>
                                <i class="fas fa-check-circle text-success fa-2x shadow-success-glow mt-2"></i>
                                <div class="mt-2">
                                    <span
                                        class="badge bg-dark border border-secondary text-white-50 px-3 py-1 rounded-pill small font-teko ls-1">
                                        <i class="fas fa-undo me-1"></i> DESHACER
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-0 d-flex position-relative z-2">
                            <!-- Left Media Strip -->
                            <div class="position-relative" style="width: 100px; max-width: 30%;">
                                <?php if (!empty($ej['ejercicio_img'])): ?>
                                    <div class="w-100 h-100 position-relative group-video z-5 cursor-pointer"
                                        onclick="openVideo('<?= BASE_URL ?>img/ejercicios/<?= $ej['ejercicio_img'] ?>')">
                                        <video src="<?= BASE_URL ?>img/ejercicios/<?= $ej['ejercicio_img'] ?>#t=0.1"
                                            class="w-100 h-100 object-fit-cover opacity-60 group-hover-opacity-100 transition-all pointer-events-none"
                                            muted preload="metadata"></video>
                                        <div
                                            class="position-absolute top-50 start-50 translate-middle text-white opacity-80 group-video-hover-scale transition-all pointer-events-none">
                                            <i class="fas fa-play-circle fa-3x filter-drop-shadow"></i>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-surface-hover d-flex align-items-center justify-content-center text-secondary"
                                        onclick="toggleEjercicio(<?= $ej['rutina_id'] ?>)">
                                        <i class="fas fa-dumbbell fa-2x"></i>
                                    </div>
                                <?php endif; ?>
                                <div
                                    class="position-absolute top-0 end-0 w-100 h-100 bg-gradient-to-r from-transparent to-black pointer-events-none">
                                </div>
                            </div>

                            <!-- Content Body -->
                            <div class="p-3 p-md-4 flex-grow-1 d-flex flex-column justify-content-center border-start border-white border-opacity-5 cursor-pointer"
                                style="min-height: 120px;" onclick="toggleDetails(<?= $ej['rutina_id'] ?>)">

                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="theme-text font-teko mb-0 text-uppercase ls-1 group-hover-text-primary transition-all"
                                        style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.1;">
                                        <?= $ej['ejercicio'] ?>
                                    </h3>
                                    <!-- Complete Button -->
                                    <button
                                        class="btn btn-icon btn-sm btn-outline-success border-secondary text-success rounded-circle z-5 shadow-sm transition-all hover-scale-110 ms-2 flex-shrink-0"
                                        style="width: 32px; height: 32px;"
                                        onclick="event.stopPropagation(); toggleEjercicio(<?= $ej['rutina_id'] ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>

                                <!-- Chips de Datos -->
                                <div class="d-flex flex-wrap gap-2 text-uppercase font-teko text-muted small tracking-wider">
                                    <span
                                        class="badge bg-surface border border-secondary text-primary px-2 px-md-3 py-2 rounded-pill shadow-sm">
                                        <span class="theme-text fs-6"><?= $ej['series'] ?></span> SERIES
                                    </span>
                                    <span
                                        class="badge bg-surface border border-secondary text-danger px-2 px-md-3 py-2 rounded-pill shadow-sm">
                                        <span class="theme-text fs-6"><?= $ej['repeticiones'] ?></span> REPS
                                    </span>
                                    <span
                                        class="badge bg-surface border border-secondary text-warning px-2 px-md-3 py-2 rounded-pill shadow-sm">
                                        <span
                                            class="theme-text fs-6"><?= isset($ej['peso']) && $ej['peso'] != '' ? $ej['peso'] : '-' ?></span>
                                        KG
                                    </span>
                                </div>

                                <?php if (!empty($ej['observacion'])): ?>
                                    <div
                                        class="mt-3 text-secondary small fst-italic position-relative ps-3 border-start border-primary border-2">
                                        "<?= $ej['observacion'] ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- EXPANDABLE DETAILS -->
                        <div class="collapse mt-2 bg-surface rounded-4 border border-secondary border-opacity-25"
                            id="details-<?= $ej['rutina_id'] ?>">
                            <div class="p-3">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-3">
                                        <thead>
                                            <tr class="text-secondary text-uppercase font-teko small">
                                                <th>Serie</th>
                                                <th>Reps</th>
                                                <th>Kg</th>
                                                <th class="text-end">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="series-body-<?= $ej['rutina_id'] ?>">
                                            <?php if (isset($ej['detalle_series']) && count($ej['detalle_series']) > 0): ?>
                                                <?php foreach ($ej['detalle_series'] as $idx => $serie): ?>
                                                    <tr id="serie-row-<?= $serie['id'] ?>">
                                                        <td class="fw-bold text-primary serie-index">Serie <?= $idx + 1 ?></td>
                                                        <td>
                                                            <input type="number"
                                                                class="form-control form-control-custom form-control-sm text-center"
                                                                style="width: 70px;" value="<?= $serie['repeticiones'] ?>"
                                                                id="reps-<?= $serie['id'] ?>">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.5"
                                                                class="form-control form-control-custom form-control-sm text-center"
                                                                style="width: 70px;" value="<?= $serie['peso'] ?>"
                                                                id="peso-<?= $serie['id'] ?>">
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button class="btn btn-sm btn-success text-white"
                                                                    onclick="saveSerie(<?= $serie['id'] ?>)">
                                                                    <i class="fas fa-save"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    onclick="deleteSerie(<?= $serie['id'] ?>)">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Si está vacío, quizás queramos borrar este TR al agregar el primero -->
                                                <tr id="empty-msg-<?= $ej['rutina_id'] ?>">
                                                    <td colspan="4" class="text-center text-muted">Sin series detalladas</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ACTIONS BAR: Add Series & Delete Exercise -->
                                <div
                                    class="d-flex justify-content-between align-items-center mt-3 border-top border-secondary border-opacity-25 pt-3">
                                    <button class="btn btn-sm btn-outline-primary text-uppercase font-teko ls-1"
                                        onclick="addSerie(<?= $ej['rutina_id'] ?>)">
                                        <i class="fas fa-plus me-1"></i> Agregar Serie
                                    </button>

                                    <button class="btn btn-sm btn-danger text-uppercase font-teko ls-1"
                                        onclick="deleteEjercicio(<?= $ej['rutina_id'] ?>)">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminar Ejercicio
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div> <!-- Cierre Card-Custom -->
                </div> <!-- Cierre animate-slide-up Wrapper -->
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<!-- VIDEO MODAL & SCRIPTS -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none position-relative">
            <!-- Close Button: Manual Force Close -->
            <button type="button"
                class="position-absolute top-0 end-0 m-0 btn btn-dark rounded-circle border border-secondary shadow-lg d-flex align-items-center justify-content-center"
                onclick="forceCloseVideo()"
                style="width: 45px; height: 45px; z-index: 1060; transform: translate(50%, -50%); cursor: pointer;">
                <i class="fas fa-times text-white fa-lg"></i>
            </button>

            <div class="modal-body p-0 rounded-4 overflow-hidden border border-secondary shadow-lg">
                <div class="ratio ratio-16x9 bg-black">
                    <video id="modalVideoPlayer" controls class="w-100 h-100 bg-black" style="object-fit: contain;">
                        Tu navegador no soporta video HTML5.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- VIDEO ---
    const videoModalEl = document.getElementById('videoModal');
    const modalPlayer = document.getElementById('modalVideoPlayer');
    let videoModalInstance = null;

    function openVideo(src) {
        modalPlayer.src = src;

        // Try Bootstrap Standard Open
        videoModalInstance = new bootstrap.Modal(videoModalEl, {
            backdrop: false, // We handle backdrop manually if needed, or let Bootstrap do it. Let's try standard 'true' first but with manual listeners.
            keyboard: true
        });
        videoModalInstance.show();

        modalPlayer.play().catch(e => console.log("Autoplay prevented", e));
    }

    // --- NUCLEAR CLOSE OPTION ---
    function forceCloseVideo() {
        // 1. Pause Video
        modalPlayer.pause();
        modalPlayer.src = ""; // Unload

        // 2. Hide via Bootstrap Instance if possible
        const instance = bootstrap.Modal.getInstance(videoModalEl);
        if (instance) {
            instance.hide();
        }

        // 3. MANUAL CLEANUP (The "Nuclear" part)
        // Force hide the modal element
        videoModalEl.classList.remove('show');
        videoModalEl.style.display = 'none';
        videoModalEl.setAttribute('aria-hidden', 'true');
        videoModalEl.removeAttribute('aria-modal');
        videoModalEl.removeAttribute('role');

        // Remove Backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(bd => bd.remove());

        // Restore Body Scroll
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    // --- CLICK OUTSIDE TO CLOSE ---
    videoModalEl.addEventListener('click', function (event) {
        // If the click is ON the modal container (the grey overlay), close it
        // If it's on .modal-content or children, check propagation
        if (event.target === videoModalEl) {
            forceCloseVideo();
        }
    });

    // Event checking when modal starts hiding to clean up video (Standard Bootstrap Hook)
    videoModalEl.addEventListener('hide.bs.modal', function () {
        modalPlayer.pause();
    });

    // --- TOGGLE COMPLETADO ---
    async function toggleEjercicio(id) {
        const overlay = document.getElementById(`overlay-${id}`);
        const isCompleted = overlay.style.opacity === '1';

        if (isCompleted) {
            overlay.style.opacity = '0';
            overlay.style.transform = 'scale(1.1)';
            overlay.style.pointerEvents = 'none';
        } else {
            overlay.style.opacity = '1';
            overlay.style.transform = 'scale(1)';
            overlay.style.pointerEvents = 'auto';
            if (navigator.vibrate) navigator.vibrate(50);

            // Auto close if open when marking as completed
            const collapseEl = document.getElementById(`details-${id}`);
            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
            if (bsCollapse) bsCollapse.hide();
        }

        try {
            await fetch('<?= BASE_URL ?>rutinas/toggle', {
                method: 'POST', body: JSON.stringify({ id: id })
            });
        } catch (e) { console.error(e); }
    }

    // --- TOGGLE DETAILS CONDITIONAL ---
    function toggleDetails(id) {
        const overlay = document.getElementById(`overlay-${id}`);
        const isCompleted = overlay.style.opacity === '1';

        // locked if completed
        if (isCompleted) {
            // Optional: Shake effect or visual cue that it is locked
            return;
        }

        const collapseEl = document.getElementById(`details-${id}`);
        // Toggle manually
        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
        bsCollapse.toggle();
    }

    // --- SAVE SERIE ---
    async function saveSerie(id) {
        const reps = document.getElementById(`reps-${id}`).value;
        const peso = document.getElementById(`peso-${id}`).value;
        // Search icon inside the clicked button context
        const btn = event.currentTarget; // The specific button clicked
        const icon = btn.querySelector('i');

        icon.className = 'fas fa-spinner fa-spin';

        try {
            const res = await fetch('<?= BASE_URL ?>rutinas/update_series', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, reps: reps, peso: peso })
            });
            const data = await res.json();

            if (data.ok) {
                icon.className = 'fas fa-check';
                setTimeout(() => icon.className = 'fas fa-save', 2000);
            } else {
                alert('Error al guardar');
                icon.className = 'fas fa-save';
            }
        } catch (e) {
            console.error(e);
            alert('Error de conexión');
            icon.className = 'fas fa-save';
        }
    }

    // --- ADD SERIE (LIVE UPDATE) ---
    async function addSerie(rutinaId) {
        const btn = event.currentTarget;
        const icon = btn.querySelector('i');
        const tbody = document.getElementById(`series-body-${rutinaId}`);

        // Obtener valores de la última serie para copiar
        let lastReps = 10;
        let lastPeso = 0;
        const rows = tbody.querySelectorAll('tr[id^="serie-row-"]');
        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            const lastId = lastRow.id.replace('serie-row-', '');
            lastReps = document.getElementById(`reps-${lastId}`).value;
            lastPeso = document.getElementById(`peso-${lastId}`).value;
        }

        icon.className = 'fas fa-spinner fa-spin';

        try {
            const res = await fetch('<?= BASE_URL ?>rutinas/add_serie', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rutina_id: rutinaId, reps: lastReps, peso: lastPeso })
            });
            const data = await res.json();

            if (data.ok && data.id) {
                icon.className = 'fas fa-plus';

                // Borrar mensaje empty si existe
                const emptyMsg = document.getElementById(`empty-msg-${rutinaId}`);
                if (emptyMsg) emptyMsg.remove();

                // Crear nueva fila HTML
                const newIdx = rows.length + 1;
                const newRow = `
                    <tr id="serie-row-${data.id}" class="animate-slide-up">
                        <td class="fw-bold text-primary serie-index">Serie ${newIdx}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm bg-black border-secondary text-white text-center" 
                                   style="width: 70px;" value="${lastReps}" id="reps-${data.id}">
                        </td>
                        <td>
                            <input type="number" step="0.5" class="form-control form-control-sm bg-black border-secondary text-white text-center" 
                                   style="width: 70px;" value="${lastPeso}" id="peso-${data.id}">
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-success text-white" onclick="saveSerie(${data.id})">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSerie(${data.id})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', newRow);

                // Scroll suave hacia la nueva serie
                document.getElementById(`serie-row-${data.id}`).scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            } else {
                alert('No se pudo agregar');
                icon.className = 'fas fa-plus';
            }
        } catch (e) {
            console.error(e);
            icon.className = 'fas fa-plus';
        }
    }

    // --- DELETE SERIE (LIVE UPDATE) ---
    async function deleteSerie(serieId) {
        if (!confirm('¿Borrar esta serie?')) return;

        // Optimistic UI Removal?
        // Mejor esperar confirmación para evitar desincronización

        try {
            const res = await fetch('<?= BASE_URL ?>rutinas/delete_serie', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: serieId })
            });
            const data = await res.json();

            if (data.ok) {
                const row = document.getElementById(`serie-row-${serieId}`);
                if (row) {
                    const tbody = row.parentElement;
                    row.remove();

                    // Re-enumerar series
                    const rows = tbody.querySelectorAll('tr[id^="serie-row-"]');
                    rows.forEach((r, idx) => {
                        r.querySelector('.serie-index').innerText = `Serie ${idx + 1}`;
                    });

                    // Si no quedan, mostrar mensaje
                    if (rows.length === 0) {
                        const rutinaId = tbody.id.replace('series-body-', '');
                        tbody.innerHTML = `<tr id="empty-msg-${rutinaId}"><td colspan="4" class="text-center text-muted">Sin series detalladas</td></tr>`;
                    }
                }
            } else {
                alert('No se pudo borrar');
            }
        } catch (e) { console.error(e); }
    }

    // --- DELETE EXERCISE ---
    async function deleteEjercicio(id) {
        if (!confirm('¿Estás seguro de eliminar todo el ejercicio?')) return;

        try {
            const res = await fetch('<?= BASE_URL ?>rutinas/eliminar_ejercicio', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();

            if (data.ok) {
                // Eliminar del DOM con animación
                const card = document.getElementById(`card-${id}`).parentElement; // El wrapper
                card.style.transition = 'all 0.5s';
                card.style.opacity = '0';
                card.style.transform = 'translateX(100px)';
                setTimeout(() => card.remove(), 500);
            } else {
                alert('No se pudo eliminar');
            }
        } catch (e) {
            console.error(e);
            alert('Error de conexión');
        }
    }
</script>

<style>
    /* ... Existing styles ... */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* FUENTES & TEXTOS */
    .font-teko {
        font-family: 'Teko', sans-serif;
    }

    .ls-3 {
        letter-spacing: 3px;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    /* EFECTOS */
    .text-shadow-neon {
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3), 0 0 20px rgba(255, 255, 255, 0.1);
    }

    .text-shadow-green {
        text-shadow: 0 0 20px rgba(34, 197, 94, 0.8);
    }

    .glow-text-sm {
        text-shadow: 0 0 5px rgba(139, 92, 246, 0.8);
    }

    /* UTILIDADES */
    .group:hover .group-hover-opacity-100 {
        opacity: 1 !important;
    }

    .group:hover .group-hover-text-primary {
        color: #8b5cf6 !important;
        text-shadow: 0 0 15px rgba(139, 92, 246, 0.6);
    }

    /* TARJETAS CYBERPUNK */
    .card-cyber {
        background: #0f0f0f;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }

    .card-cyber:hover {
        transform: translateY(-5px) scale(1.02);
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 20px 40px -10px rgba(139, 92, 246, 0.2);
    }

    /* GRADIENTS & GLOWS */
    .bg-gradient-to-r {
        background: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
    }

    .from-transparent {
        --tw-gradient-from: transparent;
    }

    .to-black {
        --tw-gradient-to: #0f0f0f;
    }

    .shadow-success-glow {
        filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.6));
    }

    /* ANIMACIONES */
    .animate-slide-up {
        animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .blur-xl {
        filter: blur(24px);
    }

    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }

    /* POINTER EVENTS UTILS */
    .pointer-events-none {
        pointer-events: none !important;
    }

    .pointer-events-all {
        pointer-events: auto !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>