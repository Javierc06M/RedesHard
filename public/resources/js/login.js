document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const usernameError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');
    const togglePassword = document.getElementById('togglePassword');
    const loginButton = document.getElementById('loginButton');

    
    
    // Mostrar/ocultar contraseña
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Cambiar el icono
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
    
    // Validación del formulario
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Validar usuario
        if (usernameInput.value.trim() === '') {
            showError(usernameError, 'El usuario es obligatorio');
            isValid = false;
        } else if (usernameInput.value.length < 3) {
            showError(usernameError, 'El usuario debe tener al menos 3 caracteres');
            isValid = false;
        } else {
            clearError(usernameError);
        }
        
        // Validar contraseña
        if (passwordInput.value === '') {
            showError(passwordError, 'La contraseña es obligatoria');
            isValid = false;
        } else if (passwordInput.value.length < 6) {
            showError(passwordError, 'La contraseña debe tener al menos 6 caracteres');
            isValid = false;
        } else {
            clearError(passwordError);
        }

        if (isValid) {
            const formData = new FormData(loginForm);
            loginButton.disabled = true;
            loginButton.classList.add('loading');

            fetch(loginForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loginButton.disabled = false;
                loginButton.classList.remove('loading');

                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                loginButton.disabled = false;
                loginButton.classList.remove('loading');
                console.error(error);
                showNotification('Error del servidor. Inténtalo de nuevo más tarde.', 'error');
            });
        }
    });
    
    // Función para mostrar errores
    function showError(element, message) {
        element.textContent = message;
        element.style.opacity = '1';
        element.parentElement.querySelector('.input-group').classList.add('error');
        element.parentElement.querySelector('input').classList.add('error-input');
    }
    
    // Función para limpiar errores
    function clearError(element) {
        element.textContent = '';
        element.style.opacity = '0';
        element.parentElement.querySelector('.input-group').classList.remove('error');
        element.parentElement.querySelector('input').classList.remove('error-input');
    }
    
    // Función para mostrar notificaciones
    function showNotification(message, type) {
        // Eliminar notificaciones existentes
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => {
            document.body.removeChild(notification);
        });
        
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        // Icono según el tipo
        const icon = type === 'success' 
            ? '<i class="fas fa-check-circle"></i>' 
            : '<i class="fas fa-exclamation-circle"></i>';
        
        notification.innerHTML = `${icon} ${message}`;
        
        // Añadir al DOM
        document.body.appendChild(notification);
        
        // Mostrar con animación
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Ocultar después de 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Limpiar errores al escribir
    usernameInput.addEventListener('input', function() {
        clearError(usernameError);
    });
    
    passwordInput.addEventListener('input', function() {
        clearError(passwordError);
    });
    
    // Efecto de focus en los inputs
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
    // Añadir clase CSS para inputs con error
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .input-group.error input {
                border-color: var(--error-color);
                background-color: rgba(239, 68, 68, 0.05);
            }
            
            .input-group.error i:not(.toggle-password) {
                color: var(--error-color);
            }
            
            .input-group.focused i {
                color: var(--primary-color);
            }
        </style>
    `);
});