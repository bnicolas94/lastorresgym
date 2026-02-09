<div class="container-fluid p-0 min-vh-100 d-flex flex-column bg-black">

    <!-- HEADER FLOTANTE -->
    <div
        class="sticky-top bg-black bg-opacity-90 backdrop-blur-md border-bottom border-white border-opacity-10 z-3 px-3 py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="<?= BASE_URL ?>rutinas"
                    class="btn btn-sm btn-icon btn-dark me-3 rounded-circle border border-secondary"><i
                        class="fas fa-arrow-left"></i></a>
                <div>
                    <h6 class="text-uppercase text-secondary ls-2 mb-0" style="font-size: 0.65rem;">MI RUTINA</h6>
                    <h4 class="text-white font-teko mb-0 lh-1 text-uppercase">CREAR NUEVA</h4>
                </div>
            </div>
            <button class="btn btn-outline-primary rounded-pill px-4" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasPlanilla">
                <i class="fas fa-clipboard-list me-2"></i> <span id="btnPlanillaCount">0</span> Ejercicios
            </button>
        </div>
    </div>

    <!-- MAIN STEPPER (PASOS) -->
    <div class="container py-4">

        <!-- INDICADOR DE PASOS -->
        <div class="d-flex justify-content-center mb-5 animate-fade-in">
            <div class="d-flex align-items-center position-relative w-100 w-md-75">
                <!-- Linea conectora -->
                <div class="position-absolute w-100 top-50 start-0 translate-middle-y bg-secondary bg-opacity-25 rounded"
                    style="height: 2px;"></div>

                <!-- Paso 1 -->
                <div class="position-relative z-1 bg-black pe-3 step-indicator active" id="badgeStep1">
                    <div class="d-flex align-items-center text-primary">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-primary-glow"
                            style="width: 32px; height: 32px;">1</div>
                        <span class="ms-2 font-teko h5 mb-0 text-uppercase d-none d-sm-block">Día</span>
                    </div>
                </div>

                <!-- Spacer -->
                <div class="flex-grow-1"></div>

                <!-- Paso 2 -->
                <div class="position-relative z-1 bg-black px-3 step-indicator text-muted opacity-50" id="badgeStep2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center fw-bold"
                            style="width: 32px; height: 32px;">2</div>
                        <span class="ms-2 font-teko h5 mb-0 text-uppercase d-none d-sm-block">Zona</span>
                    </div>
                </div>

                <!-- Spacer -->
                <div class="flex-grow-1"></div>

                <!-- Paso 3 -->
                <div class="position-relative z-1 bg-black ps-3 step-indicator text-muted opacity-50" id="badgeStep3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center fw-bold"
                            style="width: 32px; height: 32px;">3</div>
                        <span class="ms-2 font-teko h5 mb-0 text-uppercase d-none d-sm-block">Ejercicio</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR VISTAS (STEPS) -->
        <div id="stepContainer">

            <!-- PASO 1: SELECCIONAR DÍA -->
            <div id="viewStep1" class="step-view animate-fade-in">
                <div class="text-center mb-4">
                    <h2 class="text-white font-teko display-5 text-uppercase">¿Qué día entrenamos?</h2>
                    <p class="text-muted">Semana Actual: <span class="text-white fw-bold">#
                            <?= date('W') ?>
                        </span></p>
                </div>

                <div class="d-flex justify-content-center mb-4">
                    <div class="input-group w-auto">
                        <button class="btn btn-dark border-secondary text-muted hover-text-white" type="button"
                            onclick="dec('globalSemana')"><i class="fas fa-minus"></i></button>
                        <span
                            class="input-group-text bg-dark border-secondary text-white border-start-0 border-end-0">Semana</span>
                        <input type="number" id="globalSemana"
                            class="form-control bg-black border-secondary text-white text-center fw-bold"
                            value="<?= date('W') ?>" style="max-width: 80px;">
                        <button class="btn btn-dark border-secondary text-muted hover-text-white" type="button"
                            onclick="inc('globalSemana')"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <div class="row g-3 justify-content-center">
                    <?php
                    // Mapeo Dia 1 a Dia 7
                    $dias = ['Día 1', 'Día 2', 'Día 3', 'Día 4', 'Día 5', 'Día 6', 'Día 7'];
                    foreach ($dias as $idx => $nombre):
                        $val = $idx + 1;
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card-option h-100 p-4 rounded-4 border border-secondary border-opacity-25 bg-surface-dark cursor-pointer text-center position-relative overflow-hidden group-hover-border-primary transition-all"
                                onclick="selectDay(<?= $val ?>, '<?= $nombre ?>')">

                                <div
                                    class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-to-br from-transparent to-primary opacity-0 group-hover-opacity-10 transition-all">
                                </div>

                                <h1
                                    class="display-3 font-teko fw-bold text-white mb-0 opacity-25 group-hover-opacity-100 transition-all">
                                    <?= $val ?>
                                </h1>
                                <h4 class="text-uppercase text-secondary group-hover-text-white transition-all ls-2">
                                    <?= $nombre ?>
                                </h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PASO 2: SELECCIONAR ZONA (CATEGORÍA) -->
            <div id="viewStep2" class="step-view d-none animate-fade-in">
                <div class="text-center mb-4">
                    <h5 class="text-primary text-uppercase ls-2 mb-2" id="labelDiaSeleccionado">DÍA SELECCIONADO</h5>
                    <h2 class="text-white font-teko display-5 text-uppercase">¿Qué zona entrenamos?</h2>
                </div>

                <div class="row g-3">
                    <?php foreach ($ejercicios as $index => $cat): ?>
                        <div class="col-12 col-md-6 col-lg-4 animate-fade-up"
                            style="animation-delay: <?= $index * 100 ?>ms">
                            <div class="card-zone h-100 rounded-4 overflow-hidden position-relative cursor-pointer transition-transform hover-scale-sm"
                                onclick="selectZone(<?= $cat['id'] ?>, '<?= $cat['nombre'] ?>')">

                                <!-- Background Image (Carga Robusta) -->
                                <?php
                                $cleanName = strtolower($cat['nombre']);
                                $cleanName = str_replace(
                                    ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
                                    ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n'],
                                    $cleanName
                                );
                                $cleanName = str_replace(' ', '', $cleanName);
                                $imgName = $cleanName . '.png';

                                // Base64 Logic
                                $fsPath = dirname(__DIR__, 3) . '/public/img/categorias/' . $imgName;
                                $imgSrc = "";
                                $hasImage = false;

                                if (file_exists($fsPath)) {
                                    $data = file_get_contents($fsPath);
                                    if ($data !== false) {
                                        $imgSrc = 'data:image/png;base64,' . base64_encode($data);
                                        $hasImage = true;
                                    }
                                }
                                ?>

                                <!-- Contenedor de Imagen -->
                                <?php if ($hasImage): ?>
                                    <img src="<?= $imgSrc ?>" alt="<?= $cat['nombre'] ?>"
                                        class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                        style="z-index: 0;">
                                <?php else: ?>
                                    <!-- Fallback Gradiente -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100"
                                        style="background: linear-gradient(45deg, #1a1a1a, #2d2d2d); z-index: 0;">
                                    </div>
                                <?php endif; ?>

                                <!-- Overlays (Asegurando Transparencia) -->
                                <!-- Capa oscura general muy suave (20%) -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 transition-all"
                                    style="z-index: 1; background-color: rgba(0,0,0,0.2);">
                                </div>

                                <!-- Gradiente inferior para texto (Garantizado) -->
                                <div class="position-absolute bottom-0 start-0 w-100 h-50"
                                    style="z-index: 1; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);">
                                </div>

                                <!-- Content -->
                                <div class="position-relative z-1 p-4 d-flex flex-column justify-content-end h-100"
                                    style="min-height: 220px;">
                                    <h1
                                        class="font-teko text-white display-4 mb-0 lh-1 text-uppercase text-shadow transform-origin-bottom-left transition-transform group-hover-scale-110">
                                        <?= $cat['nombre'] ?>
                                    </h1>
                                    <div
                                        class="d-flex align-items-center text-primary mt-2 opacity-75 group-hover-opacity-100 transition-all">
                                        <span class="small text-uppercase fw-bold ls-1">Seleccionar Zona</span>
                                        <i class="fas fa-arrow-right ms-2 moving-arrow"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-5 text-center">
                    <button class="btn btn-link text-muted text-decoration-none" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left me-2"></i> Volver a Días
                    </button>
                </div>
            </div>

            <!-- PASO 3: SELECCIONAR EJERCICIO -->
            <div id="viewStep3" class="step-view d-none animate-fade-in">
                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom border-dark pb-3">
                    <div>
                        <h6 class="text-muted text-uppercase ls-2" id="labelZoneBreadcrumb">DÍA > ZONA</h6>
                        <h2 class="text-white font-teko display-6 mb-0">ELEGÍ EL EJERCICIO</h2>
                    </div>
                </div>

                <!-- Input Buscador -->
                <div class="mb-4">
                    <input type="text" id="inputBuscarEjercicio"
                        class="form-control bg-dark border-secondary text-white py-3 rounded-pill ps-4"
                        placeholder="🔍 Buscar ejercicio...">
                </div>

                <div class="row g-3" id="gridEjercicios">
                    <!-- JS rellena esto -->
                </div>

                <div class="mt-5 text-center">
                    <button class="btn btn-link text-muted text-decoration-none" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left me-2"></i> Volver a Zonas
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- OFFCANVAS PLANILLA (STAGING) -->
<div class="offcanvas offcanvas-end bg-black border-start border-secondary" tabindex="-1" id="offcanvasPlanilla"
    style="z-index: 2060; touch-action: none; user-select: none;">
    <div class="offcanvas-header border-bottom border-dark">
        <h5 class="offcanvas-title font-teko text-white h3">TU RUTINA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">

        <div class="p-3 bg-dark-subtle border-bottom border-dark">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">SEMANA</span>
                <strong class="text-white" id="summarySemana">Current</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted small">DÍA</span>
                <strong class="text-primary" id="summaryDia">--</strong>
            </div>
        </div>

        <div class="flex-grow-1 overflow-y-auto p-3" id="contenedorItemsPlanilla">
            <div class="text-center py-5 text-muted opacity-50">
                <i class="fas fa-dumbbell fa-3x mb-3"></i>
                <p>No hay ejercicios cargados.</p>
            </div>
        </div>

        <div class="p-3 bg-dark border-top border-secondary">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input bg-primary border-0" type="checkbox" id="checkReplicarAnio">
                <label class="form-check-label text-white small" for="checkReplicarAnio">Replicar todo el año</label>
            </div>
            <button class="btn btn-primary w-100 py-3 fw-bold shadow-primary text-uppercase ls-1"
                onclick="confirmarAsignacion()">
                <i class="fas fa-check-circle me-2"></i> Confirmar
            </button>
        </div>
    </div>
</div>

<!-- MODAL CONFIGURAR EJERCICIO -->
<div class="modal fade" id="modalConfigExercise" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary text-white">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title font-teko text-uppercase" id="modalEjTitle">Configurar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalEjId">
                <input type="hidden" id="modalEjName">
                <input type="hidden" id="modalCatId">

                <div class="row g-3">
                    <div class="col-4">
                        <label class="form-label text-muted small text-uppercase text-center w-100">Series</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" onclick="dec('inputSeries')">-</button>
                            <input type="number" id="inputSeries"
                                class="form-control text-center bg-black text-white border-secondary" value="4">
                            <button class="btn btn-outline-secondary" onclick="inc('inputSeries')">+</button>
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label text-muted small text-uppercase text-center w-100">Reps</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" onclick="dec('inputReps')">-</button>
                            <input type="number" id="inputReps"
                                class="form-control text-center bg-black text-white border-secondary" value="10">
                            <button class="btn btn-outline-secondary" onclick="inc('inputReps')">+</button>
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label text-muted small text-uppercase text-center w-100">Peso (Kg)</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" onclick="dec('inputPeso', 2.5)">-</button>
                            <input type="number" id="inputPeso"
                                class="form-control text-center bg-black text-white border-secondary" value="10"
                                step="2.5">
                            <button class="btn btn-outline-secondary" onclick="inc('inputPeso', 2.5)">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-25 p-2">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="agregarAPlanilla()">
                    <i class="fas fa-plus me-2"></i> AGREGAR LISTO
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // DATA
    const ejerciciosData = <?= $ejerciciosJson ?>; // Structure: [{id, nombre, ejercicios: []}, ...]
    // No alumnoID needed, handled by session
    const baseUrl = "<?= BASE_URL ?>";

    // STATE
    let currentStep = 1;
    let selectedSemana = <?= date('W') ?>;
    let selectedDia = null; // 1-7
    let selectedDiaNombre = '';
    let selectedZoneId = null;
    let selectedZoneName = '';

    let stagingList = [];

    // DOM ELEMENTS
    const steps = [document.getElementById('viewStep1'), document.getElementById('viewStep2'), document.getElementById('viewStep3')];
    const badges = [document.getElementById('badgeStep1'), document.getElementById('badgeStep2'), document.getElementById('badgeStep3')];

    // --- NAVIGATOR ---
    function goToStep(step) {
        currentStep = step;

        // Hide all views
        steps.forEach(el => el.classList.add('d-none'));
        // Show current
        steps[step - 1].classList.remove('d-none');

        // Styles Indicators
        badges.forEach((el, idx) => {
            if (idx + 1 === step) {
                el.classList.add('active');
                el.classList.remove('opacity-50');
                el.querySelector('.rounded-circle').classList.add('bg-primary', 'text-white', 'shadow-primary-glow');
                el.querySelector('.rounded-circle').classList.remove('bg-dark', 'border');
            } else if (idx + 1 < step) {
                // Completed
                el.classList.add('opacity-50'); // Dim completed too
                el.querySelector('.rounded-circle').innerHTML = '<i class="fas fa-check"></i>';
            } else {
                // Future
                el.classList.remove('active');
                el.classList.add('opacity-50');
                el.querySelector('.rounded-circle').innerHTML = idx + 1;
                el.querySelector('.rounded-circle').classList.remove('bg-primary', 'text-white', 'shadow-primary-glow');
                el.querySelector('.rounded-circle').classList.add('bg-dark', 'border');
            }
        });
    }

    // --- STEP 1: DAY ---
    function selectDay(diaNum, diaNombre) {
        selectedDia = diaNum;
        selectedDiaNombre = diaNombre;
        selectedSemana = document.getElementById('globalSemana').value;

        // Update summary Labels
        document.getElementById('labelDiaSeleccionado').innerText = 'DÍA: ' + diaNombre.toUpperCase();
        document.getElementById('summaryDia').innerText = diaNombre.toUpperCase();
        document.getElementById('summarySemana').innerText = '#' + selectedSemana;

        goToStep(2);
    }

    // --- STEP 2: ZONE ---
    function selectZone(zoneId, zoneName) {
        selectedZoneId = zoneId;
        selectedZoneName = zoneName;

        document.getElementById('labelZoneBreadcrumb').innerText = `${selectedDiaNombre} > ${zoneName}`;

        // Render Exercises for this zone
        renderExercises(zoneId);

        goToStep(3);
    }

    function renderExercises(zoneId) {
        const grid = document.getElementById('gridEjercicios');
        grid.innerHTML = '';

        const category = ejerciciosData.find(c => c.id == zoneId);
        if (!category || !category.ejercicios) return;

        category.ejercicios.forEach((ex, idx) => {
            // Check if already in staging
            const inStaging = stagingList.some(item => item.ejercicio_id == ex.id);
            const statusClass = inStaging ? 'border-success shadow-success-glow' : 'border-secondary border-opacity-25';
            const icon = inStaging ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '';
            const btnText = inStaging ? 'AGREGADO' : '+ AGREGAR';
            const btnClass = inStaging ? 'btn-success' : 'btn-outline-primary';

            const col = document.createElement('div');
            col.className = 'col-12 col-md-6 col-lg-4 animate-fade-up';
            col.style.animationDelay = (idx * 50) + 'ms'; // Staggered delay (más rápido para listas largas)

            col.innerHTML = `
                <div class="card-option p-3 bg-surface-dark border ${statusClass} rounded-3 h-100 d-flex flex-column justify-content-between transition-all" onclick="openConfig(${ex.id}, ${zoneId})">
                    <div>
                        <h5 class="text-white font-teko text-uppercase mb-1" style="font-size: 1.5rem;">${ex.nombre} ${icon}</h5>
                    </div>
                     <button class="btn btn-sm ${btnClass} w-100 mt-3 fw-bold rounded-pill">${btnText}</button>
                </div>
            `;
            grid.appendChild(col);
        });
    }

    // --- STEP 3: EXERCISE CONFIG ---
    let configModal; // init later safely

    function openConfig(ejId, catId) {
        if (!configModal) {
            const el = document.getElementById('modalConfigExercise');
            if (el) configModal = new bootstrap.Modal(el);
            else return alert("Error: Modal no encontrado");
        }

        // Lookup Data Safe
        const category = ejerciciosData.find(c => c.id == catId);
        if (!category) return;
        const ex = category.ejercicios.find(e => e.id == ejId);
        if (!ex) return;

        document.getElementById('modalEjId').value = ex.id;
        document.getElementById('modalEjName').value = ex.nombre;
        document.getElementById('modalCatId').value = catId;
        document.getElementById('modalEjTitle').innerText = ex.nombre;

        // Reset defaults
        document.getElementById('inputSeries').value = 4;
        document.getElementById('inputReps').value = 10;
        document.getElementById('inputPeso').value = 10;

        configModal.show();
    }

    function inc(id, step = 1) {
        const el = document.getElementById(id);
        el.value = parseFloat(el.value) + step;
    }
    function dec(id, step = 1) {
        const el = document.getElementById(id);
        if (el.value > 0) el.value = parseFloat(el.value) - step;
    }

    function agregarAPlanilla() {
        const id = document.getElementById('modalEjId').value;
        const name = document.getElementById('modalEjName').value;
        const catId = document.getElementById('modalCatId').value;

        const s = document.getElementById('inputSeries').value;
        const r = document.getElementById('inputReps').value;
        const p = document.getElementById('inputPeso').value;

        /*
        // Remove if exists previously to replace -> REMOVED TO ALLOW DUPLICATES
        const existingIdx = stagingList.findIndex(x => x.ejercicio_id == id);
        if (existingIdx >= 0) stagingList.splice(existingIdx, 1);
        */

        stagingList.push({
            ejercicio_id: id,
            nombre: name,
            categoria_id: catId,
            series: parseInt(s),
            repeticiones: parseInt(r),
            peso: parseFloat(p)
        });

        configModal.hide();
        updatePlanillaUI();

        // Re-render grid to show checkmark
        renderExercises(selectedZoneId);

        // Show offcanvas automatically? maybe just badge update
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasPlanilla');
        bsOffcanvas.show(); // Show feedback
    }

    // --- PLANILLA (STAGING) ---
    function updatePlanillaUI() {
        const container = document.getElementById('contenedorItemsPlanilla');
        document.getElementById('btnPlanillaCount').innerText = stagingList.length;

        if (stagingList.length === 0) {
            container.innerHTML = `<div class="text-center py-5 text-muted opacity-50"><i class="fas fa-dumbbell fa-3x mb-3"></i><p>Vacío.</p></div>`;
            return;
        }

        let html = '';
        stagingList.forEach((item, idx) => {
            html += `
                <div class="card bg-black border border-secondary border-opacity-25 mb-2">
                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white small text-uppercase">${item.nombre}</div>
                            <div class="text-muted small">${item.series} x ${item.repeticiones} | ${item.peso}kg</div>
                        </div>
                        <button class="btn btn-sm text-danger" onclick="removeFromPlanilla(${idx})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function removeFromPlanilla(idx) {
        stagingList.splice(idx, 1);
        updatePlanillaUI();
        // Update grid if visible
        if (!document.getElementById('viewStep3').classList.contains('d-none')) {
            renderExercises(selectedZoneId);
        }
    }

    async function confirmarAsignacion() {
        if (stagingList.length === 0) return alert("Planilla vacía.");

        const replicar = document.getElementById('checkReplicarAnio').checked;

        const payload = {
            // alumno_id se maneja en session
            semana: selectedSemana,
            dia_semana: selectedDia,
            tipo: 'manual',
            ejercicios: stagingList,
            replicar_anio: replicar
        };

        if (!confirm("¿Confirmar asignación?")) return;

        try {
            const res = await fetch(baseUrl + 'rutinas/guardar_wizard', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.ok) {
                alert("¡Creado con éxito!");
                stagingList = [];
                updatePlanillaUI();
                renderExercises(selectedZoneId);

                // Go back to Step 1
                goToStep(1);
                // O redirigir a ver el día? window.location.href = baseUrl + 'rutinas/dia/' + selectedDia;
            } else {
                alert("Error: " + data.mensaje);
            }
        } catch (e) {
            console.error(e);
            alert("Error de conexión");
        }
    }

    // Search Filter
    document.getElementById('inputBuscarEjercicio').addEventListener('keyup', function (e) {
        const term = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('#gridEjercicios > div');
        cards.forEach(col => {
            const txt = col.innerText.toLowerCase();
            col.style.display = txt.includes(term) ? 'block' : 'none';
        });
    });

    // --- SWIPE TO CLOSE (Mouse + Touch Support) ---
    const offcanvasEl = document.getElementById('offcanvasPlanilla');
    let isDragging = false;
    let startX = 0;
    let currentTranslate = 0;

    // Funciones Helper Unificadas
    const getX = (e) => e.type.includes('mouse') ? e.clientX : e.changedTouches[0].clientX;

    const handleStart = (e) => {
        // Evitar conflicto con scroll vertical si no es intencional, 
        // pero para offcanvas lateral está bien.
        isDragging = true;
        startX = getX(e);
        offcanvasEl.style.transition = 'none';
        offcanvasEl.style.cursor = 'grabbing';
    };

    const handleMove = (e) => {
        if (!isDragging) return;

        const currentX = getX(e);
        const diff = currentX - startX;

        // Solo permitir arrastre a la derecha (positivo)
        if (diff > 0) {
            currentTranslate = diff;
            offcanvasEl.style.transform = `translateX(${diff}px)`;
        }
    };

    const handleEnd = (e) => {
        if (!isDragging) return;
        isDragging = false;
        offcanvasEl.style.cursor = '';
        offcanvasEl.style.transition = 'transform 0.3s ease-in-out';

        if (currentTranslate > 100) {
            // CERRAR
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) bsOffcanvas.hide();

            // Limpieza post-animación
            setTimeout(() => {
                offcanvasEl.style.transform = '';
                currentTranslate = 0;
            }, 300);
        } else {
            // REBOTAR
            offcanvasEl.style.transform = '';
            currentTranslate = 0;
        }
    };

    // Event Listeners (Touch)
    offcanvasEl.addEventListener('touchstart', handleStart, { passive: true });
    offcanvasEl.addEventListener('touchmove', handleMove, { passive: true });
    offcanvasEl.addEventListener('touchend', handleEnd, { passive: true });

    // Event Listeners (Mouse - PC Debugging)
    offcanvasEl.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove); // Document para no perder el drag si sale rápido
    document.addEventListener('mouseup', handleEnd);

</script>

<style>
    /* Custom Styles for Wizard */
    .font-teko {
        font-family: 'Teko', sans-serif;
    }

    .card-zone {
        cursor: pointer;
    }

    .card-zone:hover {
        transform: scale(1.02);
        z-index: 10;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .text-shadow {
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
    }

    .step-indicator {
        transition: all 0.3s ease;
    }

    .step-indicator.active .rounded-circle {
        transform: scale(1.1);
    }

    .shadow-primary-glow {
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.5);
    }

    .shadow-success-glow {
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
    }

    .bg-surface-dark {
        background-color: #1a1a1a;
    }

    .group-hover-border-primary:hover {
        border-color: #8b5cf6 !important;
    }

    .group-hover-opacity-100:hover {
        opacity: 1 !important;
    }

    /* Scrollbar dark */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #111;
    }

    ::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Animaciones Extra */
    .group-hover-scale-110:hover,
    .card-zone:hover .group-hover-scale-110 {
        transform: scale(1.05);
    }

    .group-hover-opacity-100:hover,
    .card-zone:hover .group-hover-opacity-100 {
        opacity: 1 !important;
    }

    .moving-arrow {
        transition: transform 0.3s ease;
    }

    .card-zone:hover .moving-arrow {
        transform: translateX(5px);
    }

    .bg-gradient-to-t {
        background: linear-gradient(to top, var(--bs-black) 0%, transparent 100%);
    }

    /* --- ANIMACIONES PERSONALES --- */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 20px, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    .animate-fade-up {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
        /* Inicialmente invisible */
    }
</style>