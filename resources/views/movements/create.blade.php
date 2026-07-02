@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-secondary fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Registrar Entrada / Salida</h3>

    <div class="card border-0 shadow-sm p-4">
        <form id="movementForm">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Almacén</label>
                    <select id="warehouseSelect" class="form-select bg-light" required>
                        <option value="">Selecciona Almacén</option>
                        @foreach(\App\Models\Warehouse::whereHas('branches', function($q) { $q->where('company_id', auth()->user()->company_id); })->get() as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Tipo de Operación</label>
                    <select id="movementTypeSelect" class="form-select bg-light" required>
                        <option value="">Selecciona Tipo de Operación</option>
                        @foreach($movementTypes as $mt)
                            @if($mt->id != 3)
                                <option value="{{ $mt->id }}">{{ $mt->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="productsContainer" class="mb-4">
                <h4 class="fw-bold">Productos</h4>
                <button type="button" class="btn btn-sm btn-secondary mb-2" id="addProductBtn">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>

            <div class="text-end">
                <a href="{{ route('movements.index') }}" class="btn btn-light border me-2">Volver</a>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Procesar Movimiento</button>
            </div>
        </form>

        <div id="errorMessage" class="alert alert-danger mt-3 d-none"></div>
    </div>
</div>

<!-- Modal para Seleccionar/Crear Lotes -->
<div class="modal fade" id="lotModal" tabindex="-1" aria-labelledby="lotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lotModalLabel">Gestionar Lotes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Código de Lote</label>
                        <input type="text" id="newLotCode" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" id="newLotExpiration" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="addNewLotBtn" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Agregar Lote
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Código Lote</th>
                                <th>Fecha Vencimiento</th>
                                <th>Cantidad Original</th>
                                <th>Disponible</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody id="lotModalBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <p class="me-auto fw-bold">Total: <span id="lotModalTotal">0</span></p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmLotsBtn" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
let products = @json($products);
let productsMap = {};
products.forEach(p => productsMap[p.id] = p);

let allotments = @json($allotments);
let allotmentProductsMap = @json($loteProductos);

let productItemsData = [];
let productCounter = 0;
let currentProductIndex = null;
let lotModal = null;

document.getElementById('addProductBtn').addEventListener('click', addProduct);

document.addEventListener('DOMContentLoaded', function() {
    lotModal = new bootstrap.Modal(document.getElementById('lotModal'));
    
    document.getElementById('addNewLotBtn').addEventListener('click', addNewLotToModal);
    document.getElementById('confirmLotsBtn').addEventListener('click', confirmModalLots);
    
    const updateAllProducts = () => {
        document.querySelectorAll('.product-item').forEach((item, index) => {
            const select = item.querySelector('.product-select');
            if (select.value) {
                handleProductChange(select, index);
            }
        });
    };
    
    document.getElementById('warehouseSelect').addEventListener('change', updateAllProducts);
    document.getElementById('movementTypeSelect').addEventListener('change', updateAllProducts);
});

function addProduct() {
    const container = document.getElementById('productsContainer');
    const productDiv = document.createElement('div');
    productDiv.className = 'product-item card p-3 mb-3';
    productDiv.id = `product-${productCounter}`;
    productDiv.dataset.index = productCounter;
    
    productItemsData.push({
        productId: null,
        quantity: 1,
        selectedLots: []
    });
    
    productDiv.innerHTML = `
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Producto</label>
                <select class="form-select product-select bg-light" data-counter="${productCounter}" required>
                    <option value="">-- Elija un producto --</option>
                    ${products.map(p => `<option value="${p.id}" data-type-management="${p.type_management_id}">${p.name} (SKU: ${p.code})</option>`).join('')}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Cantidad</label>
                <input type="number" class="form-control quantity-input bg-light" min="1" value="1" required readonly>
            </div>
            <div class="col-md-4 allotment-fields" style="display: none;">
                <label class="form-label fw-bold text-muted">Lotes</label>
                <div class="input-group">
                    <input type="text" class="form-control lot-summary-input bg-light" readonly placeholder="0 lotes seleccionados">
                    <button type="button" class="btn btn-primary lot-action-btn ms-2" title="Gestionar Lotes">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                <div class="selected-lots-summary mt-2" style="display: none;"></div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-product-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(productDiv);
    
    const select = productDiv.querySelector('.product-select');
    select.addEventListener('change', function() {
        const index = parseInt(this.closest('.product-item').dataset.index);
        handleProductChange(this, index);
    });
    
    const removeBtn = productDiv.querySelector('.remove-product-btn');
    removeBtn.addEventListener('click', function() {
        const index = parseInt(this.closest('.product-item').dataset.index);
        productItemsData.splice(index, 1);
        
        document.querySelectorAll('.product-item').forEach((item, i) => {
            item.dataset.index = i;
            const select = item.querySelector('.product-select');
            select.dataset.counter = i;
        });
        
        productDiv.remove();
    });
    
    const lotBtn = productDiv.querySelector('.lot-action-btn');
    lotBtn.addEventListener('click', function() {
        const index = parseInt(this.closest('.product-item').dataset.index);
        openLotModal(index);
    });
    
    productCounter++;
}

function handleProductChange(select, index) {
    const productId = select.value;
    productItemsData[index].productId = productId ? parseInt(productId) : null;
    productItemsData[index].selectedLots = [];
    
    const productDiv = select.closest('.product-item');
    const allotmentFields = productDiv.querySelector('.allotment-fields');
    const quantityInput = productDiv.querySelector('.quantity-input');
    
    if (!productId) {
        allotmentFields.style.display = 'none';
        quantityInput.readOnly = false;
        quantityInput.value = 1;
        productItemsData[index].quantity = 1;
        return;
    }
    
    const product = productsMap[productId];
    if (product.type_management_id === 2) {
        allotmentFields.style.display = 'block';
        quantityInput.readOnly = true;
        updateLotSummary(productDiv, index);
    } else {
        allotmentFields.style.display = 'none';
        quantityInput.readOnly = false;
        quantityInput.addEventListener('change', function() {
            productItemsData[index].quantity = parseInt(this.value);
        });
    }
}

function openLotModal(index) {
    currentProductIndex = index;
    const productId = productItemsData[index].productId;
    const warehouseId = parseInt(document.getElementById('warehouseSelect').value);
    const movementType = parseInt(document.getElementById('movementTypeSelect').value);
    const today = new Date().toISOString().split('T')[0];
    
    document.getElementById('newLotCode').value = '';
    document.getElementById('newLotExpiration').value = '';
    
    let productAllotments = [];
    if (movementType === 2) {
        productAllotments = allotments.filter(a => {
            const hasProduct = allotmentProductsMap.some(ap => ap.allotment_id === a.id && ap.product_id == productId);
            const isNotExpired = !a.expiration_date || a.expiration_date >= today;
            return hasProduct && a.warehouse_id === warehouseId && isNotExpired;
        }).sort((a, b) => {
            const aAvail = (a.quantity || 0) - (a.quantity_dispatch || 0);
            const bAvail = (b.quantity || 0) - (b.quantity_dispatch || 0);
            if (aAvail > 0 && bAvail > 0) return 0;
            if (aAvail > 0) return -1;
            return 1;
        });
    } else {
        productAllotments = allotments.filter(a => {
            const hasProduct = allotmentProductsMap.some(ap => ap.allotment_id === a.id && ap.product_id == productId);
            return hasProduct && a.warehouse_id === warehouseId;
        });
    }
    
    const tbody = document.getElementById('lotModalBody');
    tbody.innerHTML = '';
    
    productAllotments.forEach(a => {
        const originalQty = a.quantity || 0;
        const availableQty = (a.quantity || 0) - (a.quantity_dispatch || 0);
        const selectedLot = productItemsData[index].selectedLots.find(l => l.isNew === false && l.allotmentId === a.id);
        const selectedQty = selectedLot ? selectedLot.quantity : 0;
        
        tbody.innerHTML += `
            <tr data-allotment-id="${a.id}" data-is-new="false">
                <td class="fw-bold">${a.code}</td>
                <td>${a.expiration_date || 'No vence'}</td>
                <td>${originalQty}</td>
                <td class="text-success">${availableQty}</td>
                <td>
                    <input type="number" class="form-control lot-quantity-input bg-light" min="0" max="${movementType === 2 ? availableQty : 999999}" value="${selectedQty}">
                </td>
            </tr>
        `;
    });
    
    productItemsData[index].selectedLots.filter(l => l.isNew === true).forEach(l => {
        tbody.innerHTML += `
            <tr data-allotment-id="new-${l.code}" data-is-new="true">
                <td class="fw-bold">${l.code} <span class="badge bg-info">Nuevo</span></td>
                <td>${l.expiration_date || 'No vence'}</td>
                <td>-</td>
                <td>-</td>
                <td>
                    <input type="number" class="form-control lot-quantity-input bg-light" min="1" value="${l.quantity}">
                </td>
            </tr>
        `;
    });
    
    document.querySelectorAll('.lot-quantity-input').forEach(input => {
        input.addEventListener('change', updateLotModalTotal);
        input.addEventListener('input', updateLotModalTotal);
    });
    
    updateLotModalTotal();
    
    document.getElementById('lotModal').querySelector('.modal-title').textContent = movementType === 1 ? 'Seleccionar / Crear Lotes (Entrada)' : 'Seleccionar Lotes (Salida)';
    document.getElementById('newLotCode').closest('.row').style.display = movementType === 1 ? 'flex' : 'none';
    
    lotModal.show();
}

function addNewLotToModal() {
    const code = document.getElementById('newLotCode').value;
    const expiration = document.getElementById('newLotExpiration').value;
    
    if (!code) {
        alert('Por favor proporciona un código de lote');
        return;
    }
    
    const tbody = document.getElementById('lotModalBody');
    const newRow = document.createElement('tr');
    newRow.dataset.allotmentId = `new-${code}`;
    newRow.dataset.isNew = 'true';
    newRow.innerHTML = `
        <td class="fw-bold">${code} <span class="badge bg-info">Nuevo</span></td>
        <td>${expiration || 'No vence'}</td>
        <td>-</td>
        <td>-</td>
        <td>
            <input type="number" class="form-control lot-quantity-input bg-light" min="1" value="1">
        </td>
    `;
    
    const input = newRow.querySelector('.lot-quantity-input');
    input.addEventListener('change', updateLotModalTotal);
    input.addEventListener('input', updateLotModalTotal);
    
    tbody.appendChild(newRow);
    
    document.getElementById('newLotCode').value = '';
    document.getElementById('newLotExpiration').value = '';
    
    updateLotModalTotal();
}

function updateLotModalTotal() {
    let total = 0;
    document.querySelectorAll('.lot-quantity-input').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    document.getElementById('lotModalTotal').textContent = total;
}

function confirmModalLots() {
    const selectedLots = [];
    let total = 0;
    
    document.querySelectorAll('#lotModalBody tr').forEach(row => {
        const isNew = row.dataset.isNew === 'true';
        const quantity = parseInt(row.querySelector('.lot-quantity-input').value) || 0;
        
        if (quantity > 0) {
            if (isNew) {
                const code = row.querySelector('td.fw-bold').textContent.replace(' <span class="badge bg-info">Nuevo</span>', '');
                const expiration = row.cells[1].textContent.trim() === 'No vence' ? null : row.cells[1].textContent;
                selectedLots.push({
                    isNew: true,
                    code: code,
                    expiration_date: expiration,
                    quantity: quantity
                });
            } else {
                selectedLots.push({
                    isNew: false,
                    allotmentId: parseInt(row.dataset.allotmentId),
                    quantity: quantity
                });
            }
            total += quantity;
        }
    });
    
    if (total === 0) {
        alert('Por favor selecciona al menos una cantidad de lote');
        return;
    }
    
    productItemsData[currentProductIndex].selectedLots = selectedLots;
    productItemsData[currentProductIndex].quantity = total;
    
    const productDiv = document.querySelector(`.product-item[data-index="${currentProductIndex}"]`);
    const quantityInput = productDiv.querySelector('.quantity-input');
    quantityInput.value = total;
    
    updateLotSummary(productDiv, currentProductIndex);
    
    lotModal.hide();
}

function updateLotSummary(productDiv, index) {
    const summaryDiv = productDiv.querySelector('.selected-lots-summary');
    const lotCount = productItemsData[index].selectedLots.length;
    
    if (lotCount === 0) {
        summaryDiv.style.display = 'none';
        productDiv.querySelector('.lot-summary-input').value = '0 lotes seleccionados';
        return;
    }
    
    let summaryHTML = '<div class="alert alert-info py-2"><strong>Lotes seleccionados:</strong><ul class="mb-0">';
    productItemsData[index].selectedLots.forEach(l => {
        if (l.isNew) {
            summaryHTML += `<li>${l.code} (nuevo): ${l.quantity} unidades</li>`;
        } else {
            const allotment = allotments.find(a => a.id === l.allotmentId);
            summaryHTML += `<li>${allotment ? allotment.code : 'Lote #' + l.allotmentId}: ${l.quantity} unidades</li>`;
        }
    });
    summaryHTML += '</ul></div>';
    
    summaryDiv.innerHTML = summaryHTML;
    summaryDiv.style.display = 'block';
    productDiv.querySelector('.lot-summary-input').value = lotCount + ' lote(s) seleccionados';
}

document.getElementById('movementForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const warehouseId = document.getElementById('warehouseSelect').value;
    const movementType = parseInt(document.getElementById('movementTypeSelect').value);
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.classList.add('d-none');
    
    const productsData = [];
    
    try {
        for (let index = 0; index < productItemsData.length; index++) {
            const itemData = productItemsData[index];
            const productDiv = document.querySelector(`.product-item[data-index="${index}"]`);
            const product = productsMap[itemData.productId];
            
            if (!product) {
                throw new Error(`Producto ${index + 1} no seleccionado`);
            }
            
            const pd = { product_id: itemData.productId };
            
            if (movementType === 1) {
                pd.quantity_in = itemData.quantity;
                if (product.type_management_id === 2) {
                    if (itemData.selectedLots.length === 0) {
                        throw new Error(`Para el producto ${product.name}, debes seleccionar/crear al menos un lote`);
                    }
                    pd.selected_lots = itemData.selectedLots;
                }
            } else {
                pd.quantity_out = itemData.quantity;
                if (product.type_management_id === 2) {
                    if (itemData.selectedLots.length === 0) {
                        throw new Error(`Para el producto ${product.name}, debes seleccionar al menos un lote`);
                    }
                    pd.selected_lots = itemData.selectedLots;
                }
            }
            
            productsData.push(pd);
        }
        
        if (productsData.length === 0) {
            throw new Error('Debes agregar al menos un producto');
        }
        
        const endpoint = movementType === 1 ? '/api/movements/incoming' : '/api/movements/outgoing';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
            },
            body: JSON.stringify({
                warehouse_id: parseInt(warehouseId),
                movement_type_id: movementType,
                products: productsData
            })
        });
        
        if (!response.ok) {
            throw new Error(await response.text());
        }
        
        alert('Movimiento creado exitosamente!');
        window.location.href = '/movements';
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('d-none');
        console.error(error);
    }
});
</script>
@endsection
