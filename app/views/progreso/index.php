<div class="container pt-4 pb-5">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4 animate-fade-in">
        <a href="<?= BASE_URL ?>dashboard" class="btn btn-icon btn-outline-secondary border-0 me-3">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Tus Métricas</h6>
            <h2 class="text-white fw-bold font-teko mb-0">PROGRESO DE FUERZA</h2>
        </div>
    </div>

    <!-- Selector de Ejercicios -->
    <div class="mb-4 animate-fade-in">
        <label class="text-muted small mb-2 d-block">Seleccioná un ejercicio para ver tu evolución:</label>
        <select class="form-select form-control-custom" id="selectEjercicio" onchange="cargarGrafico()">
            <option value="" disabled selected>Elegí un ejercicio...</option>
            <?php foreach ($ejercicios as $categoria => $lista): ?>
                <optgroup label="<?= $categoria ?>">
                    <?php foreach ($lista as $ej): ?>
                        <option value="<?= $ej['id'] ?>">
                            <?= $ej['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Card Grafico -->
    <div class="card-custom mb-3 p-3 animate-fade-in" style="min-height: 350px; position:relative;">
        <!-- Loading Spinner -->
        <div id="loadingChart" class="position-absolute top-50 start-50 translate-middle text-center d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted small mt-2">Cargando datos...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyChart" class="position-absolute top-50 start-50 translate-middle text-center text-muted">
            <i class="fas fa-chart-area fa-3x mb-3 opacity-25"></i>
            <p class="small">Seleccioná un ejercicio arriba</p>
        </div>

        <canvas id="progresoChart"></canvas>
    </div>

    <!-- Stats Resumen -->
    <div id="statsContainer" class="row g-3 d-none animate-fade-in">
        <div class="col-6">
            <div class="card-custom bg-surface p-3 text-center">
                <small class="text-muted text-uppercase ls-1" style="font-size: 0.7rem;">Máximo (RM)</small>
                <h3 class="text-white font-teko mb-0 mt-1 text-success" id="statMax">-- <span
                        class="fs-6 text-muted">kg</span></h3>
            </div>
        </div>
        <div class="col-6">
            <div class="card-custom bg-surface p-3 text-center">
                <small class="text-muted text-uppercase ls-1" style="font-size: 0.7rem;">Último Registro</small>
                <h3 class="text-white font-teko mb-0 mt-1" id="statUltimo">-- <span class="fs-6 text-muted">kg</span>
                </h3>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let myChart = null;

    async function cargarGrafico() {
        const selector = document.getElementById('selectEjercicio');
        const ejercicioId = selector.value;
        const loading = document.getElementById('loadingChart');
        const empty = document.getElementById('emptyChart');
        const statsDiv = document.getElementById('statsContainer');

        if (!ejercicioId) return;

        // UI States
        if (myChart) myChart.destroy();
        loading.classList.remove('d-none');
        empty.classList.add('d-none');
        statsDiv.classList.add('d-none');

        try {
            const response = await fetch(`<?= BASE_URL ?>progreso/data?id=${ejercicioId}`);
            const data = await response.json();

            loading.classList.add('d-none');

            if (data.length === 0) {
                empty.innerHTML = '<i class="fas fa-search fa-3x mb-3 opacity-25"></i><p>No hay datos suficientes aún.</p>';
                empty.classList.remove('d-none');
                return;
            }

            // Prepare Data
            const labels = data.map(d => d.fecha);
            const dataPoints = data.map(d => d.rm); // Usamos RM Estimado

            // Update Stats
            const maxVal = Math.max(...dataPoints);
            const lastVal = dataPoints[dataPoints.length - 1];
            document.getElementById('statMax').innerHTML = `${maxVal} <span class="fs-6 text-muted">kg</span>`;
            document.getElementById('statUltimo').innerHTML = `${lastVal} <span class="fs-6 text-muted">kg</span>`;
            statsDiv.classList.remove('d-none');

            // Render Chart
            const ctx = document.getElementById('progresoChart').getContext('2d');

            // Gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)'); // Primary color fade
            gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '1RM Estimado (kg)',
                        data: dataPoints,
                        borderColor: '#8b5cf6', // Primary
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#06b6d4', // Secondary
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#18181b',
                            titleColor: '#fff',
                            bodyColor: '#ccc',
                            borderColor: '#27272a',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });

        } catch (e) {
            console.error(e);
            loading.classList.add('d-none');
            alert("Error al cargar datos");
        }
    }
</script>