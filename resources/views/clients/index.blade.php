@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-users me-2 text-primary"></i>Gestión de Clientes</h3>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-1"></i>Nuevo Cliente
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted">Buscar cliente</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchClient" class="form-control" placeholder="Nombre, documento..." oninput="filtrarClientes()">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small" id="tablaClientes">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr data-search="{{ strtolower($client->name . ' ' . ($client->last_name ?? '') . ' ' . ($client->document ?? '') . ' ' . ($client->nit ?? '')) }}">
                        <td class="fw-bold text-dark">{{ $client->name }}</td>
                        <td>{{ $client->last_name ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $client->document ?? $client->nit ?? '-' }}</span></td>
                        <td>{{ $client->phone ?? '-' }}</td>
                        <td>{{ $client->email ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="openEditModal({{ $client->id }}, '{{ $client->name }}', '{{ $client->last_name ?? '' }}', '{{ $client->document ?? '' }}', '{{ $client->phone ?? '' }}', '{{ $client->email ?? '' }}', '{{ $client->address ?? '' }}')" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No hay clientes registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellido</label>
                        <input type="text" name="last_name" class="form-control">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Tipo Documento</label>
                            <select name="type_document_id" class="form-select">
                                <option value="">Seleccionar</option>
                                @foreach(\App\Models\TypeDocument::all() as $td)
                                    <option value="{{ $td->id }}">{{ $td->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Documento</label>
                            <input type="text" name="document" class="form-control" placeholder="RUC, DNI, etc.">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editClientForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellido</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Documento</label>
                        <input type="text" name="document" id="edit_document" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="address" id="edit_address" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filtrarClientes() {
    const term = document.getElementById('searchClient').value.toLowerCase().trim();
    let visibles = 0;

    document.querySelectorAll('#tablaClientes tbody tr[data-search]').forEach(tr => {
        const show = !term || tr.dataset.search.includes(term);
        tr.style.display = show ? '' : 'none';
        if (show) visibles++;
    });
}

function openCreateModal() {
    new bootstrap.Modal(document.getElementById('createClientModal')).show();
}

function openEditModal(id, name, lastName, document, phone, email, address) {
    document.getElementById('editClientForm').action = '/clients/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_document').value = document;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_address').value = address;
    new bootstrap.Modal(document.getElementById('editClientModal')).show();
}
</script>
@endsection
