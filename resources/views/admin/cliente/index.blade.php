@extends('layouts.admin')

@section('title', 'Administracion de Ventas')

@section('content')


    @php
        $admin = Auth::guard('admin')->user();
    @endphp
<!-- Contenido Principal -->
    <main class="sa-main" id="mainContent">
        <div class="sa-content">
            <div class="sa-page-header">
                <div class="sa-page-header__title">
                    <h1>Administracion</h1>
                    <p>Bienvenido de nuevo, <strong> {{ $admin -> nombre }}. </strong> Aquí tienes un resumen de tus ventas.</p>
                </div>
                <div class="sa-page-header__actions">
                    <div class="sa-date-picker">
                        <button class="sa-date-picker__btn">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Últimos 30 días</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <button type="button" id="btnNuevaVenta" onclick="openModal()" class="sa-btn sa-btn--primary">
                        <i class="fas fa-plus"></i>
                        <span>Nueva Venta</span>
                    </button>

                </div>
            </div>

            <div class="sa-dashboard-cards">
                <div class="sa-card sa-card--stats">
                    <div class="sa-card__icon sa-card__icon--blue">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="sa-card__content">
                        <h3 class="sa-card__title">Ventas Totales</h3>
                        <p class="sa-card__value">S/ {{ number_format($totalActual, 2) }}</p>

                        @if ($porcentajeCambio >= 0)
                            <p class="sa-card__change sa-card__change--up">
                                <i class="fas fa-arrow-up"></i> {{ number_format($porcentajeCambio, 1) }}% desde el mes pasado
                            </p>
                        @else
                            <p class="sa-card__change sa-card__change--down">
                                <i class="fas fa-arrow-down"></i> {{ number_format(abs($porcentajeCambio), 1) }}% desde el mes pasado
                            </p>
                        @endif
                    </div>
                </div>
                
                {{-- <div class="sa-card sa-card--stats">
                    <div class="sa-card__icon sa-card__icon--green">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="sa-card__content">
                        <h3 class="sa-card__title">Nuevos Clientes</h3>
                        <p class="sa-card__value">54</p>
                        <p class="sa-card__change sa-card__change--up">
                            <i class="fas fa-arrow-up"></i> 8.2% desde el mes pasado
                        </p>
                    </div>
                </div> --}}
                
                <div class="sa-card sa-card--stats">
                    <div class="sa-card__icon sa-card__icon--purple">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="sa-card__content">
                        <h3 class="sa-card__title">Productos Vendidos</h3>
                        <p class="sa-card__value">{{ number_format($productosActual) }}</p>

                        @if ($variacionProductos >= 0)
                            <p class="sa-card__change sa-card__change--up">
                                <i class="fas fa-arrow-up"></i> {{ number_format($variacionProductos, 1) }}% desde el mes pasado
                            </p>
                        @else
                            <p class="sa-card__change sa-card__change--down">
                                <i class="fas fa-arrow-down"></i> {{ number_format(abs($variacionProductos), 1) }}% desde el mes pasado
                            </p>
                        @endif
                    </div>
                </div>
                
                {{-- <div class="sa-card sa-card--stats">
                    <div class="sa-card__icon sa-card__icon--orange">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="sa-card__content">
                        <h3 class="sa-card__title">Tasa de Conversión</h3>
                        <p class="sa-card__value">24.8%</p>
                        <p class="sa-card__change sa-card__change--down">
                            <i class="fas fa-arrow-down"></i> 2.1% desde el mes pasado
                        </p>
                    </div>
                </div> --}}
            </div>

            <!-- Tabla de Ventas -->
            <div class="sa-card sa-card--table">
                <div class="sa-card__header">
                    <h2 class="sa-card__header-title">Últimas Transacciones</h2>
                    <div class="sa-card__header-actions">
                        <div class="sa-search sa-search--sm">
                            <i class="fas fa-search sa-search__icon"></i>
                            <input type="text" placeholder="Buscar venta..." class="sa-search__input" id="tableSearch">
                        </div>
                        {{-- <div class="sa-dropdown">
                            <button class="sa-btn sa-btn--outline sa-btn--sm">
                                <i class="fas fa-filter"></i>
                                <span>Filtrar</span>
                            </button>
                        </div> --}}
                        <button id="btnExportarPDF" class="sa-btn sa-btn--outline sa-btn--sm">
                            <i class="fas fa-download"></i>
                            <span>Exportar</span>
                        </button>
                    </div>
                </div>
                <div class="sa-table-container" id="ventas-container">
                    @include('layouts.partials.ventas-table')
                </div>

            </div>
        </div>
    </main>

       <!-- Overlay del modal -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
        <!-- Modal Nueva Venta -->
        <div class="sales-modal" onclick="event.stopPropagation()">
            <div class="modal-container">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        <i class="fas fa-cash-register modal-icon"></i>
                        Registrar Nueva Venta
                    </h5>
                    <button type="button" class="close-btn" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form class="sales-form" id="ventaForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="form-body">
                        <div class="form-grid">
                            <div class="input-group-half">
                                <label for="fecha" class="input-label">Fecha</label>
                                <input type="date" class="form-input" name="fecha" id="fecha" required>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="tipo_pago" class="input-label">Tipo de Pago</label>
                                <select class="form-select" name="tipo_pago" id="tipo_pago" required>
                                    <option selected disabled value="">Selecciona...</option>
                                    <option value="Efectivo">💵 Efectivo</option>
                                    <option value="Transferencia">🏦 Transferencia</option>
                                    <option value="Tarjeta">💳 Yape</option>
                                </select>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="nombre" class="input-label">Nombre del Cliente</label>
                                <input type="text" class="form-input" name="nombre" id="nombre" placeholder="Ingresa el nombre completo" required>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="telefono" class="input-label">Teléfono</label>
                                <input type="tel" class="form-input" name="telefono" id="telefono" placeholder="+52 123 456 7890" required>
                            </div>
                            
                            <div class="input-group-full">
                                <label for="descripcion" class="input-label">Descripción</label>
                                <textarea class="form-textarea" name="descripcion" id="descripcion" rows="3" placeholder="Detalle del producto o servicio vendido..."></textarea>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="costo" class="input-label">Costo</label>
                                <div class="currency-input">
                                    <span class="currency-symbol">$</span>
                                    <input type="number" class="form-input-currency" name="costo" id="costo" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer-custom">
                        <button type="button" class="cancel-btn" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-check"></i> Registrar Venta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL PARA EDITAR AL CLIENTE -->

    <div class="modal-overlay" id="modalEditOverlay" onclick="closeEditModal()">
        <!-- Modal Editar Venta -->
        <div class="sales-modal" onclick="event.stopPropagation()">
            <div class="modal-container">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        <i class="fas fa-pen-square modal-icon"></i>
                        Editar Venta
                    </h5>
                    <button type="button" class="close-btn" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form class="sales-form" id="ventaEditForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-body">
                        <div class="form-grid">
                            <div class="input-group-half">
                                <label for="edit_fecha" class="input-label">Fecha</label>
                                <input type="date" class="form-input" name="fecha" id="edit_fecha" required>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="edit_tipo_pago" class="input-label">Tipo de Pago</label>
                                <select class="form-select" name="tipo_pago" id="edit_tipo_pago" required>
                                    <option selected disabled value="">Selecciona...</option>
                                    <option value="Efectivo">💵 Efectivo</option>
                                    <option value="Transferencia">🏦 Transferencia</option>
                                    <option value="Tarjeta">💳 Yape</option>
                                </select>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="edit_nombre" class="input-label">Nombre del Cliente</label>
                                <input type="text" class="form-input" name="nombre" id="edit_nombre" required>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="edit_telefono" class="input-label">Teléfono</label>
                                <input type="tel" class="form-input" name="telefono" id="edit_telefono" required>
                            </div>
                            
                            <div class="input-group-full">
                                <label for="edit_descripcion" class="input-label">Descripción</label>
                                <textarea class="form-textarea" name="descripcion" id="edit_descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="input-group-half">
                                <label for="edit_costo" class="input-label">Costo</label>
                                <div class="currency-input">
                                    <span class="currency-symbol">$</span>
                                    <input type="number" class="form-input-currency" name="costo" id="edit_costo" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer-custom">
                        <button type="button" class="cancel-btn" onclick="closeEditModal()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


     <script>
        const userData = {
            nombre: @json(Auth::guard('admin')->user()->nombre),
            email: @json(Auth::guard('admin')->user()->email),
            avatar: @json(Auth::guard('admin')->user()->avatar_url ?? '/placeholder.svg?height=48&width=48')
        };
    </script>

     <script>
         function openModal() {
            document.getElementById('modalOverlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // Establecer fecha actual por defecto
            document.getElementById('fecha').value = new Date().toISOString().split('T')[0];
        }

        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.sales-form');

            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                try {
                    const response = await fetch("{{ route('ventas.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Venta registrada',
                            text: result.message || 'La venta fue registrada correctamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        form.reset();

                        // Recarga la página luego de 2 segundos
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else if (response.status === 422) {
                        let errors = '';
                        if (result.errors) {
                            errors = Object.values(result.errors).flat().join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            html: errors || 'Hay errores en el formulario.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Ocurrió un error al registrar la venta.'
                        });
                    }
                } catch (error) {
                    console.error('Error al enviar:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de red',
                        text: 'No se pudo conectar con el servidor.'
                    });
                }
            });
        });

    </script>

    <script>
        function loadVentas(url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('ventas-container').innerHTML = html;
                // Si usas íconos o componentes JS, reactivar aquí
                feather.replace(); // por ejemplo si usas Feather Icons
            });
        }


    </script>

    <script>
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_fecha').value = this.dataset.fecha;
                document.getElementById('edit_tipo_pago').value = this.dataset.tipo_pago;
                document.getElementById('edit_nombre').value = this.dataset.nombre;
                document.getElementById('edit_telefono').value = this.dataset.telefono;
                document.getElementById('edit_descripcion').value = this.dataset.descripcion;
                document.getElementById('edit_costo').value = this.dataset.costo;

                document.getElementById('modalEditOverlay').style.display = 'flex';
            });
        });

        function closeEditModal() {
            document.getElementById('modalEditOverlay').style.display = 'none';
        }

        document.getElementById('ventaEditForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const id = document.getElementById('edit_id').value;
            const url = `/ventas/${id}`;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                closeEditModal();

                // Opcional: recarga la tabla o la página
                setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un problema al actualizar la venta.'
                });
                console.error('Error:', error);
            });
        });
    </script>

    <script>
       document.addEventListener('DOMContentLoaded', () => {
        const btnExportarPDF = document.getElementById('btnExportarPDF');
        
        btnExportarPDF.addEventListener('click', () => {
            const table = document.querySelector('#salesTable');
            if (!table) {
            alert('No se encontró la tabla de ventas.');
            return;
            }
            
            // Obtener filas y datos, excluyendo la columna de acciones
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            
            // Encabezados, exceptuando el de "Acciones"
            const headers = Array.from(table.querySelectorAll('thead th'))
            .slice(0, -1) // quitamos la última columna "Acciones"
            .map(th => th.innerText.trim());
            
            // Agregar la columna secuencial como primer header
            headers.unshift('#');
            
            // Construir cuerpo de tabla para pdfmake con texto centrado y negritas en encabezado
            const body = [];
            
            // Primera fila: encabezados en negrita y centrados
            const headerRow = headers.map(text => ({ text: text, style: 'tableHeader', alignment: 'center' }));
            body.push(headerRow);
            
            // Filas de datos, centradas y tamaño de fuente aumentado
            rows.forEach((row, index) => {
            const cells = Array.from(row.querySelectorAll('td')).slice(0, -1); // excluimos "Acciones"
            const rowData = [{ text: (index + 1).toString(), alignment: 'center' }]; // columna secuencial
            
            cells.forEach(td => {
                rowData.push({ text: td.innerText.trim(), alignment: 'center', fontSize: 12 });
            });
            
            body.push(rowData);
            });
            
            // Fecha y hora actual formateada
            const now = new Date();
            const fechaExportacion = now.toLocaleString();
            
            // Definición del PDF
            const docDefinition = {
            pageSize: 'A4',
            pageMargins: [40, 60, 40, 60],
            header: {
                text: 'Reporte de Ventas',
                style: 'header',
                alignment: 'center',
                margin: [0, 10, 0, 20]
            },
            footer: (currentPage, pageCount) => ({
                columns: [
                { text: `Exportado: ${fechaExportacion}`, alignment: 'left', margin: [40, 0, 0, 0], fontSize: 8, italics: true },
                { text: `Página ${currentPage} de ${pageCount}`, alignment: 'right', margin: [0, 0, 40, 0], fontSize: 8 }
                ]
            }),
            content: [
                {
                table: {
                    headerRows: 1,
                    widths: ['auto', '*', '*', '*', 'auto', '*', '*'],
                    body: body
                },
                layout: {
                    fillColor: (rowIndex) => (rowIndex === 0 ? '#4F81BD' : rowIndex % 2 === 0 ? '#F2F2F2' : null),
                    hLineColor: () => '#CCCCCC',
                    vLineColor: () => '#CCCCCC',
                    paddingLeft: () => 8,
                    paddingRight: () => 8,
                    paddingTop: () => 6,
                    paddingBottom: () => 6
                }
                }
            ],
            styles: {
                header: {
                fontSize: 20,
                bold: true,
                color: '#4F81BD'
                },
                tableHeader: {
                bold: true,
                fontSize: 14,
                color: 'white'
                }
            },
            defaultStyle: {
                font: 'Roboto',
                fontSize: 12,
                color: '#333333'
            }
            };
            
            pdfMake.createPdf(docDefinition).download(`ventas_${now.toISOString().slice(0,10)}.pdf`);
        });
        });

    </script>
    
    <script>
        document.getElementById('tableSearch').addEventListener('input', function () {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('#salesTable tbody tr');

            rows.forEach(row => {
                const nombre = row.cells[1]?.textContent.toLowerCase() || '';
                const telefono = row.cells[2]?.textContent.toLowerCase() || '';

                const coincide = nombre.includes(search) || telefono.includes(search);
                row.style.display = coincide ? '' : 'none';
            });
        });
    </script>





@endsection