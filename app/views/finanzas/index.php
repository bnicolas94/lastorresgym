<div class="container pt-4 pb-5">

    <!-- Header -->
    <div class="mb-4 d-flex justify-content-between align-items-center animate-fade-in">
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Administración</h6>
            <h2 class="text-white fw-bold font-teko mb-0">PANEL FINANCIERO</h2>
        </div>
        <div>
            <button class="btn btn-primary font-teko shadow-primary-glow" data-bs-toggle="modal"
                data-bs-target="#modalNuevoPago">
                <i class="fas fa-plus me-2"></i> REGISTRAR PAGO
            </button>
        </div>
    </div>

    <!-- NAVEGACIÓN TABS -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= empty($fecha_filtro) ? 'active' : '' ?> font-teko ls-1" id="pills-resumen-tab"
                data-bs-toggle="pill" data-bs-target="#pills-resumen" type="button" role="tab">RESUMEN</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= !empty($fecha_filtro) ? 'active' : '' ?> font-teko ls-1"
                id="pills-movimientos-tab" data-bs-toggle="pill" data-bs-target="#pills-movimientos" type="button"
                role="tab">MOVIMIENTOS</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link font-teko ls-1" id="pills-arqueo-tab" data-bs-toggle="pill"
                data-bs-target="#pills-arqueo" type="button" role="tab">ARQUEO DE CAJA</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <!-- TAP 1: RESUMEN (Existing Dashboard) -->
        <div class="tab-pane fade <?= empty($fecha_filtro) ? 'show active' : '' ?>" id="pills-resumen" role="tabpanel">
            <div class="row g-4 animate-fade-up">

                <!-- Ingresos Hoy (Shift) -->
                <div class="col-12 col-md-4">
                    <div class="card-custom p-4 bg-surface-dark border-start border-4 border-success h-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase text-muted ls-1">Ingresos (Turno Actual)</small>
                                <h2 class="font-teko text-white mb-0 display-5 mt-2">$
                                    <?= number_format($ingresos_efectivo_hoy ?? 0, 0, ',', '.') ?>
                                </h2>
                            </div>
                            <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success">
                                <i class="fas fa-arrow-up fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-success">
                            <?php if (!empty($ultimo_arqueo)): ?>
                                <i class="fas fa-history me-1"></i> Desde: <?= date('d/m H:i', strtotime($ultimo_arqueo)) ?>
                            <?php else: ?>
                                <i class="fas fa-calendar-day me-1"></i> Desde inicio de día
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Efectivo en Caja -->
                <div class="col-12 col-md-4">
                    <div class="card-custom p-4 bg-surface-dark border-start border-4 border-warning h-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase text-muted ls-1">Efectivo en Caja</small>
                                <h2 class="font-teko text-white mb-0 display-5 mt-2">$
                                    <?= number_format($efectivo_caja, 0, ',', '.') ?>
                                </h2>
                            </div>
                            <div class="p-3 bg-warning bg-opacity-10 rounded-circle text-warning">
                                <i class="fas fa-wallet fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted d-flex justify-content-between align-items-center">
                            <span>Disponible físico</span>
                            <button class="btn btn-sm btn-outline-warning font-teko py-0" data-bs-toggle="modal"
                                data-bs-target="#modalRetiro">
                                <i class="fas fa-minus-circle me-1"></i> RETIRAR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Acumulado Mes -->
                <div class="col-12 col-md-4">
                    <div class="card-custom p-4 bg-surface-dark border-start border-4 border-info h-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase text-muted ls-1">Acumulado Mes</small>
                                <h2 class="font-teko text-white mb-0 display-5 mt-2">$
                                    <?= number_format($ingresos_mes, 0, ',', '.') ?>
                                </h2>
                            </div>
                            <div class="p-3 bg-info bg-opacity-10 rounded-circle text-info">
                                <i class="fas fa-calendar-alt fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">
                            Total bruto mensual (Todas las cajas)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mt-4 g-4 animate-fade-up" style="animation-delay: 0.1s;">
                <div class="col-12 col-md-8">
                    <div class="card-custom p-4 h-100">
                        <h5 class="font-teko text-uppercase text-white mb-4 ls-1">Métodos de Pago</h5>
                        <div style="height: 300px; position: relative;">
                            <canvas id="chartMetodos"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAP 2: MOVIMIENTOS (Unified Table + Filter) -->
        <div class="tab-pane fade <?= !empty($fecha_filtro) ? 'show active' : '' ?>" id="pills-movimientos"
            role="tabpanel">
            <div class="card-custom p-4 animate-fade-up">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-teko text-uppercase text-white mb-0 ls-1">Historial de Movimientos</h5>

                    <!-- Date Filter -->
                    <form action="<?= BASE_URL ?>finanzas" method="GET" class="d-flex align-items-center">
                        <label class="text-muted small me-2 font-teko text-uppercase" style="cursor: pointer;"
                            onclick="document.getElementById('fechaInput').showPicker()">Filtrar Fecha:</label>
                        <input type="date" id="fechaInput" name="fecha" value="<?= $fecha_filtro ?? '' ?>"
                            class="form-control-custom form-control-sm text-white border-secondary"
                            style="cursor: pointer;" onclick="try{this.showPicker()}catch(e){}"
                            onchange="this.form.submit()">

                        <?php if (!empty($fecha_filtro)): ?>
                            <a href="<?= BASE_URL ?>finanzas" class="btn btn-sm btn-outline-secondary ms-2"
                                title="Limpiar filtro">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="text-secondary text-uppercase font-teko ls-1">
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Usuario / Alumno</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimos_movimientos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay movimientos encontrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimos_movimientos as $mov): ?>
                                    <!-- Dynamic Row Style -->
                                    <tr
                                        class="<?= $mov['tipo'] == 'cierre' ? 'bg-warning bg-opacity-10 border-start border-warning border-4' : '' ?>">
                                        <td class="text-muted"><?= date('d/m H:i', strtotime($mov['fecha'])) ?></td>
                                        <td>
                                            <?php if ($mov['tipo'] == 'ingreso'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success font-teko ls-1">INGRESO</span>
                                            <?php elseif ($mov['tipo'] == 'retiro'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger font-teko ls-1">RETIRO</span>
                                            <?php elseif ($mov['tipo'] == 'cierre'): ?>
                                                <span class="badge bg-warning text-dark font-teko ls-1">CIERRE DE CAJA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-white">
                                            <?= $mov['concepto'] ?>
                                            <?php if (!empty($mov['destino'])): ?>
                                                <small class="d-block text-secondary">Destino: <?= $mov['destino'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small"><?= $mov['detalle_usuario'] ?></td>
                                        <td class="font-teko fs-5 
                                            <?= $mov['tipo'] == 'ingreso' ? 'text-success' :
                                                ($mov['tipo'] == 'retiro' ? 'text-danger' : 'text-warning') ?>">

                                            <?php
                                            $sign = ($mov['tipo'] == 'ingreso') ? '+' : (($mov['tipo'] == 'retiro') ? '-' : '=');
                                            ?>
                                            <?= $sign ?> $<?= number_format($mov['monto'], 0, ',', '.') ?>

                                            <?php if ($mov['tipo'] == 'cierre'): ?>
                                                <button class="btn btn-sm btn-outline-danger border-0 ms-2" title="Eliminar Cierre"
                                                    onclick="eliminarArqueo(<?= $mov['id'] ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAP 3: ARQUEO DE CAJA (Closing) -->
        <div class="tab-pane fade" id="pills-arqueo" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card-custom p-4 bg-surface border border-secondary border-opacity-25 animate-fade-up">
                        <h4 class="font-teko text-white mb-4 text-center ls-2">CIERRE DE CAJA DIARIO</h4>

                        <form id="formArqueo">
                            <div class="bg-dark rounded p-3 mb-4 border border-secondary border-opacity-10">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Ingresos (Sistema):</span>
                                    <span class="text-success font-teko fs-5">$
                                        <?= number_format($ingresos_efectivo_hoy, 0, ',', '.') ?></span>
                                    <input type="hidden" id="ingresos_sistema" name="ingresos_sistema"
                                        value="<?= $ingresos_efectivo_hoy ?>">
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Retiros (Sistema):</span>
                                    <span class="text-danger font-teko fs-5">- $
                                        <?= number_format($retiros_hoy, 0, ',', '.') ?></span>
                                    <input type="hidden" id="retiros_sistema" name="retiros_sistema"
                                        value="<?= $retiros_hoy ?>">
                                </div>
                                <hr class="border-secondary border-opacity-25 my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white fw-bold">SALDO ESPERADO:</span>
                                    <span class="text-warning font-teko fs-3">$
                                        <?= number_format($efectivo_caja, 0, ',', '.') ?></span>
                                    <input type="hidden" id="saldo_sistema" name="saldo_sistema"
                                        value="<?= $efectivo_caja ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-secondary font-teko text-uppercase ls-1">Efectivo Real
                                    (Contado)</label>
                                <div class="input-group input-group-lg">
                                    <span
                                        class="input-group-text bg-dark border-secondary border-opacity-50 text-secondary">$</span>
                                    <input type="number" id="efectivo_real" name="efectivo_real"
                                        class="form-control-custom fs-4 text-center text-white" required min="0"
                                        placeholder="0">
                                </div>
                                <div class="form-text text-end mt-2" id="diffText">Diferencia: $ 0</div>
                                <input type="hidden" id="diferencia" name="diferencia" value="0">
                            </div>

                            <div class="mb-4">
                                <label
                                    class="form-label text-secondary font-teko text-uppercase ls-1">Observaciones</label>
                                <textarea name="observaciones" class="form-control-custom" rows="2"
                                    placeholder="Ej: Faltan $100 cambio, o Sobran $50 propina..."></textarea>
                            </div>

                            <button type="submit"
                                class="btn btn-warning w-100 font-teko py-2 fs-5 text-dark fw-bold shadow-warning-glow">
                                <i class="fas fa-lock me-2"></i> CERRAR CAJA
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Conciliación Bancaria Column -->
                <div class="col-12 col-md-8 col-lg-6 mt-4 mt-lg-0">
                    <div class="card-custom p-4 bg-surface border border-info border-opacity-25 animate-fade-up"
                        style="animation-delay: 0.1s;">
                        <h4 class="font-teko text-info mb-4 text-center ls-2">CONCILIACIÓN BANCARIA (TRANSFERENCIAS)
                        </h4>

                        <div class="bg-dark rounded p-3 border border-secondary border-opacity-10 mb-3">
                            <h6 class="text-secondary text-uppercase ls-1 mb-3">Detalle por Cuenta Destino</h6>

                            <?php
                            $totalTransf = 0;
                            foreach ($transferencias_hoy as $destino => $monto):
                                $totalTransf += $monto;
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded"
                                    style="background-color: rgba(255,255,255,0.05);">
                                    <span class="text-white"><i class="fas fa-university me-2 text-info"></i>
                                        <?= ucfirst($destino) ?></span>
                                    <span class="font-teko fs-5 text-info">$
                                        <?= number_format($monto, 0, ',', '.') ?></span>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($transferencias_hoy)): ?>
                                <p class="text-muted small text-center my-3">No hay transferencias registradas hoy.</p>
                            <?php endif; ?>

                            <hr class="border-secondary border-opacity-25 my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white fw-bold">TOTAL TRANSFERENCIAS:</span>
                                <span class="font-teko fs-4 text-white">$
                                    <?= number_format($totalTransf, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="alert alert-info bg-opacity-10 border-0 text-info small">
                            <i class="fas fa-info-circle me-1"></i> Verificar estos montos con el Home Banking de cada
                            cuenta.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Pago -->
<div class="modal fade" id="modalNuevoPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-secondary border-opacity-50"
            style="background-color: var(--bg-surface); color: var(--text-main);">
            <div class="modal-header border-secondary border-opacity-10">
                <h5 class="modal-title font-teko text-uppercase ls-1 text-primary">Registrar Nuevo Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formNuevoPago">
                <div class="modal-body p-4">

                    <!-- Search User -->
                    <div class="mb-4">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Buscar Alumno
                            (DNI)</label>
                        <div class="input-group">
                            <input type="number" id="searchDni" class="form-control-custom" placeholder="Ingrese DNI">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <input type="hidden" name="alumno_id" id="alumnoId" required>

                        <!-- Result Card -->
                        <div id="studentResult"
                            class="d-none mt-3 p-3 bg-dark rounded border border-secondary border-opacity-25 animate-fade-in">
                            <div class="d-flex align-items-center">
                                <img src="" id="studentPhoto" class="rounded-circle me-3"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="text-white mb-0 font-teko ls-1" id="studentName">Nombe Apellido</h6>
                                    <small class="text-muted" id="studentStatus">Vencimiento: 01/01/2024</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Monto ($)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-secondary"
                                    style="padding: 0.75rem 1rem;">$</span>
                                <input type="number" name="monto" class="form-control-custom" required min="1"
                                    placeholder="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Método</label>
                            <select name="metodo_pago" id="selectMetodo"
                                class="form-select form-control-custom text-white"
                                style="appearance: none; background-image: none;">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Destination Select (Initially Hidden) -->
                    <div class="mt-3 d-none animate-fade-in" id="divDestino">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Cuenta Destino</label>
                        <select name="destino" class="form-select form-control-custom text-white"
                            style="appearance: none; background-image: none;">
                            <option value="" disabled selected>Seleccionar Destinatario...</option>
                            <option value="Fernando">Cuenta Fernando</option>
                            <option value="Matias">Cuenta Matías</option>
                        </select>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Concepto</label>
                        <input type="text" name="concepto" class="form-control-custom" required value="Cuota Mensual">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Observaciones</label>
                        <textarea name="observaciones" class="form-control-custom" placeholder="Opcional..."
                            rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-10 p-4">
                    <button type="button" class="btn btn-outline-secondary font-teko"
                        data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary font-teko px-4 shadow-primary-glow">GUARDAR
                        PAGO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Retiro de Caja -->
<div class="modal fade" id="modalRetiro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-secondary border-opacity-50"
            style="background-color: var(--bg-surface); color: var(--text-main);">
            <div class="modal-header border-secondary border-opacity-10">
                <h5 class="modal-title font-teko text-uppercase ls-1 text-danger">Registrar Retiro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formRetiro">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">El retiro se descontará automáticamente del "Efectivo en Caja" del
                        día y quedará asociado a tu usuario.</p>

                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Monto a Retirar
                            ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-50 text-secondary"
                                style="padding: 0.75rem 1rem;">$</span>
                            <input type="number" name="monto" class="form-control-custom" required min="1"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Concepto / Motivo</label>
                        <input type="text" name="concepto" class="form-control-custom" required
                            placeholder="Ej: Retiro dueño, Compra insumos...">
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-10 p-4">
                    <button type="button" class="btn btn-outline-secondary font-teko"
                        data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-danger font-teko px-4 shadow-danger-glow">CONFIRMAR
                        RETIRO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Methods
    const ctxMethods = document.getElementById('chartMetodos');
    if (ctxMethods) {
        new Chart(ctxMethods, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($metodos_pago_chart['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($metodos_pago_chart['data']) ?>,
                    backgroundColor: ['#10b981', '#0ea5e9', '#6366f1'],
                    borderColor: '#18181b', // Match bg-surface
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#9ca3af', font: { family: 'Teko', size: 14 } }
                    }
                }
            }
        });
    }

    // Toggle Destination Select
    const selMetodo = document.getElementById('selectMetodo');
    const divDestino = document.getElementById('divDestino');

    if (selMetodo && divDestino) {
        selMetodo.addEventListener('change', (e) => {
            if (e.target.value === 'transferencia') {
                divDestino.classList.remove('d-none');
                divDestino.querySelector('select').setAttribute('required', 'required');
            } else {
                divDestino.classList.add('d-none');
                divDestino.querySelector('select').removeAttribute('required');
                divDestino.querySelector('select').value = "";
            }
        });
    }

    // Search Student Logic
    const btnSearch = document.getElementById('btnSearch');
    const inputDni = document.getElementById('searchDni');

    // Allow Enter key
    inputDni.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnSearch.click();
        }
    });

    btnSearch.addEventListener('click', async () => {
        const dni = inputDni.value;
        if (!dni) return;

        try {
            const formData = new FormData();
            formData.append('dni', dni);

            const res = await fetch('<?= BASE_URL ?>finanzas/buscarAlumno', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.ok) {
                const al = data.alumno;
                document.getElementById('alumnoId').value = al.id;
                document.getElementById('studentName').innerText = al.nombre + ' ' + al.apellido;

                // Status Color
                const statusEl = document.getElementById('studentStatus');
                statusEl.innerHTML = `Vence: ${al.vence_fmt} <span class="badge ${al.estado == 'AL DÍA' ? 'bg-success' : 'bg-danger'} ms-2">${al.estado}</span>`;

                // Photo
                const photoUrl = al.foto ? '<?= BASE_URL ?>public/img/foto-perfil/' + al.foto : '<?= BASE_URL ?>public/img/default-user.png';
                document.getElementById('studentPhoto').src = photoUrl;

                document.getElementById('studentResult').classList.remove('d-none');
            } else {
                alert('Alumno no encontrado');
                document.getElementById('studentResult').classList.add('d-none');
                document.getElementById('alumnoId').value = '';
            }
        } catch (error) {
            console.error(error);
        }
    });

    // Handle Payment Form
    document.getElementById('formNuevoPago').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        if (!formData.get('alumno_id')) {
            alert('Por favor, buscá y seleccioná un alumno primero.');
            return;
        }

        try {
            const res = await fetch('<?= BASE_URL ?>finanzas/registrarPago', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.ok) {
                location.reload();
            } else {
                alert('Error al registrar pago');
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión');
        }
    });

    // Withdrawal Form
    document.getElementById('formRetiro').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!confirm('¿Estás seguro de registrar este retiro?')) return;

        const formData = new FormData(e.target);

        try {
            const res = await fetch('<?= BASE_URL ?>finanzas/registrarRetiro', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.ok) {
                location.reload();
            } else {
                alert('Error al registrar retiro: ' + (data.error || 'Desconocido'));
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión');
        }
    });

    // Arqueo Logic
    const inpReal = document.getElementById('efectivo_real');
    const inpSaldo = document.getElementById('saldo_sistema');
    const inpDiff = document.getElementById('diferencia');
    const txtDiff = document.getElementById('diffText');

    if (inpReal) {
        inpReal.addEventListener('input', () => {
            const real = parseFloat(inpReal.value) || 0;
            const system = parseFloat(inpSaldo.value) || 0;
            const diff = real - system;

            inpDiff.value = diff;
            txtDiff.innerText = `Diferencia: $ ${diff.toLocaleString('es-AR')}`;

            if (diff < 0) txtDiff.className = 'form-text text-end mt-2 text-danger fw-bold';
            else if (diff > 0) txtDiff.className = 'form-text text-end mt-2 text-success fw-bold';
            else txtDiff.className = 'form-text text-end mt-2 text-muted';
        });

        document.getElementById('formArqueo').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!confirm('¿Estás seguro de cerrar la caja? Esta acción guardará el arqueo.')) return;

            const formData = new FormData(e.target);
            try {
                const res = await fetch('<?= BASE_URL ?>finanzas/registrarArqueo', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.ok) {
                    alert('Caja cerrada correctamente.');
                    location.reload();
                } else {
                    alert('Error al cerrar caja: ' + (data.error || 'Desconocido'));
                }
            } catch (error) {
                console.error(error);
            }
        });
    }

    // Delete Arqueo Function
    async function eliminarArqueo(id) {
        if (!confirm('¡ATENCIÓN! \n\n¿Estás seguro de ELIMINAR este cierre de caja? \n\nEsta acción recalculará el efectivo en caja sumando todos los movimientos desde el cierre anterior.')) {
            return;
        }

        const formData = new FormData();
        formData.append('id', id);

        try {
            const res = await fetch('<?= BASE_URL ?>finanzas/eliminarArqueo', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.ok) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'No se pudo eliminar'));
            }
        } catch (e) {
            console.error(e);
            alert('Error de conexión');
        }
    }
</script>