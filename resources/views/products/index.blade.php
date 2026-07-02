@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-boxes me-2 text-info"></i>Catálogo de Almacén</h3>

        <span class="fw-bold text-dark me-2">
        <i class="fas fa-file-download me-1 text-secondary"></i>
            Descargar tabla:
        </span>

        <div class="btn-group" role="group">
            <a href="{{ route('products.pdf') }}" class="btn text-white" style="background-color:#e40011;">
                <i class="fas fa-file-pdf"></i>
            </a>

            <a href="{{ route('products.excel') }}" class="btn text-white" style="background-color:#49ba47;">
                <i class="fas fa-file-excel"></i>
            </a>

        </div>

        <a href="{{ route('products.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Producto
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm py-2">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>Código Único</th>
                        <th>Nombre del Producto / Medicamento</th>
                        <th>Precio Venta</th>
                        <th>Costo</th>
                        <th>Código Barras</th>
                        <th class="text-center">Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-muted small fw-bold">{{ $product->code ?? 'S/C' }}</td>

                        <td class="fw-bold text-dark">{{ $product->name }}</td>

                        <td>S/. {{ number_format($product->price ?? 0, 2) }}</td>

                        <td>S/. {{ number_format($product->price_cost ?? 0, 2) }}</td>

                        <td class="text-muted small">{{ $product->barcode ?? 'S/B' }}</td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-success"
                                onclick="openEditModal(this)"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-cost="{{ $product->price_cost }}"
                                data-barcode="{{ $product->barcode }}"> Editar </button>

                            <button
                                type="button"
                                class="btn btn-danger"
                                onclick="deleteProduct({{ $product->id }})">
                                Eliminar
                            </button>

                            <button type="button"
                                    class="btn btn-outline-info"
                                    onclick="openInfoModal(this)"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-code="{{ $product->code }}"
                                    data-price="{{ $product->price }}"
                                    data-cost="{{ $product->price_cost }}"
                                    data-barcode="{{ $product->barcode }}">Info
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-box-open d-block fa-2x mb-2 opacity-50"></i>
                            No hay productos registrados aún en esta compañía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!--cuadrito para editar/Bootstrap Modal Editar-->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit_id">

                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" id="edit_name" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Precio Venta</label>
                        <input type="number" id="edit_price" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Costo</label>
                        <input type="number" id="edit_price_cost" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Código Barras</label>
                        <input type="text" id="edit_barcode" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" onclick="updateProduct()">Guardar</button>
                </div>

            </div>
        </div>
    </div>
</div>

<!--modal ver info-->
<div class="modal fade" id="infoProductModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Información del Producto</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>Nombre:</strong> <span id="info_name"></span></p>

                <p><strong>Código:</strong> <span id="info_code"></span></p>

                <p><strong>Precio:</strong> S/. <span id="info_price"></span></p>

                <p><strong>Costo:</strong> S/. <span id="info_cost"></span></p>

                <p><strong>Código Barras:</strong> <span id="info_barcode"></span></p>

            </div>

        </div>
    </div>
</div>


<!-- js para modal -->
<script>
    /*modificar*/
    function openEditModal(btn) {

        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_price').value = btn.dataset.price;
        document.getElementById('edit_price_cost').value = btn.dataset.cost;
        document.getElementById('edit_barcode').value = btn.dataset.barcode;

        const modal = new bootstrap.Modal(
            document.getElementById('editProductModal')
        );

        modal.show();
    }

    function updateProduct() {

        const id = document.getElementById('edit_id').value;

        fetch(`/products/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: document.getElementById('edit_name').value,
                price: document.getElementById('edit_price').value,
                price_cost: document.getElementById('edit_price_cost').value,
                barcode: document.getElementById('edit_barcode').value
            })
        })
        .then(async res => {

            const data = await res.json();

            if (!res.ok) {
                console.log(data);
                alert('Error al actualizar');
                return;
            }

            alert('Producto actualizado');

            const modal = bootstrap.Modal.getInstance(
                document.getElementById('editProductModal')
            );

            modal.hide();

            location.reload();
        })
        .catch(error => {
            console.error(error);
            alert('Error de conexión');
        });
    }

    /*eliminar*/
    function deleteProduct(id) {

    if (!confirm('¿Deseas eliminar este producto?')) {
        return;
    }

        fetch(`/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async res => {

            const data = await res.json();

            if (!res.ok) {
                console.log(data);
                alert('Error al eliminar');
                return;
            }

            alert('Producto eliminado');
            location.reload();
        })
        .catch(error => {
            console.error(error);
            alert('Error de conexión');
        });
    }

    function deleteProduct(id) {

    if (!confirm('¿Deseas eliminar este producto?')) {
        return;
    }

    fetch(`/products/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
        })
        .then(async res => {

            const data = await res.json();

            if (!res.ok) {
                console.log(data);
                alert('Error al eliminar');
                return;
            }

            alert('Producto eliminado');
            location.reload();
        })
        .catch(error => {
            console.error(error);
            alert('Error de conexión');
        });
    }

    /*ver info*/
    function openInfoModal(btn) {

    document.getElementById('info_name').textContent = btn.dataset.name;
    document.getElementById('info_code').textContent = btn.dataset.code;
    document.getElementById('info_price').textContent = btn.dataset.price;
    document.getElementById('info_cost').textContent = btn.dataset.cost;
    document.getElementById('info_barcode').textContent = btn.dataset.barcode;

    new bootstrap.Modal(
        document.getElementById('infoProductModal')
    ).show();
    }
</script>

@endsection
