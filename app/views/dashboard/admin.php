<div class="container pt-4 pb-5">

    <!-- Welcome Header -->
    <div class="mb-4 animate-fade-in d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Panel de Control</h6>
            <h2 class="text-white fw-bold font-teko mb-0">BIENVENIDO, <?= explode(' ', $usuario)[0] ?></h2>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                <i class="fas fa-shield-alt me-1"></i> ADMIN
            </span>
        </div>
    </div>

    <!-- 1. BLOQUE PRINCIPAL: ÚLTIMO ACCESO & ASISTENCIAS HOY -->
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-12 col-md-8">
            <div class="card-custom h-100 p-0 overflow-hidden relative">
                <div
                    class="p-3 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center bg-dark-subtle">
                    <span class="text-uppercase ls-1 text-muted small fw-bold"><i class="fas fa-history me-1"></i>
                        Último Acceso</span>
                    <span class="badge bg-danger rounded-pill px-2 animate-pulse">● EN VIVO</span>
                </div>

                <?php if ($ultimo_acceso): ?>
                    <div class="p-4 d-flex align-items-center">
                        <div class="rounded-circle p-1 border border-primary me-3 flex-shrink-0"
                            style="width: 80px; height: 80px;">
                            <img src="<?= !empty($ultimo_acceso['foto']) ? $ultimo_acceso['foto'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>"
                                class="w-100 h-100 rounded-circle object-fit-cover bg-dark"
                                onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
                        </div>
                        <div>
                            <h3 class="font-teko text-white mb-0 lh-1">
                                <?= $ultimo_acceso['nombre'] . ' ' . ($ultimo_acceso['apellido'] ?? '') ?>
                            </h3>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge bg-dark border border-secondary text-muted me-2">
                                    <i class="far fa-clock me-1"></i> <?= $ultimo_acceso['hora'] ?>
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fas fa-check me-1"></i> ACCESO PERMITIDO
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">No hay registros hoy.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card-custom h-100 p-4 text-center d-flex flex-column justify-content-center bg-gradient-dark">
                <small class="text-uppercase text-muted ls-2 mb-2">Socios de Hoy</small>
                <h1 class="display-1 font-teko fw-bold text-white mb-0"
                    style="text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);">
                    <?= $hoy ?>
                </h1>
                <p class="text-warning small mb-0"><i class="fas fa-bolt me-1"></i> Personas registradas</p>
            </div>
        </div>
    </div>

    <!-- 2. KPIs -->
    <div class="row g-3 mb-4 animate-fade-in" style="animation-delay: 0.1s">
        <div class="col-4">
            <div class="card-custom p-3">
                <small class="d-block text-muted text-uppercase ls-1" style="font-size: 0.7rem;">Activos</small>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <h2 class="font-teko text-white mb-0 lh-1" id="kpiActivos"><?= $activos ?></h2>
                    <i class="fas fa-users text-primary opacity-50 mb-1"></i>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-custom p-3">
                <small class="d-block text-muted text-uppercase ls-1" style="font-size: 0.7rem;">Nuevos (Mes)</small>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <h2 class="font-teko text-white mb-0 lh-1 text-info" id="kpiNuevos">+<?= $nuevos ?></h2>
                    <i class="fas fa-user-plus text-info opacity-50 mb-1"></i>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-custom p-3 bg-surface-highlight">
                <small class="d-block text-muted text-uppercase ls-1" style="font-size: 0.7rem;">En Gym Ahora</small>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <h2 class="font-teko text-danger mb-0 lh-1" id="kpiEnGym"><?= $en_gym ?></h2>
                    <i class="fas fa-running text-danger opacity-50 mb-1"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Anuncios Activos (Admin view) -->
    <?php if (!empty($anuncios)): ?>
        <div class="mb-5 animate-fade-in" style="animation-delay: 0.15s">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-bullhorn text-primary me-2"></i>
                <h5 class="text-white font-teko mb-0">ANUNCIOS VIGENTES</h5>
                <div class="flex-grow-1 h-px bg-secondary opacity-20 ms-3"></div>
            </div>

            <div class="d-flex gap-3 overflow-auto pb-3 no-scrollbar w-100" style="scroll-snap-type: x mandatory;">
                <?php foreach ($anuncios as $anuncio): ?>
                    <div class="flex-shrink-0" style="width: 250px; scroll-snap-align: start;">
                        <div
                            class="card bg-surface-dark border-secondary border-opacity-10 rounded-4 h-100 position-relative overflow-hidden group">
                            <div
                                class="position-absolute top-0 start-0 w-100 h-100 border-glow transition-all
                                <?= $anuncio['prioridad'] === 'alta' ? 'shadow-danger-glow opacity-30 border-danger' : ($anuncio['prioridad'] === 'media' ? 'shadow-warning-glow opacity-20 border-warning' : 'opacity-10') ?>">
                            </div>
                            <div class="card-body p-3 position-relative z-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span
                                        class="badge bg-dark border border-secondary border-opacity-25 <?= $anuncio['prioridad'] === 'alta' ? 'text-danger' : ($anuncio['prioridad'] === 'media' ? 'text-warning' : 'text-primary') ?> font-teko p-0 px-2 small">
                                        <?= strtoupper($anuncio['prioridad']) ?>
                                    </span>
                                </div>
                                <h6 class="text-white font-teko text-uppercase ls-1 mb-1">
                                    <?= htmlspecialchars($anuncio['titulo']) ?>
                                </h6>
                                <p class="text-muted mb-0"
                                    style="font-size: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($anuncio['contenido']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. GRÁFICOS (Chart.js) -->
    <div class="row g-3 mb-4 animate-fade-in" style="animation-delay: 0.2s">
        <!-- Flujo Semanal -->
        <div class="col-12 col-md-8">
            <div class="card-custom p-3 h-100">
                <h6 class="text-secondary font-teko mb-3 border-bottom border-dark pb-2">
                    <i class="fas fa-chart-area me-2 text-primary"></i> FLUJO DE ASISTENCIAS (7 DÍAS)
                </h6>
                <div style="height: 200px;">
                    <canvas id="chartFlujo"></canvas>
                </div>
            </div>
        </div>
        <!-- Donut Membresías -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-3 h-100">
                <h6 class="text-secondary font-teko mb-3 border-bottom border-dark pb-2">
                    <i class="fas fa-chart-pie me-2 text-primary"></i> MEMBRESÍAS
                </h6>
                <div style="height: 200px; position: relative;">
                    <canvas id="chartMembresias"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-2 small">
                    <span class="text-success"><i class="fas fa-circle me-1"></i> Al Día</span>
                    <span class="text-danger"><i class="fas fa-circle me-1"></i> Vencidos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3b. USO DE LA APP -->
    <div class="mb-4 animate-fade-in" style="animation-delay: 0.25s">
        <div class="card-custom">
            <div
                class="card-header border-secondary border-opacity-10 d-flex justify-content-between align-items-center bg-black bg-opacity-25">
                <div class="d-flex align-items-center">
                    <i class="fas fa-mobile-alt text-primary me-2"></i>
                    <h6 class="text-white font-teko mb-0 me-2" style="font-size: 1.2rem;">USO DE LA APP</h6>
                    <span class="badge bg-primary rounded-pill" id="badgeAppUsage">0</span>
                </div>
                <div>
                    <input type="date" id="dateAppUsage"
                        class="form-control form-control-sm bg-dark border-secondary text-white" style="width: 140px;"
                        value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-dark table-hover table-striped mb-0 align-middle small">
                        <thead class="bg-black text-secondary text-uppercase sticky-top">
                            <tr>
                                <th class="ps-4">Foto</th>
                                <th>Socio</th>
                                <th class="text-end pe-4">Acceso</th>
                            </tr>
                        </thead>
                        <tbody id="tableAppUsageBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. VENCIMIENTOS -->
    <div class="animate-fade-in" style="animation-delay: 0.3s">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
            <h5 class="text-white font-teko mb-0">PRÓXIMOS VENCIMIENTOS (Esta Semana)</h5>
        </div>

        <div class="card-custom p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark table-borderless table-striped text-white mb-0 align-middle small">
                    <thead class="bg-black text-uppercase text-secondary">
                        <tr>
                            <th class="ps-3">Socio</th>
                            <th>Teléfono</th>
                            <th>Vence</th>
                            <th class="text-end pe-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vencimientos)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No hay vencimientos próximos.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vencimientos as $v): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-white-50">
                                        <?= strtoupper($v['nombre'] . ' ' . $v['apellido']) ?>
                                    </td>
                                    <td><?= $v['telefono'] ?></td>
                                    <td class="text-warning fw-bold"><?= date('d/m/Y', strtotime($v['vence'])) ?></td>
                                    <td class="text-end pe-3">
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $v['telefono']) ?>"
                                            target="_blank" class="btn btn-sm btn-outline-success border-0 py-0">
                                            <i class="fab fa-whatsapp"></i> Avisar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data Flujo
    const flujoData = <?= json_encode($flujo) ?>;
    const labelsFlujo = flujoData.map(d => {
        // "2023-01-30" split -> [2023, 01, 30]
        const parts = d.fecha.split('-');
        // parts[2] = day, parts[1] = month
        return `${parts[2]}/${parts[1]}`;
    });
    const dataFlujo = flujoData.map(d => d.cantidad);

    // Chart Flujo
    new Chart(document.getElementById('chartFlujo'), {
        type: 'line',
        data: {
            labels: labelsFlujo,
            datasets: [{
                label: 'Asistencias',
                data: dataFlujo,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#333' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Chart Membresias
    const mem = <?= json_encode($membresias) ?>;
    new Chart(document.getElementById('chartMembresias'), {
        type: 'doughnut',
        data: {
            labels: ['Al Día', 'Vencidos'],
            datasets: [{
                data: [mem.al_dia, mem.vencidos],
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>

<script>
    // Real-Time Updates
    function updateLiveMetrics() {
        fetch('<?= BASE_URL ?>dashboard/api_live_metrics')
            .then(response => response.json())
            .then(data => {
                // Update Counters
                document.querySelector('.display-1.font-teko').innerText = data.hoy;

                // Update KPIs
                const act = document.getElementById('kpiActivos');
                const eng = document.getElementById('kpiEnGym');
                if (act) act.innerText = data.activos;
                if (eng) eng.innerText = data.en_gym;

                // Update Ultimo Acceso
                const container = document.querySelector('.card-custom .p-4.d-flex');
                if (data.ultimo_acceso && container) {
                    const img = container.querySelector('img');
                    const name = container.querySelector('h3');
                    const time = container.querySelector('.fa-clock').parentNode;

                    if (img) {
                        img.src = data.ultimo_acceso.foto || 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                        // Add JS-side error handler dynamically just in case
                        img.onerror = function () { this.src = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; };
                    }
                    if (name) {
                        const n = data.ultimo_acceso.nombre || '';
                        const a = data.ultimo_acceso.apellido || '';
                        name.innerText = (n + ' ' + a).trim();
                    }

                    if (data.ultimo_acceso.fecha_acceso) {
                        const date = new Date(data.ultimo_acceso.fecha_acceso);
                        // Adjust for timezone if needed, usually comes as UTC or Server Time.
                        // Assuming server time matches local for now.
                        const hours = date.getHours().toString().padStart(2, '0');
                        const minutes = date.getMinutes().toString().padStart(2, '0');
                        if (time) time.innerHTML = `<i class="far fa-clock me-1"></i> ${hours}:${minutes}`;
                    }
                }
            })
            .catch(err => console.error('Error fetching live data:', err));
    }

    // Poll every 5 seconds
    setInterval(updateLiveMetrics, 5000);

    // App Usage Logic
    const dateInput = document.getElementById('dateAppUsage');
    dateInput.addEventListener('change', loadAppUsage);

    function loadAppUsage() {
        const date = dateInput.value;
        const tbody = document.getElementById('tableAppUsageBody');
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>';

        fetch('<?= BASE_URL ?>dashboard/api_app_usage?fecha=' + date)
            .then(res => res.json())
            .then(data => {
                document.getElementById('badgeAppUsage').innerText = data.count;

                if (data.count === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Sin actividad registrada.</td></tr>';
                    return;
                }

                let html = '';
                data.list.forEach(item => {
                    let avatar = item.foto || 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                    // Fix URL relativa
                    if (avatar.indexOf('http') !== 0 && avatar.indexOf('data:') !== 0) {
                        avatar = '<?= BASE_URL ?>img/foto-perfil/' + avatar;
                    }

                    html += `
                        <tr>
                            <td class="ps-4" style="width: 60px;">
                                <div class="rounded-circle overflow-hidden border border-secondary" style="width: 36px; height: 36px;">
                                    <img src="${avatar}" class="w-100 h-100 object-fit-cover bg-black" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
                                </div>
                            </td>
                            <td class="fw-bold text-white text-uppercase">${item.nombre} ${item.apellido}</td>
                            <td class="text-end pe-4 text-primary fw-bold">${item.hora_fmt}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3">Error al cargar datos.</td></tr>';
            });
    }

    // Initial Load
    loadAppUsage();
</script>