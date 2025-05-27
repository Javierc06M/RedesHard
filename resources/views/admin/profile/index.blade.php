@extends('layouts.admin')

@section('title', 'Perfil de Administrador')

@section('content')

   <div class="content-wrapper">
        <!-- Información del Administrador -->
        <section class="admin-section">
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-user-cog"></i> Información del Administrador</h2>
                    <button class="action-btn" id="editBtn" onclick="toggleEdit()">
                        <i class="fas fa-edit"></i>
                        <span>Editar</span>
                    </button>
                </div>
                
                <div class="admin-content">
                    <div class="admin-profile">
                        <div class="profile-image">
                            @if ($admin->imagen)
                               <img id="adminImage" src="{{ asset('storage/' . $admin->imagen) }}" alt="Administrador">
                            @else
                                <div class="initials-placeholder" id="adminInitials">
                                    {{ strtoupper(substr($admin->nombre, 0, 1) . substr($admin->apellidos, 0, 1)) }}
                                </div>
                            @endif

                            <div class="image-overlay" id="imageOverlay" style="display: none;">
                                <input type="file" id="imageInput" accept="image/*">
                                <label for="imageInput" class="upload-label">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-fields">
                        <div class="field-row">
                            <div class="field-group">
                                <label>Nombre</label>
                                <div class="field-container">
                                    <span class="field-display" data-field="nombre">{{ $admin->nombre}}</span>
                                    <input type="text" class="field-edit" data-field="nombre" value="{{ $admin->nombre }}">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label>Apellidos</label>
                                <div class="field-container">
                                    <span class="field-display" data-field="apellido">{{ $admin->apellidos}}</span>
                                    <input type="text" class="field-edit" data-field="apellido" value="{{ $admin->apellidos }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="field-row">
                            <div class="field-group">
                                <label>Email</label>
                                <div class="field-container">
                                    <span class="field-display" data-field="email">{{$admin->email}}</span>
                                    <input type="email" class="field-edit" data-field="email" value="{{ $admin->email }}">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label>Username</label>
                                <div class="field-container">
                                    <span class="field-display" data-field="username">{{$admin->username}}</span>
                                    <input type="text" class="field-edit" data-field="username" value="{{ $admin->username  }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="edit-actions" id="editActions" style="display: none;">
                    <button class="action-btn save-btn" onclick="saveChanges()">
                        <i class="fas fa-check"></i>
                        <span>Guardar</span>
                    </button>
                    <button class="action-btn cancel-btn" onclick="cancelEdit()">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Tabla de Ventas -->
        <section class="sales-section">
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Registro de Ventas</h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar ventas...">
                        </div>
                        <button class="action-btn add-btn" onclick="exportPDF()">
                            <i class="fa-solid fa-download"></i>
                            <span>Exportar</span>
                        </button>
                    </div>
                </div>
                
                <div class="table-wrapper" id="table-wrapper">
                    <table class="sales-table" id="salesTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Descripción</th>
                                <th>Importe</th>
                                <th>Pago</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                        @foreach ($ventas as $venta)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                <td data-label="Cliente">{{ $venta->nombre }}</td>
                                <td data-label="Teléfono">{{ $venta->telefono }}</td>
                                <td data-label="Descripción">{{ $venta->descripcion }}</td>
                                <td data-label="Importe">S/ {{ number_format($venta->costo, 2) }}</td>
                                <td data-label="Pago">
                                    <span class="payment-badge credit">{{ $venta->tipo_pago }}</span>
                                </td>
                                <td data-label="Acciones" class="actions-cell">
                                    <!-- Puedes colocar botones de editar/eliminar si lo deseas -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>


    <script>
        async function exportPDF() {
            const { jsPDF } = window.jspdf;
            const originalTable = document.getElementById('salesTable');

            // Clonamos la tabla original
            const clonedTable = originalTable.cloneNode(true);

            // Eliminamos la columna de acciones del encabezado
            const headerRow = clonedTable.querySelector('thead tr');
            if (headerRow) {
                const lastTh = headerRow.querySelector('th:last-child');
                if (lastTh && lastTh.classList.contains('actions-col')) {
                    lastTh.remove();
                }
            }

            // Eliminamos las celdas de acciones en cada fila del cuerpo
            const rows = clonedTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const lastTd = row.querySelector('td:last-child');
                if (lastTd && lastTd.classList.contains('actions-cell')) {
                    lastTd.remove();
                }
            });

            // Creamos un contenedor oculto para renderizar solo el contenido que exportaremos
            const exportWrapper = document.createElement('div');
            exportWrapper.style.padding = '20px';
            exportWrapper.style.backgroundColor = '#ffffff';
            exportWrapper.style.fontFamily = 'Arial, sans-serif';
            exportWrapper.style.color = '#333';
            exportWrapper.style.width = '100%';
            exportWrapper.innerHTML = `
                <h2 style="text-align: center; margin-bottom: 20px;">Reporte de Ventas</h2>
                ${clonedTable.outerHTML}
            `;
            document.body.appendChild(exportWrapper);

            // Usamos html2canvas para capturar la tabla clonada sin los botones
            await html2canvas(exportWrapper, { scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgProps = pdf.getImageProperties(imgData);

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('reporte_ventas.pdf');
            });

            // Eliminamos el contenedor temporal
            document.body.removeChild(exportWrapper);
        }

    </script>

@endsection