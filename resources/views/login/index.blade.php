<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sistema de Control de Ventas</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('resources/css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-store"></i>
                </div>
                <h1>Sistema de Control de Ventas</h1>
                <p>Panel de Administración</p>
            </div>
            
            <form action="{{ route('login.submit') }}" autocomplete="off" method="POST" id="loginForm" class="login-form">
                @csrf   
                <div class="form-group">
                    <label for="username">Usuario Administrador</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Ingrese su nombre de usuario" autocomplete="off" required>
                    </div>
                    <span class="error-message" id="username-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" autocomplete="off" required>
                        <i class="fas fa-eye-slash toggle-password" style="margin-right:0" id="togglePassword"></i>
                    </div>
                    <span class="error-message" id="password-error"></span>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Recordar sesión</label>
                    </div>
                    <a href="{{ route('admin.email') }}" class="forgot-password">¿Olvidó su contraseña?</a>
                </div>
                
                <button type="submit" class="login-button" id="loginButton">
                    <span class="button-text">Iniciar Sesión</span>
                    <span class="button-loader"><i class="fas fa-circle-notch fa-spin"></i></span>
                </button>
            </form>
            
            <div class="login-footer">
                <p>© 2024 Sistema de Control de Ventas</p>
                <p><a href="#">Términos y condiciones</a> | <a href="#">Soporte técnico</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('resources/js/login.js')}}"></script>

</body>
</html>