<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recuperar Contraseña</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
   <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow p-4 rounded" style="width: 100%; max-width: 400px;">
        <h4 class="text-center mb-4">🔒 Recuperar contraseña del Admin</h4>

        <form action="{{ route('admin.password.send') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">📧 Enviar enlace</button>
        </form>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if(session('status'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('status') }}',
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>

</body>
</html>