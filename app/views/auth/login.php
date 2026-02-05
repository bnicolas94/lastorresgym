<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Las Torres Gym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Teko:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4 animate-fade-in">
                    <h1 class="display-4 fw-bold text-white" style="font-family: 'Teko'">LAS TORRES <span
                            class="text-primary-gradient">GYM</span></h1>
                    <p class="text-muted">Bienvenido de nuevo, titán.</p>
                </div>

                <div class="card-custom animate-fade-in">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger border-0 bg-danger-subtle text-danger mb-3 py-2 small">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>auth/login" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small">DNI / Usuario</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-transparent border-end-0 border-secondary text-primary"><i
                                        class="fas fa-user"></i></span>
                                <input type="number" name="dni"
                                    class="form-control form-control-custom border-start-0 ps-0"
                                    placeholder="Ingresá tu DNI" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small">Contraseña</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-transparent border-end-0 border-secondary text-primary"><i
                                        class="fas fa-lock"></i></span>
                                <input type="password" name="password"
                                    class="form-control form-control-custom border-start-0 ps-0" placeholder="••••••••"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input bg-dark border-secondary" type="checkbox" name="remember"
                                    id="remember">
                                <label class="form-check-label text-muted small" for="remember">
                                    Recordar usuario
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-custom btn-lg">INGRESAR <i
                                    class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="#" class="text-muted small text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>