<div class="container pt-4 pb-5">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4 animate-fade-in">
        <a href="<?= BASE_URL ?>dashboard" class="btn btn-icon btn-outline-secondary border-0 me-3">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Mi Cuenta</h6>
            <h2 class="text-white fw-bold font-teko mb-0">PERFIL</h2>
        </div>
    </div>

    <!-- Feedback mensaje -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'saved'): ?>
        <div class="alert alert-success animate-fade-in mb-4 border-0 bg-success-subtle text-success">
            <i class="fas fa-check-circle me-2"></i> Cambios guardados correctamente.
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>perfil/guardar" method="POST" class="animate-fade-in" enctype="multipart/form-data">

        <!-- Foto y Nombre -->
        <div class="card-custom mb-4 p-4 text-center position-relative">
            <div class="position-relative d-inline-block mb-3">
                <div class="rounded-circle overflow-hidden border border-2 border-primary p-1"
                    style="width: 100px; height: 100px;">
                    <!-- Lógica de visualización: Si empieza con http es URL completa, sino concatenar ruta local -->
                    <?php
                    $fotoUrl = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                    if (!empty($usuario['foto'])) {
                        if (strpos($usuario['foto'], 'http') === 0) {
                            $fotoUrl = $usuario['foto'];
                        } else {
                            $fotoUrl = BASE_URL . 'img/foto-perfil/' . $usuario['foto'];
                        }
                    }
                    ?>
                    <img src="<?= htmlspecialchars($fotoUrl) ?>" id="previewFoto"
                        class="w-100 h-100 rounded-circle object-fit-cover bg-dark"
                        onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
                </div>
                <!-- Input File Oculto -->
                <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*">

                <button type="button"
                    class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 mb-1 me-1"
                    style="width: 32px; height: 32px;" onclick="document.getElementById('inputFoto').click()">
                    <i class="fas fa-camera small"></i>
                </button>
            </div>

            <h3 class="text-white font-teko mb-0">
                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
            </h3>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 mt-2">DNI:
                <?= $usuario['dni'] ?>
            </span>

            <?php if (isset($usuario['vence'])): ?>
                <div class="mt-3 text-muted small">
                    <!-- Vencimiento -->
                    Vencimiento: <span
                        class="<?= strtotime($usuario['vence']) < time() ? 'text-danger' : 'text-success' ?>">
                        <?= date('d/m/Y', strtotime($usuario['vence'])) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Datos Editables -->
        <div class="card-custom p-4">
            <h5 class="text-white font-teko mb-3 border-bottom border-secondary border-opacity-25 pb-2">DATOS DE
                CONTACTO</h5>

            <div class="mb-3">
                <label class="form-label text-secondary small text-uppercase ls-1">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-envelope"></i></span>
                    <input type="email" name="email"
                        class="form-control bg-dark border-secondary text-white focus-primary"
                        value="<?= htmlspecialchars($usuario['email']) ?>" placeholder="tu@email.com">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary small text-uppercase ls-1">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-phone"></i></span>
                    <input type="tel" name="telefono"
                        class="form-control bg-dark border-secondary text-white focus-primary"
                        value="<?= htmlspecialchars($usuario['telefono']) ?>" placeholder="11...">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary small text-uppercase ls-1">Dirección</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="direccion"
                        class="form-control bg-dark border-secondary text-white focus-primary"
                        value="<?= htmlspecialchars($usuario['direccion']) ?>" placeholder="Calle 123">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold ls-1 text-uppercase">
                Guardar Cambios
            </button>
        </div>

    </form>

    <!-- Seguridad Link -->
    <div class="mt-4 text-center animate-fade-in">
        <a href="<?= BASE_URL ?>perfil/password"
            class="text-muted text-decoration-none small hover-text-white transition-all">
            <i class="fas fa-lock me-1"></i> Cambiar Contraseña
        </a>
    </div>

    <!-- Cerrar Sesión (Mobile) -->
    <div class="mt-5 mb-3 animate-fade-in">
        <a href="<?= BASE_URL ?>auth/logout"
            class="btn btn-outline-danger w-100 py-3 fw-bold ls-1 text-uppercase rounded-xl d-flex align-items-center justify-content-center">
            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
        </a>
    </div>

</div>

<style>
    .focus-primary:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
    }

    .hover-text-white:hover {
        color: #fff !important;
    }
</style>

<script>
    // Preview de imagen seleccionada
    const inputFoto = document.getElementById('inputFoto');
    const previewFoto = document.getElementById('previewFoto');

    if (inputFoto && previewFoto) {
        inputFoto.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewFoto.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>