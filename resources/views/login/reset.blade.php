<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reestablecer Contraseña</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow border-0 p-4 rounded-4" style="max-width: 450px; width: 100%;">
            <div class="text-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2889/2889676.png" alt="Reset Icon" width="60">
                <h4 class="mt-3 fw-bold text-primary">Restablecer Contraseña</h4>
                <p class="text-muted small">Ingrese su correo y nueva contraseña para acceder nuevamente</p>
            </div>

            <form id="resetForm" action="{{ route('admin.password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="correo@admin.com" required>
                    <label for="email">Correo electrónico</label>
                </div>

                <div class="form-floating mb-3 position-relative">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Nueva contraseña" required>
                    <label for="password">Nueva contraseña</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" id="togglePassword">
                        👁️
                    </button>
                </div>

                <div class="form-floating mb-4 position-relative">
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar contraseña" required>
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" id="togglePassword2">
                        👁️
                    </button>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                    🔐 Actualizar contraseña
                </button>
            </form>
        </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      document.getElementById('resetForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                }).then(() => {
                    window.location.href = '/login'; // Redirige después del éxito
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error del servidor',
                text: 'Ocurrió un error inesperado. Intenta más tarde.',
            });
        }
    });
</script>

</body>
</html>