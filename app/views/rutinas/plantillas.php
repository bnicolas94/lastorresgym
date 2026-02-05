<div class="container-fluid p-0 animate-fade-in">
    <!-- HEADER -->
    <div class="border-bottom border-secondary border-opacity-10 py-4 mb-4">
        <div class="px-3 px-md-5 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="text-primary text-uppercase ls-2 mb-1">GESTIÓN PROFESIONAL</h5>
                <h1 class="theme-text font-teko display-4 mb-0 lh-1">CONSTRUCTOR DE PLANTILLAS</h1>
            </div>
            <div class="d-none d-md-block">
                <i class="fas fa-layer-group fa-3x text-primary opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="px-3 px-md-5">
        <div class="row g-4">
            <!-- BUILDER CARD -->
            <div class="col-12 col-xl-8">
                <div class="card-custom position-relative overflow-hidden rounded-4 shadow-sm">
                    <div class="border-bottom border-secondary border-opacity-25 p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h3 class="theme-text font-teko mb-0 ls-1">ARMAR NUEVA RUTINA PERSONALIZADA</h3>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="theme-text opacity-75 small text-uppercase ls-1 mb-2">Nombre de la
                                    Rutina</label>
                                <input type="text" id="inputNombreRutina"
                                    class="form-control form-control-custom py-3 ls-1"
                                    placeholder="Ej: ESPALDA/PIERNAS">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="theme-text opacity-75 small text-uppercase ls-1 mb-2">Zona /
                                    Categoría</label>
                                <select id="selectCategoria" class="form-select form-control-custom py-3 ls-1"
                                    onchange="onCategoryChange()">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($ejercicios as $cat): ?>
                                        <option value="<?= $cat['id'] ?>">
                                            <?= $cat['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="theme-text opacity-75 small text-uppercase ls-1 mb-2">Ejercicio</label>
                                <select id="selectEjercicio" class="form-select form-control-custom py-3 ls-1" disabled>
                                    <option value="">Seleccionar zona primero</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100 py-3 fw-bold ls-1 shadow-primary"
                                    onclick="agregarAlCuerpo()">
                                    AGREGAR
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 min-vh-50">
                        <div id="contenedorEjerciciosCuerpo" class="row g-3">
                            <!-- Aquí se renderizan los ejercicios agregados -->
                            <div class="col-12 text-center py-5 opacity-50 theme-text" id="mensajeVacio">
                                <i class="fas fa-dumbbell fa-3x mb-3 text-secondary"></i>
                                <p>Aún no has agregado ejercicios a esta planta.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="card-footer border-top border-secondary border-opacity-25 p-4 text-center bg-transparent">
                        <button class="btn btn-primary btn-lg px-5 py-3 fw-bold ls-1 shadow-primary"
                            onclick="guardarPlantilla()">
                            <i class="fas fa-cloud-upload-alt me-2"></i> GUARDAR PLANTILLA COMPLETA
                        </button>
                    </div>
                </div>
            </div>

            <!-- LIST SIDEBAR -->
            <div class="col-12 col-xl-4">
                <div class="card-custom position-relative overflow-hidden rounded-4 h-100">
                    <div class="card-header border-bottom border-secondary border-opacity-25 p-4 bg-transparent">
                        <h3 class="theme-text font-teko mb-0 ls-1">PLANTILLAS GUARDADAS</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="listaPlantillas">
                            <?php if (empty($plantillas)): ?>
                                <div class="p-4 text-center text-muted opacity-50">
                                    No hay plantillas creadas por ti aún.
                                </div>
                            <?php else: ?>
                                <?php foreach ($plantillas as $p): ?>
                                    <div
                                        class="list-group-item bg-transparent border-secondary border-opacity-10 p-3 d-flex justify-content-between align-items-center group-hover-bg">
                                        <div>
                                            <h5 class="theme-text mb-0 font-teko text-uppercase ls-1">
                                                <?= $p['nombre'] ?>
                                            </h5>
                                            <span class="text-muted tiny text-uppercase">ID: #
                                                <?= $p['id'] ?>
                                            </span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger border-0 opacity-50 hover-opacity-100"
                                            onclick="eliminarPlantilla(<?= $p['id'] ?>, '<?= $p['nombre'] ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const maestroEjercicios = <?= $ejerciciosJson ?>;
    const baseUrl = "<?= BASE_URL ?>";
    let ejerciciosVocal = []; // Lo que se está armando

    function onCategoryChange() {
        const catId = document.getElementById('selectCategoria').value;
        const selectEj = document.getElementById('selectEjercicio');

        selectEj.innerHTML = '';
        if (!catId) {
            selectEj.innerHTML = '<option value="">Seleccionar zona primero</option>';
            selectEj.disabled = true;
            return;
        }

        const categoria = maestroEjercicios.find(c => c.id == catId);
        if (categoria && categoria.ejercicios) {
            let html = '<option value="">Seleccionar...</option>';
            categoria.ejercicios.forEach(e => {
                html += `<option value="${e.id}">${e.nombre}</option>`;
            });
            selectEj.innerHTML = html;
            selectEj.disabled = false;
        }
    }

    function agregarAlCuerpo() {
        const catId = document.getElementById('selectCategoria').value;
        const ejId = document.getElementById('selectEjercicio').value;

        if (!catId || !ejId) return alert("Por favor selecciona Categoría y Ejercicio");

        const categoria = maestroEjercicios.find(c => c.id == catId);
        const ejercicio = categoria.ejercicios.find(e => e.id == ejId);

        // Evitar duplicados en el mismo constructor si se desea
        if (ejerciciosVocal.some(ex => ex.ejercicio_id == ejId)) {
            return alert("Este ejercicio ya está en la lista.");
        }

        ejerciciosVocal.push({
            ejercicio_id: ejId,
            nombre: ejercicio.nombre,
            categoria_id: catId,
            categoria_nombre: categoria.nombre,
            series: 4,
            repeticiones: 10,
            peso: 10
        });

        renderBuilder();
    }

    function renderBuilder() {
        const container = document.getElementById('contenedorEjerciciosCuerpo');
        const mensajeVacio = document.getElementById('mensajeVacio');

        if (ejerciciosVocal.length === 0) {
            mensajeVacio.classList.remove('d-none');
            // Remove everything else
            Array.from(container.children).forEach(child => {
                if (child.id !== 'mensajeVacio') child.remove();
            });
            return;
        }

        mensajeVacio.classList.add('d-none');
        // Limpiamos contenido anterior no-mensaje
        Array.from(container.children).forEach(child => {
            if (child.id !== 'mensajeVacio') child.remove();
        });

        ejerciciosVocal.forEach((ex, idx) => {
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6 col-xxl-4 animate-scale-in';
            col.innerHTML = `
                <div class="card-custom border border-secondary border-opacity-25 rounded-3 p-3 h-100 position-relative">
                    <button class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 p-2 text-decoration-none" onclick="quitarDelCuerpo(${idx})">
                        <i class="fas fa-times-circle"></i>
                    </button>
                    <div class="mb-3">
                        <h5 class="theme-text font-teko text-uppercase ls-1 mb-0">${ex.nombre}</h5>
                        <span class="text-primary tiny text-uppercase fw-bold">${ex.categoria_nombre}</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="theme-text opacity-75 tiny text-uppercase d-block mb-1 text-center">Series</label>
                            <input type="number" class="form-control form-control-sm form-control-custom text-center" 
                                value="${ex.series}" onchange="updateEx(${idx}, 'series', this.value)">
                        </div>
                        <div class="col-4">
                            <label class="theme-text opacity-75 tiny text-uppercase d-block mb-1 text-center">Reps</label>
                            <input type="number" class="form-control form-control-sm form-control-custom text-center" 
                                value="${ex.repeticiones}" onchange="updateEx(${idx}, 'repeticiones', this.value)">
                        </div>
                        <div class="col-4">
                            <label class="theme-text opacity-75 tiny text-uppercase d-block mb-1 text-center">Peso</label>
                            <input type="number" class="form-control form-control-sm form-control-custom text-center" 
                                value="${ex.peso}" step="0.5" onchange="updateEx(${idx}, 'peso', this.value)">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });
    }

    function updateEx(idx, field, val) {
        ejerciciosVocal[idx][field] = val;
    }

    function quitarDelCuerpo(idx) {
        ejerciciosVocal.splice(idx, 1);
        renderBuilder();
    }

    async function guardarPlantilla() {
        const nombre = document.getElementById('inputNombreRutina').value;
        if (!nombre) return alert("Por favor ingresa un nombre para la rutina.");
        if (ejerciciosVocal.length === 0) return alert("Agrega al menos un ejercicio.");

        if (!confirm(`¿Guardar plantilla "${nombre}"?`)) return;

        try {
            const res = await fetch(`${baseUrl}rutinas/guardar_plantilla`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre, ejercicios: ejerciciosVocal })
            });
            const data = await res.json();
            if (data.ok) {
                alert("¡Plantilla guardada con éxito!");
                location.reload();
            } else {
                alert("Error: " + data.mensaje);
            }
        } catch (e) {
            console.error(e);
            alert("Error de conexión");
        }
    }

    async function eliminarPlantilla(id, nombre) {
        if (!confirm(`¿Estás seguro de eliminar la plantilla "${nombre}"?`)) return;

        try {
            const res = await fetch(`${baseUrl}rutinas/eliminar_plantilla`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const data = await res.json();
            if (data.ok) {
                location.reload();
            } else {
                alert("Error al eliminar");
            }
        } catch (e) {
            console.error(e);
            alert("Error de conexión");
        }
    }
</script>

<style>
    .font-teko {
        font-family: 'Teko', sans-serif;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .ls-2 {
        letter-spacing: 2px;
    }

    .tiny {
        font-size: 0.7rem;
    }

    .min-vh-50 {
        min-height: 50vh;
    }

    .shadow-primary {
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .group-hover-bg:hover {
        background-color: rgba(139, 92, 246, 0.05) !important;
    }

    .animate-scale-in {
        animation: scaleIn 0.3s cubic-bezier(0.12, 0, 0.39, 0);
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>