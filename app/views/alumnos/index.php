<div class="container pt-4 pb-5">

    <!-- Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 animate-fade-in">
        <div class="mb-3 mb-md-0">
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Administración</h6>
            <h2 class="text-white fw-bold font-teko mb-0">LISTADO MAESTRO DE SOCIOS</h2>
        </div>
        <div>
            <button class="btn btn-primary fw-bold text-uppercase ls-1 shadow-primary" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseAgregar" aria-expanded="false">
                <i class="fas fa-plus me-2"></i> Nuevo Socio
            </button>
        </div>
    </div>

    <!-- Feedback Mensajes -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'created'): ?>
        <div class="alert alert-success animate-fade-in mb-4 border-0 bg-success-subtle text-success">
            <i class="fas fa-check-circle me-2"></i> Socio registrado exitosamente (Pass: 1234).
        </div>
    <?php endif; ?>

    <!-- Formulario Alta Rápida (Collapse) -->
    <div class="collapse mb-4 animate-fade-in" id="collapseAgregar">
        <div class="card-custom p-4 border border-primary border-opacity-25">
            <h5 class="text-white font-teko mb-3"><i class="fas fa-user-plus me-2 text-primary"></i> INGRESAR SOCIO
                (EXPRESS)</h5>
            <form action="<?= BASE_URL ?>alumnos/guardar" method="POST">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label text-secondary small text-uppercase">DNI (Solo números)</label>
                        <input type="number" name="dni"
                            class="form-control bg-dark border-secondary text-white focus-primary" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-secondary small text-uppercase">Nombre</label>
                        <input type="text" name="nombre"
                            class="form-control bg-dark border-secondary text-white focus-primary" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-secondary small text-uppercase">Apellido</label>
                        <input type="text" name="apellido"
                            class="form-control bg-dark border-secondary text-white focus-primary" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-secondary small text-uppercase">Teléfono (Opcional)</label>
                        <input type="tel" name="telefono"
                            class="form-control bg-dark border-secondary text-white focus-primary"
                            placeholder="Ej: 1122334455">
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseAgregar">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Guardar Socio</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Buscador & Filtros -->
    <div class="card-custom p-3 mb-4 animate-fade-in">
        <form action="<?= BASE_URL ?>alumnos" method="GET"
            class="d-flex flex-wrap gap-2 justify-content-between align-items-center">

            <!-- Hidden inputs to persist other params -->
            <input type="hidden" name="sort" value="<?= $filters['sort'] ?>">
            <input type="hidden" name="dir" value="<?= $filters['dir'] ?>">

            <div class="d-flex gap-2 flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-dark border-secondary text-white focus-primary"
                        placeholder="Buscar por Nombre, Apellido o DNI..." value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
                <?php if (!empty($busqueda)): ?>
                    <a href="<?= BASE_URL ?>alumnos" class="btn btn-outline-secondary" title="Limpiar"><i
                            class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>

            <!-- Toggle Filter -->
            <div class="form-check form-switch ps-5">
                <input class="form-check-input" type="checkbox" role="switch" id="switchShowAll" name="show_all"
                    value="1" onchange="this.form.submit()" <?= $filters['show_all'] ? 'checked' : '' ?>>
                <label class="form-check-label text-white small text-uppercase ls-1 ms-1" for="switchShowAll">
                    <?= $filters['show_all'] ? 'Mostrar solo activos' : 'Mostrar todos' ?>
                </label>
            </div>
        </form>
        <div class="mt-2 text-end">
            <small class="text-muted">Total: <span class="text-white fw-bold"><?= $total ?></span> socios
                encontrados</small>
        </div>
    </div>

    <!-- Tabla Resultante -->
    <div class="card-custom p-0 overflow-hidden animate-fade-in">
        <div class="table-responsive">
            <table class="table table-dark table-hover table-borderless table-striped text-white mb-0 align-middle">
                <thead class="bg-black text-uppercase text-secondary small ls-1">
                    <tr>
                        <?php
                        // Helper para links de sorting
                        function sortLink($label, $col, $currentSort, $currentDir, $baseUrl, $params)
                        {
                            $newDir = ($currentSort == $col && $currentDir == 'ASC') ? 'DESC' : 'ASC';
                            $icon = '';
                            if ($currentSort == $col) {
                                $icon = ($currentDir == 'ASC') ? '<i class="fas fa-sort-up ms-1 text-primary"></i>' : '<i class="fas fa-sort-down ms-1 text-primary"></i>';
                            } else {
                                $icon = '<i class="fas fa-sort ms-1 opacity-25"></i>';
                            }

                            // Rebuild query params
                            $q = array_merge($params, ['sort' => $col, 'dir' => $newDir]);
                            $url = $baseUrl . 'alumnos?' . http_build_query($q);

                            return "<a href='$url' class='text-decoration-none text-secondary d-inline-flex align-items-center'>$label $icon</a>";
                        }

                        $baseParams = ['q' => $busqueda, 'show_all' => $filters['show_all'] ? '1' : '0'];
                        ?>
                        <th class="ps-4">Foto</th>
                        <th><?= sortLink('Socio', 'apellido', $filters['sort'], $filters['dir'], BASE_URL, $baseParams) ?>
                        </th>
                        <th class="d-none d-md-table-cell">Contacto</th>
                        <th><?= sortLink('Vencimiento', 'vence', $filters['sort'], $filters['dir'], BASE_URL, $baseParams) ?>
                        </th>
                        <th class="text-center">
                            <?= sortLink('Gym', 'gym_visitas', $filters['sort'], $filters['dir'], BASE_URL, $baseParams) ?>
                        </th>
                        <th class="text-center">
                            <?= sortLink('Uso App', 'app_uso', $filters['sort'], $filters['dir'], BASE_URL, $baseParams) ?>
                        </th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alumnos) > 0): ?>
                        <?php foreach ($alumnos as $al): ?>
                            <tr>
                                <td class="ps-4" style="width: 60px;">
                                    <div class="rounded-circle overflow-hidden border border-secondary"
                                        style="width: 40px; height: 40px;">
                                        <?php
                                        $fotoUrl = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                                        if (!empty($al['foto'])) {
                                            if (strpos($al['foto'], 'http') === 0) {
                                                $fotoUrl = $al['foto'];
                                            } else {
                                                $fotoUrl = BASE_URL . 'img/foto-perfil/' . $al['foto'];
                                            }
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($fotoUrl) ?>"
                                            class="w-100 h-100 object-fit-cover bg-black"
                                            onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-white">
                                        <?= htmlspecialchars($al['apellido'] . ', ' . $al['nombre']) ?>
                                    </div>
                                    <small class="text-muted">DNI: <?= $al['dni'] ?></small>
                                </td>
                                <td class="d-none d-md-table-cell text-muted small">
                                    <?php if ($al['telefono']): ?>
                                        <div><i class="fab fa-whatsapp text-success me-1"></i> <?= $al['telefono'] ?></div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $vence = strtotime($al['vence']);
                                    $vencido = $vence < time();
                                    $color = $vencido ? 'text-danger' : 'text-success';
                                    $icono = $vencido ? 'fa-exclamation-circle' : 'fa-check-circle';
                                    ?>
                                    <div class="<?= $color ?> fw-bold small">
                                        <i class="fas <?= $icono ?> me-1"></i> <?= date('d/m/Y', $vence) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                        <?= $al['gym_visitas'] ?> visitas
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span
                                            class="badge bg-<?= $al['app_status_color'] ?> bg-opacity-25 text-<?= $al['app_status_color'] ?> border border-<?= $al['app_status_color'] ?> border-opacity-25 mb-1"
                                            style="min-width: 60px;">
                                            <?= $al['app_status_label'] ?>
                                        </span>
                                        <small class="text-muted" style="font-size: 0.65rem;">
                                            Total: <?= $al['app_uso'] ?> | Últ: <?= $al['app_ultimo_fmt'] ?>
                                        </small>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-outline-secondary border-0" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul
                                            class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg border-secondary">
                                            <li><a class="dropdown-item small" href="#"><i class="fas fa-edit me-2"></i>
                                                    Editar</a></li>
                                            <li><a class="dropdown-item small"
                                                    href="<?= BASE_URL ?>asignacion/index/<?= $al['id'] ?>"><i
                                                        class="fas fa-plus-circle me-2"></i> Asignar Rutina</a></li>
                                            <li><a class="dropdown-item small"
                                                    href="<?= BASE_URL ?>rutinas/ver_cliente/<?= $al['id'] ?>"><i
                                                        class="fas fa-dumbbell me-2"></i> Ver Rutinas</a></li>
                                            <li>
                                                <hr class="dropdown-divider border-secondary opacity-25">
                                            </li>
                                            <li><a class="dropdown-item small text-danger" href="#"><i
                                                        class="fas fa-ban me-2"></i> Suspender</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No se encontraron socios con ese criterio.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación Simple -->
        <?php if ($paginas > 1): ?>
            <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        // Rebuild pagination params
                        $pageParams = $baseParams;
                        $pageParams['sort'] = $filters['sort'];
                        $pageParams['dir'] = $filters['dir'];
                        ?>
                        <li class="page-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link bg-dark border-secondary text-white"
                                href="<?= BASE_URL ?>alumnos?page=<?= $pagina_actual - 1 ?>&<?= http_build_query($pageParams) ?>">Ant</a>
                        </li>
                        <?php for ($i = 1; $i <= $paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                <a class="page-link <?= $i == $pagina_actual ? 'bg-primary border-primary' : 'bg-dark border-secondary text-white' ?>"
                                    href="<?= BASE_URL ?>alumnos?page=<?= $i ?>&<?= http_build_query($pageParams) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $pagina_actual >= $paginas ? 'disabled' : '' ?>">
                            <a class="page-link bg-dark border-secondary text-white"
                                href="<?= BASE_URL ?>alumnos?page=<?= $pagina_actual + 1 ?>&<?= http_build_query($pageParams) ?>">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

</div>

<style>
    /* Adjust collapse transition */
    .collapse {
        transition: all 0.3s ease;
    }

    .pagination .page-link:focus {
        box-shadow: none;
    }
</style>