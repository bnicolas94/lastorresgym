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

    <!-- 1. KPIs -->
    <div class="row g-3 mb-4 animate-fade-in">
        <!-- Ingresos Hoy -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-4 bg-surface-dark border-start border-4 border-success h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-uppercase text-muted ls-1">Ingresos Hoy</small>
                        <h2 class="font-teko text-white mb-0 display-5 mt-2">$
                            <?= number_format($ingresos_hoy, 0, ',', '.') ?>
                        </h2>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success">
                        <i class="fas fa-dollar-sign fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i> Arqueo</span> listo para
                    cierre
                </div>
            </div>
        </div>

        <!-- Efectivo en Caja -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-4 bg-surface-dark border-start border-4 border-warning h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-uppercase text-muted ls-1">Efectivo en Caja (Hoy)</small>
                        <h2 class="font-teko text-white mb-0 display-5 mt-2">$
                            <?= number_format($efectivo_caja, 0, ',', '.') ?>
                        </h2>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-circle text-warning">
                        <i class="fas fa-wallet fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Dinero físico a rendir
                </div>
            </div>
        </div>

        <!-- Ingresos Mes -->
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
                        <i class="fas fa-chart-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Charts & Tables -->
    <div class="row g-3">
        <!-- Chart Distribution -->
        <div class="col-12 col-lg-4">
            <div class="card-custom p-4 h-100">
                <h5 class="text-white font-teko mb-4">MÉTODOS DE PAGO</h5>
                <div style="height: 250px; position: relative;">
                    <canvas id="chartMetodos"></canvas>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="col-12 col-lg-8">
            <div class="card-custom h-100 p-0 overflow-hidden">
                <div
                    class="p-3 border-bottom border-secondary border-opacity-10 bg-black bg-opacity-20 d-flex justify-content-between align-items-center">
                    <h5 class="text-white font-teko mb-0">ÚLTIMOS MOVIMIENTOS</h5>
                    <button class="btn btn-sm btn-outline-secondary font-teko">VER TODO</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0 text-white small">
                        <thead class="bg-black text-secondary text-uppercase">
                            <tr>
                                <th class="ps-4">Socio</th>
                                <th>Concepto</th>
                                <th>Método</th>
                                <th>Fecha</th>
                                <th class="text-end pe-4">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimos_movimientos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No hay movimientos registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimos_movimientos as $mov): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle overflow-hidden me-2 border border-secondary"
                                                    style="width: 32px; height: 32px;">
                                                    <img src="<?= $mov['foto'] ? $mov['foto'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>"
                                                        class="w-100 h-100 object-fit-cover">
                                                </div>
                                                <span class="fw-bold">
                                                    <?= $mov['nombre'] . ' ' . $mov['apellido'] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-muted">
                                            <?= $mov['concepto'] ?>
                                        </td>
                                        <td>
                                            <?php if ($mov['metodo_pago'] === 'efectivo'): ?>
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">EFECTIVO</span>
                                            <?php elseif ($mov['metodo_pago'] === 'transferencia'): ?>
                                                <span
                                                    class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">TRANSF</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">MP</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted">
                                            <?= date('d/m H:i', strtotime($mov['fecha_pago'])) ?>
                                        </td>
                                        <td class="text-end pe-4 fw-bold font-monospace fs-6">$
                                            <?= number_format($mov['monto'], 0, ',', '.') ?>
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

</div>

<!-- Modal Nuevo Pago -->
<div class="modal fade" id="modalNuevoPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface border-secondary border-opacity-50">
            <div class="modal-header border-secondary border-opacity-10">
                <h5 class="modal-title font-teko text-uppercase ls-1 text-white">Registrar Ingreso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formNuevoPago">
                <div class="modal-body p-4">
                    <!-- Search Student (Simplified for logic, ideally an autocomplete) -->
                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">ID Alumno (Temp)</label>
                        <input type="number" name="alumno_id" class="form-control-custom" required
                            placeholder="ID del alumno">
                        <small class="text-muted">En futura versión: Buscador por nombre</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Monto ($)</label>
                            <input type="number" name="monto" class="form-control-custom" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Método</label>
                            <select name="metodo_pago" class="form-select-custom w-100 text-white bg-dark">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Concepto</label>
                        <input type="text" name="concepto" class="form-control-custom" required value="Cuota Mensual">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Observaciones</label>
                        <textarea name="observaciones" class="form-control-custom" rows="2"></textarea>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Methods
    const ctx = document.getElementById('chartMetodos');
    if (ctx) {
        const dataMetodos = <?= json_encode($metodos_pago_chart) ?>;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: dataMetodos.labels,
                datasets: [{
                    data: dataMetodos.data,
                    backgroundColor: ['#10b981', '#06b6d4', '#8b5cf6'], // Green, Cyan, Violet
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#9ca3af', font: { family: 'Outfit' } } }
                }
            }
        });
    }

    // Submit Form
    document.getElementById('formNuevoPago').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

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
</script>