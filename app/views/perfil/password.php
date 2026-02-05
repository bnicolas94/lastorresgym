<div class="container pt-4 pb-5">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4 animate-fade-in">
        <a href="<?= BASE_URL ?>perfil" class="btn btn-icon btn-outline-secondary border-0 me-3">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div>
            <h6 class="text-secondary text-uppercase ls-2 mb-0">Seguridad</h6>
            <h2 class="text-white fw-bold font-teko mb-0">CAMBIAR CONTRASEÑA</h2>
        </div>
    </div>

    <!-- Errores -->
    <?php if (isset($errores) && !empty($errores)): ?>
        <div class="alert alert-danger animate-fade-in mb-4 border-0 bg-danger-subtle text-danger">
            <ul class="mb-0 ps-3">
                <?php foreach ($errores as $error): ?>
                    <li>
                        <?= $error ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>perfil/guardar_password" method="POST" class="animate-fade-in">

        <div class="card-custom p-4">

            <div class="mb-4">
                <label class="form-label text-secondary small text-uppercase ls-1">Contraseña Actual</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-lock"></i></span>
                    <input type="password" name="password_actual"
                        class="form-control bg-dark border-secondary text-white focus-primary" placeholder="••••••">
                </div>
            </div>

            <hr class="border-secondary opacity-25 my-4">

            <div class="mb-3">
                <label class="form-label text-secondary small text-uppercase ls-1">Nueva Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-key"></i></span>
                    <input type="password" name="password_nueva"
                        class="form-control bg-dark border-secondary text-white focus-primary"
                        placeholder="Mínimo 6 caracteres">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary small text-uppercase ls-1">Confirmar Nueva</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i
                            class="fas fa-check-double"></i></span>
                    <input type="password" name="password_confirmar"
                        class="form-control bg-dark border-secondary text-white focus-primary"
                        placeholder="Repetir nueva contraseña">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold ls-1 text-uppercase">
                Actualizar Contraseña
            </button>
        </div>

    </form>
</div>

<style>
    .focus-primary:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
    }
</style>