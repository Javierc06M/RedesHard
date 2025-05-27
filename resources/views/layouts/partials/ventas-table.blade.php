                    <table class="sa-table" id="salesTable">
                        <thead>
                            <tr>
                                <th class="sa-table__th sa-table__th--sortable" data-sort="date">
                                    <span>Fecha</span>
                                    <i class="fas fa-sort"></i>
                                </th>
                                <th class="sa-table__th sa-table__th--sortable" data-sort="name">
                                    <span>Nombre</span>
                                    <i class="fas fa-sort"></i>
                                </th>
                                <th class="sa-table__th sa-table__th--sortable" data-sort="phone">
                                    <span>Teléfono</span>
                                    <i class="fas fa-sort"></i>
                                </th>
                                <th class="sa-table__th" data-sort="description">
                                    <span>Descripción</span>
                                </th>
                                <th class="sa-table__th sa-table__th--sortable" data-sort="amount">
                                    <span>Pago</span>
                                    <i class="fas fa-sort"></i>
                                </th>
                                <th class="sa-table__th sa-table__th--sortable" data-sort="method">
                                    <span>Modalidad de Pago</span>
                                    <i class="fas fa-sort"></i>
                                </th>
                                <th class="sa-table__th sa-table__th--actions">
                                    <span>Acciones</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="salesTableBody">
                                @foreach ($ventas as $venta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $venta->nombre }}</td>
                                        <td>{{ $venta->telefono }}</td>
                                        <td>{{ $venta->descripcion }}</td>
                                        <td class="sa-amount">S/ {{ number_format($venta->costo, 2) }}</td>
                                        <td>
                                            <div class="sa-payment-method">
                                                <i class="fas fa-credit-card"></i> {{ $venta->tipo_pago }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="sa-actions">
{{--                                                 <button class="sa-actions__btn" title="Ver detalles"><i class="fas fa-eye"></i></button> --}}
                                                <button class="sa-actions__btn btn-edit" 
                                                    data-id="{{ $venta->id }}"
                                                    data-fecha="{{ $venta->fecha }}"
                                                    data-tipo_pago="{{ $venta->tipo_pago }}"
                                                    data-nombre="{{ $venta->nombre }}"
                                                    data-telefono="{{ $venta->telefono }}"
                                                    data-descripcion="{{ $venta->descripcion }}"
                                                    data-costo="{{ $venta->costo }}"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>


                                                <button class="sa-actions__btn btn-eliminar" 
                                                        data-id="{{ $venta->id }}" 
                                                        data-nombre="{{ $venta->cliente_nombre }}" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="sa-table-footer">
                            <div class="sa-table-footer__info">
                                Mostrando <span id="currentRange">
                                    {{ ($ventas->currentPage() - 1) * $ventas->perPage() + 1 }}-{{ min($ventas->currentPage() * $ventas->perPage(), $ventas->total()) }}
                                </span> de <span id="totalItems">{{ $ventas->total() }}</span> Ventas
                            </div>

                            <div class="sa-pagination">
                                {{-- Botón anterior --}}
                                <button 
                                    class="sa-pagination__btn sa-pagination__btn--prev" 
                                    onclick="loadVentas('{{ $ventas->previousPageUrl() }}')" 
                                    {{ $ventas->onFirstPage() ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                {{-- Números de página --}}
                                <div class="sa-pagination__pages">
                                    @foreach ($ventas->getUrlRange(1, $ventas->lastPage()) as $page => $url)
                                        @if ($page == $ventas->currentPage())
                                            <button class="sa-pagination__page sa-pagination__page--active">{{ $page }}</button>
                                        @elseif ($page == 1 || $page == $ventas->lastPage() || abs($page - $ventas->currentPage()) <= 1)
                                            <button class="sa-pagination__page" onclick="loadVentas('{{ $url }}')">{{ $page }}</button>
                                        @elseif ($page == 2 && $ventas->currentPage() > 4)
                                            <span class="sa-pagination__ellipsis">...</span>
                                        @elseif ($page == $ventas->lastPage() - 1 && $ventas->currentPage() < $ventas->lastPage() - 3)
                                            <span class="sa-pagination__ellipsis">...</span>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Botón siguiente --}}
                                <button 
                                    class="sa-pagination__btn sa-pagination__btn--next" 
                                    onclick="loadVentas('{{ $ventas->nextPageUrl() }}')" 
                                    {{ $ventas->hasMorePages() ? '' : 'disabled' }}>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', async function (e) {
            if (e.target.closest('.btn-eliminar')) {
                const button = e.target.closest('.btn-eliminar');
                const id = button.dataset.id;
                const nombre = button.dataset.nombre;

                Swal.fire({
                    title: `¿Eliminar a ${nombre}?`,
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/ventas/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });

                            const resultData = await response.json();

                            if (response.ok) {
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: resultData.message || 'El registro ha sido eliminado.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Recargar la página después de 2 segundos
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: resultData.message || 'No se pudo eliminar el registro.'
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de red',
                                text: 'No se pudo conectar con el servidor.'
                            });
                        }
                    }
                });
            }
        });
    });
</script>
