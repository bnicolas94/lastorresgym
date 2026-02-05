<div class="container pt-4">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-fade-in">
        <div>
            <h6 class="text-primary text-uppercase ls-2 mb-1">Administración</h6>
            <h1 class="display-5 text-white fw-bold mb-0 font-teko">GESTIÓN DE ANUNCIOS</h1>
        </div>
        <button class="btn btn-primary font-teko text-uppercase px-4 py-2 ls-1 shadow-primary-glow"
            data-bs-toggle="modal" data-bs-target="#modalAnuncio">
            <i class="fas fa-plus me-2"></i> Nuevo Anuncio
        </button>
    </div>

    <!-- Lista de Anuncios -->
    <div class="row g-4 animate-slide-up">
        <?php foreach ($anuncios as $anuncio): ?>
            <div class="col-md-6 col-lg-4" id="anuncio-card-<?= $anuncio['id'] ?>">
                <div
                    class="card bg-surface-dark border-secondary border-opacity-25 rounded-4 h-100 position-relative group overflow-hidden transition-all hover-translate-y">
                    <!-- Glow Border Effect -->
                    <div
                        class="absolute-fill border-glow opacity-20 group-hover-opacity-100 transition-all pointer-events-none">
                    </div>

                    <div class="card-body p-4 position-relative z-2">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span
                                class="badge rounded-pill px-3 py-1 font-teko text-uppercase ls-1 
                                <?= $anuncio['prioridad'] === 'alta' ? 'bg-danger shadow-danger-glow' : ($anuncio['prioridad'] === 'media' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                <?= $anuncio['prioridad'] ?>
                            </span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-icon btn-sm btn-outline-danger border-opacity-25"
                                    onclick="eliminarAnuncio(<?= $anuncio['id'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <h4 class="text-white font-teko text-uppercase ls-1 mb-2">
                            <?= htmlspecialchars($anuncio['titulo']) ?>
                        </h4>
                        <p class="text-muted small mb-4">
                            <?= nl2br(htmlspecialchars($anuncio['contenido'])) ?>
                        </p>

                        <div
                            class="mt-auto pt-3 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                            <div class="text-muted small font-teko">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?= date('d/m/Y', strtotime($anuncio['fecha_creacion'])) ?>
                            </div>
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" <?= $anuncio['activo'] ? 'checked' : '' ?>
                                    onchange="toggleAnuncio(
                            <?= $anuncio['id'] ?>)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Nuevo Anuncio -->
<div class="modal fade" id="modalAnuncio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface border-secondary border-opacity-50">
            <div class="modal-header border-secondary border-opacity-10">
                <h5 class="modal-title font-teko text-uppercase ls-1">Publicar Nuevo Anuncio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formAnuncio">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Título</label>
                        <input type="text" name="titulo"
                            class="form-control bg-dark border-secondary border-opacity-50 text-white" required
                            maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary font-teko text-uppercase ls-1">Contenido</label>
                        <textarea name="contenido"
                            class="form-control bg-dark border-secondary border-opacity-50 text-white" required
                            rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Prioridad</label>
                            <select name="prioridad"
                                class="form-select bg-dark border-secondary border-opacity-50 text-white">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary font-teko text-uppercase ls-1">Expira
                                (Opcional)</label>
                            <input type="date" name="fecha_expiracion"
                                class="form-control bg-dark border-secondary border-opacity-50 text-white">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-10 p-4">
                    <button type="button" class="btn btn-outline-secondary font-teko"
                        data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary font-teko px-4 shadow-primary-glow">PUBLICAR
                        AHORA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('formAnuncio').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const res = await fetch('<?= BASE_URL ?>anuncios/guardar', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.ok) {
                location.reload();
            } else {
                alert('Error al publicar');
            }
        } catch (e) { console.error(e); }
    });

    async function eliminarAnuncio(id) {
        if (!confirm('¿Deseas eliminar este anuncio?')) return;
        try {
            const res = await fetch('<?= BASE_URL ?>anuncios/eliminar', {
                method: 'POST',
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.ok) {
                document.getElementById(`anuncio-card-${id}`).remove();
            }
        } catch (e) { console.error(e); }
    }

    async function toggleAnuncio(id) {
        try {
            await fetch('<?= BASE_URL ?>anuncios/toggle', {
                method: 'POST',
                body: JSON.stringify({ id: id })
            });
        } catch (e) { console.error(e); }
    }
</script>

<style>
    .bg-surface {
        background-color: var(--bg-surface) !important;
        color: var(--text-main);
    }

    .hover-translate-y:hover {
        transform: translateY(-5px);
    }

    .custom-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .custom-switch .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.4);
    }
</style>