// Estado global
let isEditing = false;
let originalValues = {};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    console.log('Contenido del administrador cargado');
});

function initializeEventListeners() {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }
    
    // Cambio de imagen
    const imageInput = document.getElementById('imageInput');
    if (imageInput) {
        imageInput.addEventListener('change', handleImageChange);
    }
}

// === FUNCIONES DE EDICIÓN DEL ADMINISTRADOR ===

function toggleEdit() {
    if (isEditing) {
        cancelEdit();
    } else {
        enterEditMode();
    }
}

function enterEditMode() {
    isEditing = true;
    
    saveOriginalValues();
    showEditFields();
    updateEditUI(true);
}

function saveOriginalValues() {
    const displays = document.querySelectorAll('.field-display');
    originalValues = {};
    displays.forEach(display => {
        const field = display.getAttribute('data-field');
        originalValues[field] = display.textContent.trim();
    });
}

function showEditFields() {
    // Ocultar displays y mostrar inputs
    document.querySelectorAll('.field-display').forEach(d => d.style.display = 'none');
    document.querySelectorAll('.field-edit').forEach(i => i.style.display = 'block');
    
    // Mostrar overlay de imagen para subir foto
    const imageOverlay = document.getElementById('imageOverlay');
    if (imageOverlay) {
        imageOverlay.style.display = 'flex';
    }
}

function updateEditUI(editing) {
    const editBtn = document.getElementById('editBtn');
    const editActions = document.getElementById('editActions');
    
    if (editing) {
        editBtn.innerHTML = '<i class="fas fa-times"></i><span>Cancelar</span>';
        editBtn.onclick = cancelEdit;
        editActions.style.display = 'flex';
    } else {
        editBtn.innerHTML = '<i class="fas fa-edit"></i><span>Editar</span>';
        editBtn.onclick = toggleEdit;
        editActions.style.display = 'none';
    }
}

async function saveChanges() {
    if (!validateFields()) return;

    const formData = new FormData();

    // Agregar campos de texto
    const inputs = document.querySelectorAll('.field-edit');
    inputs.forEach(input => {
        const field = input.getAttribute('data-field');
        formData.append(field, input.value.trim());
    });

    // Agregar imagen si hay
    const imageInput = document.getElementById('imageInput');
    if (imageInput && imageInput.files.length > 0) {
        formData.append('imagen', imageInput.files[0]);
    }

    try {
        const response = await fetch('/admin/profile/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                // NO poner Content-Type
            },
            body: formData
        });

        if (!response.ok) throw new Error('Error al actualizar la información');

        const result = await response.json();

        // Actualizar texto en UI
        inputs.forEach(input => {
            const field = input.getAttribute('data-field');
            const display = document.querySelector(`.field-display[data-field="${field}"]`);
            if (display) display.textContent = input.value.trim();
        });

        // Actualizar imagen si backend envía URL
        if (result.imageUrl) {
            const adminImage = document.getElementById('adminImage');
            if (adminImage) {
                adminImage.src = result.imageUrl + '?t=' + new Date().getTime();
            }
        }

        exitEditMode();
        showNotification('Información actualizada correctamente', 'success');

        // 🔄 Recargar página automáticamente
        setTimeout(() => {
            window.location.reload();
        }, 800); // Espera opcional para ver notificación (0.8 seg)

    } catch (error) {
        showNotification(error.message || 'Error al actualizar', 'error');
    }
}


function validateFields() {
    const inputs = document.querySelectorAll('.field-edit');
    for (let input of inputs) {
        const value = input.value.trim();
        const field = input.getAttribute('data-field');

        if (!value) {
            showNotification(`El campo ${field} es obligatorio`, 'error');
            input.focus();
            return false;
        }
        if (field === 'email' && !isValidEmail(value)) {
            showNotification('Por favor, ingresa un email válido', 'error');
            input.focus();
            return false;
        }
    }
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function cancelEdit() {
    // Restaurar valores originales en inputs
    Object.entries(originalValues).forEach(([field, value]) => {
        const input = document.querySelector(`.field-edit[data-field="${field}"]`);
        if (input) input.value = value;
    });

    exitEditMode();
}

function exitEditMode() {
    isEditing = false;
    
    // Mostrar displays y ocultar inputs
    document.querySelectorAll('.field-edit').forEach(i => i.style.display = 'none');
    document.querySelectorAll('.field-display').forEach(d => d.style.display = 'block');

    // Ocultar overlay de imagen
    const imageOverlay = document.getElementById('imageOverlay');
    if (imageOverlay) imageOverlay.style.display = 'none';

    updateEditUI(false);
    originalValues = {};
}

function handleImageChange(event) {
    const file = event.target.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            showNotification('Por favor, selecciona un archivo de imagen válido', 'error');
            event.target.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showNotification('La imagen debe ser menor a 5MB', 'error');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            let adminImage = document.getElementById('adminImage');
            const adminInitials = document.getElementById('adminInitials');

            if (!adminImage) {
                // Crear imagen si no existía
                adminImage = document.createElement('img');
                adminImage.id = 'adminImage';
                adminImage.alt = 'Administrador';
                const profileImageDiv = document.querySelector('.profile-image');
                profileImageDiv.insertBefore(adminImage, profileImageDiv.firstChild);
            }

            adminImage.src = e.target.result;
            adminImage.style.display = 'block';

            if (adminInitials) {
                adminInitials.style.display = 'none';
            }
        };

        reader.readAsDataURL(file);
    }
}

document.getElementById('imageInput')?.addEventListener('change', handleImageChange);

// === FUNCIONES DE LA TABLA DE VENTAS ===

function handleSearch(event) {
    const searchTerm = event.target.value.toLowerCase();
    const rows = document.querySelectorAll('#salesTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(searchTerm);
        row.style.display = isVisible ? '' : 'none';
    });
}

function convertCellsToInputs(cells) {
    for (let i = 0; i < cells.length - 1; i++) {
        const cell = cells[i];
        const currentValue = cell.textContent.trim();
        
        switch (i) {
            case 0: // Fecha
                cell.innerHTML = `<input type="date" class="table-input" value="${currentValue}">`;
                break;
            case 4: // Importe
                const numericValue = currentValue.replace(/[€,]/g, '');
                cell.innerHTML = `<input type="number" step="0.01" class="table-input" value="${numericValue}">`;
                break;
            case 5: // Tipo de pago
                const paymentType = getPaymentTypeFromText(currentValue);
                cell.innerHTML = createPaymentSelect(paymentType);
                break;
            default: // Texto normal
                cell.innerHTML = `<input type="text" class="table-input" value="${currentValue}">`;
        }
    }
}

function getPaymentTypeFromText(text) {
    const lowerText = text.toLowerCase();
    if (lowerText.includes('tarjeta')) return 'credit';
    if (lowerText.includes('transferencia')) return 'transfer';
    if (lowerText.includes('efectivo')) return 'cash';
    if (lowerText.includes('paypal')) return 'paypal';
    return 'credit';
}

function createPaymentSelect(selectedType) {
    return `
        <select class="table-select">
            <option value="credit" ${selectedType === 'credit' ? 'selected' : ''}>Tarjeta</option>
            <option value="transfer" ${selectedType === 'transfer' ? 'selected' : ''}>Transferencia</option>
            <option value="cash" ${selectedType === 'cash' ? 'selected' : ''}>Efectivo</option>
            <option value="paypal" ${selectedType === 'paypal' ? 'selected' : ''}>PayPal</option>
        </select>
    `;
}

function updateRowActions(actionsCell, mode, originalData = null) {
    if (mode === 'edit') {
        actionsCell.innerHTML = `
            <button class="table-action edit-action" onclick="saveSaleChanges(this)" title="Guardar">
                <i class="fas fa-check"></i>
            </button>
            <button class="table-action delete-action" onclick="cancelSaleEdit(this, ${JSON.stringify(originalData).replace(/"/g, '&quot;')})" title="Cancelar">
                <i class="fas fa-times"></i>
            </button>
        `;
    } else {
        actionsCell.innerHTML = `
            <button class="table-action edit-action" onclick="editSale(this)" title="Editar">
                <i class="fas fa-edit"></i>
            </button>
            <button class="table-action delete-action" onclick="deleteSale(this)" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        `;
    }
}

function saveSaleChanges(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Obtener y validar valores
    const values = [];
    for (let i = 0; i < cells.length - 1; i++) {
        const input = cells[i].querySelector('input, select');
        if (!input) continue;
        
        const value = input.value.trim();
        if (!value && i !== 4) { // El importe puede ser 0
            showNotification('Todos los campos son obligatorios', 'error');
            input.focus();
            return;
        }
        values.push(value);
    }
    
    // Actualizar celdas con los nuevos valores
    updateCellsWithValues(cells, values);
    
    // Restaurar botones de acción
    updateRowActions(cells[cells.length - 1], 'normal');
    
    showNotification('Venta actualizada correctamente', 'success');
}

function updateCellsWithValues(cells, values) {
    values.forEach((value, index) => {
        const cell = cells[index];
        
        switch (index) {
            case 4: // Importe
                cell.textContent = `€${parseFloat(value).toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                break;
            case 5: // Tipo de pago
                const paymentInfo = getPaymentInfo(value);
                cell.innerHTML = `<span class="payment-badge ${paymentInfo.class}">${paymentInfo.text}</span>`;
                break;
            default:
                cell.textContent = value;
        }
    });
}

function getPaymentInfo(type) {
    const types = {
        'credit': { text: 'Tarjeta', class: 'credit' },
        'transfer': { text: 'Transferencia', class: 'transfer' },
        'cash': { text: 'Efectivo', class: 'cash' },
        'paypal': { text: 'PayPal', class: 'paypal' }
    };
    return types[type] || types['credit'];
}

function cancelSaleEdit(button, originalData) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Restaurar contenido original
    originalData.forEach((data, index) => {
        if (index < cells.length - 1) {
            cells[index].innerHTML = data;
        }
    });
    
    // Restaurar botones de acción
    updateRowActions(cells[cells.length - 1], 'normal');
}

function deleteSale(button) {
    if (confirm('¿Estás seguro de que deseas eliminar esta venta?')) {
        const row = button.closest('tr');
        row.style.animation = 'fadeOut 0.3s ease';
        
        setTimeout(() => {
            row.remove();
            showNotification('Venta eliminada correctamente', 'success');
        }, 300);
    }
}

function addNewSale() {
    const tableBody = document.getElementById('salesTableBody');
    const newRow = document.createElement('tr');
    
    const today = new Date().toISOString().split('T')[0];
    
    newRow.innerHTML = `
        <td data-label="Fecha"><input type="date" class="table-input" value="${today}"></td>
        <td data-label="Cliente"><input type="text" class="table-input" placeholder="Nombre del cliente"></td>
        <td data-label="Teléfono"><input type="tel" class="table-input" placeholder="+34 XXX XXX XXX"></td>
        <td data-label="Descripción"><input type="text" class="table-input" placeholder="Descripción del servicio"></td>
        <td data-label="Importe"><input type="number" step="0.01" class="table-input" placeholder="0.00"></td>
        <td data-label="Pago">${createPaymentSelect('credit')}</td>
        <td data-label="Acciones" class="actions-cell">
            <button class="table-action edit-action" onclick="saveSaleChanges(this)" title="Guardar">
                <i class="fas fa-check"></i>
            </button>
            <button class="table-action delete-action" onclick="removeNewSale(this)" title="Cancelar">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    
    // Insertar al principio de la tabla
    tableBody.insertBefore(newRow, tableBody.firstChild);
    
    // Enfocar el primer input
    const firstInput = newRow.querySelector('input');
    if (firstInput) {
        firstInput.focus();
    }
    
    // Animación de entrada
    newRow.style.animation = 'fadeIn 0.3s ease';
}

function removeNewSale(button) {
    const row = button.closest('tr');
    row.style.animation = 'fadeOut 0.3s ease';
    
    setTimeout(() => {
        row.remove();
    }, 300);
}

// === FUNCIONES DE UTILIDAD ===

function showNotification(message, type = 'success') {
    // Remover notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 3000);
}

// Agregar estilos de animación para fadeOut
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
`;
document.head.appendChild(style);