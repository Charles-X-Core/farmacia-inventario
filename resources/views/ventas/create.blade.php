@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-secondary fw-bold"><i class="fas fa-file-invoice me-2 text-success"></i>Nuevo Comprobante</h3>

    @if(session('success'))
        <div class="alert alert-success py-2 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="saleForm" action="{{ route('ventas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="warehouse_id" id="warehouseId" value="{{ $warehouse->id ?? '' }}">

        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Cliente</label>
                    <div class="input-group">
                        <input type="hidden" name="client_id" id="clientId" value="">
                        <input type="text" id="clientName" class="form-control bg-light" value="Consumidor Final" readonly placeholder="Consumidor Final">
                        <button type="button" class="btn btn-outline-primary" onclick="openClientModal()" title="Buscar cliente">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setIncognito()" title="Cliente Incógnito">
                            <i class="fas fa-user-secret"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Serie</label>
                    <input type="text" class="form-control bg-light" value="{{ $series->serie ?? 'NV01' }}-{{ str_pad($series->number ?? 1, 6, '0', STR_PAD_LEFT) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Descuento (S/.)</label>
                    <input type="number" name="discount" id="discountInput" class="form-control" value="0" min="0" step="0.01" oninput="recalcularTotal()">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-success w-100" onclick="openPaymentModal()">
                        <i class="fas fa-credit-card me-1"></i>Continuar venta
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-box me-2 text-primary"></i>Productos</h5>
                <button type="button" class="btn btn-sm btn-primary" onclick="openProductSearch()">
                    <i class="fas fa-plus me-1"></i>Agregar producto
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small" id="productsTable">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th>Producto</th>
                            <th width="130">Cantidad</th>
                            <th width="130">Precio (S/.)</th>
                            <th width="130">Subtotal (S/.)</th>
                            <th width="80">Lote</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="productsBody">
                    </tbody>
                </table>
                <div id="emptyProducts" class="text-center py-5 text-muted">
                    <i class="fas fa-shopping-cart fa-3x mb-3 d-block text-secondary"></i>
                    <p class="mb-0">No hay productos agregados. Haz clic en "Agregar producto".</p>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <div class="text-end">
                    <div class="text-muted small">Subtotal: <span id="subtotalDisplay" class="fw-bold text-dark">S/. 0.00</span></div>
                    <div class="text-muted small">Descuento: <span id="discountDisplay" class="fw-bold text-danger">- S/. 0.00</span></div>
                    <h4 class="mb-0 text-success fw-bold">Total: S/. <span id="totalDisplay">0.00</span></h4>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="clientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2"></i>Buscar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="clientSearchInput" class="form-control mb-3" placeholder="Buscar por nombre, apellido o documento..." oninput="searchClients()">
                <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                    <table class="table table-hover small mb-0">
                        <thead class="table-light sticky-top">
                            <tr><th>Nombre</th><th>Documento</th><th></th></tr>
                        </thead>
                        <tbody id="clientSearchResults">
                            <tr><td colspan="3" class="text-center text-muted">Escribe para buscar...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buscar Producto -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Buscar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="productSearchInput" class="form-control mb-3" placeholder="Buscar por nombre, código o código de barras..." oninput="filterProducts()">
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                    <table class="table table-hover small mb-0">
                        <thead class="table-light sticky-top">
                            <tr><th>Código</th><th>Producto</th><th>Precio</th><th>Tipo</th><th></th></tr>
                        </thead>
                        <tbody id="productSearchResults">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Seleccionar Lotes -->
<div class="modal fade" id="lotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lotModalTitle">Seleccionar Lotes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Código Lote</th>
                                <th>Vencimiento</th>
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
                <button type="button" class="btn btn-primary" onclick="confirmLots()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Métodos de Pago -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Métodos de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    Total a pagar: <strong>S/. <span id="paymentTotalDisplay">0.00</span></strong>
                    &nbsp;|&nbsp; Por pagar: <strong class="text-danger" id="paymentRemaining">S/. 0.00</strong>
                </div>
                <div id="paymentRows">
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addPaymentRow()">
                    <i class="fas fa-plus me-1"></i>Agregar método
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" onclick="confirmPayment()" id="confirmPaymentBtn" disabled>
                    <i class="fas fa-check me-1"></i>Confirmar y enviar
                </button>
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
let paymentMethods = @json($paymentMethods);
let duplicateData = @json(session('duplicate'));

let saleItems = [];
let itemCounter = 0;
let currentLotItemIndex = null;
let paymentRowCounter = 0;

let productModal = null;
let lotModal = null;
let paymentModal = null;
let clientModal = null;

document.addEventListener('DOMContentLoaded', function() {
    productModal = new bootstrap.Modal(document.getElementById('productModal'));
    lotModal = new bootstrap.Modal(document.getElementById('lotModal'));
    paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    clientModal = new bootstrap.Modal(document.getElementById('clientModal'));

    if (duplicateData) {
        loadDuplicateData(duplicateData);
    }
});

function openProductSearch() {
    document.getElementById('productSearchInput').value = '';
    filterProducts();
    productModal.show();
}

function filterProducts() {
    const term = document.getElementById('productSearchInput').value.toLowerCase().trim();
    const tbody = document.getElementById('productSearchResults');
    tbody.innerHTML = '';

    const filtered = products.filter(p => {
        if (!p.is_sale || !p.status) return false;
        if (!term) return true;
        return (p.name || '').toLowerCase().includes(term) ||
               (p.code || '').toLowerCase().includes(term) ||
               (p.barcode || '').toLowerCase().includes(term);
    });

    filtered.forEach(p => {
        const isLotes = p.type_management_id == 2;
        tbody.innerHTML += `
            <tr>
                <td class="fw-bold">${p.code || 'S/C'}</td>
                <td>${p.name}</td>
                <td>S/. ${parseFloat(p.price).toFixed(2)}</td>
                <td><span class="badge ${isLotes ? 'bg-warning text-dark' : 'bg-info text-white'}">${isLotes ? 'LOTES' : 'LIBRE'}</span></td>
                <td><button type="button" class="btn btn-sm btn-success" onclick="addProduct(${p.id})"><i class="fas fa-plus"></i></button></td>
            </tr>`;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No se encontraron productos.</td></tr>';
    }
}

function addProduct(productId) {
    const product = productsMap[productId];
    if (!product) return;

    const item = {
        index: itemCounter,
        product_id: product.id,
        name: product.name,
        code: product.code,
        price: parseFloat(product.price),
        quantity: 1,
        is_lotes: product.type_management_id == 2,
        selected_lots: []
    };

    saleItems.push(item);
    itemCounter++;

    renderProductsTable();
    productModal.hide();
}

function renderProductsTable() {
    const tbody = document.getElementById('productsBody');
    const empty = document.getElementById('emptyProducts');
    tbody.innerHTML = '';

    if (saleItems.length === 0) {
        empty.style.display = 'block';
        recalcularTotal();
        return;
    }

    empty.style.display = 'none';

    saleItems.forEach((item, idx) => {
        const subtotal = item.price * item.quantity;
        const lotBadge = item.is_lotes
            ? `<button type="button" class="btn btn-sm btn-outline-warning" onclick="openLotModal(${idx})" title="Seleccionar lotes"><i class="fas fa-list"></i> ${item.selected_lots.length > 0 ? item.selected_lots.length + ' lotes' : 'Lotes'}</button>`
            : '<span class="text-muted small">N/A</span>';

        tbody.innerHTML += `
            <tr data-index="${idx}">
                <td class="fw-bold">${item.name} <span class="text-muted small">(${item.code})</span></td>
                <td><input type="number" class="form-control form-control-sm" value="${item.quantity}" min="1" onchange="updateQty(${idx}, this.value)"></td>
                <td><input type="number" class="form-control form-control-sm" value="${item.price}" min="0" step="0.01" onchange="updatePrice(${idx}, this.value)"></td>
                <td class="fw-bold text-success">S/. ${subtotal.toFixed(2)}</td>
                <td>${lotBadge}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${idx})"><i class="fas fa-trash"></i></button></td>
            </tr>`;
    });

    recalcularTotal();
}

function updateQty(idx, val) {
    saleItems[idx].quantity = parseInt(val) || 1;
    renderProductsTable();
}

function updatePrice(idx, val) {
    saleItems[idx].price = parseFloat(val) || 0;
    renderProductsTable();
}

function removeItem(idx) {
    saleItems.splice(idx, 1);
    renderProductsTable();
}

function recalcularTotal() {
    let subtotal = 0;
    saleItems.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = Math.max(0, subtotal - discount);

    document.getElementById('subtotalDisplay').textContent = 'S/. ' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = '- S/. ' + discount.toFixed(2);
    document.getElementById('totalDisplay').textContent = total.toFixed(2);
}

function openLotModal(itemIndex) {
    currentLotItemIndex = itemIndex;
    const item = saleItems[itemIndex];
    const tbody = document.getElementById('lotModalBody');
    tbody.innerHTML = '';

    const warehouseId = parseInt(document.getElementById('warehouseId').value);
    const today = new Date().toISOString().split('T')[0];

    const productAllotments = allotments.filter(a => {
        const hasProduct = allotmentProductsMap.some(ap => ap.allotment_id === a.id && ap.product_id == item.product_id);
        const isNotExpired = !a.expiration_date || a.expiration_date >= today;
        const hasStock = a.available > 0;
        return hasProduct && a.warehouse_id === warehouseId && isNotExpired && hasStock;
    }).sort((a, b) => {
        if (a.expiration_date && b.expiration_date) return a.expiration_date.localeCompare(b.expiration_date);
        if (a.expiration_date) return -1;
        return 1;
    });

    document.getElementById('lotModalTitle').textContent = 'Seleccionar Lotes - ' + item.name;

    productAllotments.forEach(a => {
        const selected = item.selected_lots.find(l => l.allotmentId === a.id);
        const selectedQty = selected ? selected.quantity : 0;

        tbody.innerHTML += `
            <tr>
                <td class="fw-bold">${a.code}</td>
                <td>${a.expiration_date || 'No vence'}</td>
                <td><span class="badge bg-success">${a.available}</span></td>
                <td><input type="number" class="form-control form-control-sm lot-qty-input" min="0" max="${a.available}" value="${selectedQty}" data-allotment-id="${a.id}"></td>
            </tr>`;
    });

    document.querySelectorAll('.lot-qty-input').forEach(inp => {
        inp.addEventListener('input', updateLotModalTotal);
    });

    updateLotModalTotal();
    lotModal.show();
}

function updateLotModalTotal() {
    let total = 0;
    document.querySelectorAll('.lot-qty-input').forEach(inp => {
        total += parseInt(inp.value) || 0;
    });
    document.getElementById('lotModalTotal').textContent = total;
}

function confirmLots() {
    const selectedLots = [];
    let total = 0;

    document.querySelectorAll('.lot-qty-input').forEach(inp => {
        const qty = parseInt(inp.value) || 0;
        if (qty > 0) {
            selectedLots.push({
                allotmentId: parseInt(inp.dataset.allotmentId),
                quantity: qty
            });
            total += qty;
        }
    });

    if (total === 0) {
        alert('Selecciona al menos un lote.');
        return;
    }

    saleItems[currentLotItemIndex].selected_lots = selectedLots;
    saleItems[currentLotItemIndex].quantity = total;

    lotModal.hide();
    renderProductsTable();
}

function openClientModal() {
    document.getElementById('clientSearchInput').value = '';
    document.getElementById('clientSearchResults').innerHTML = '<tr><td colspan="3" class="text-center text-muted">Escribe para buscar...</td></tr>';
    clientModal.show();
}

function searchClients() {
    const term = document.getElementById('clientSearchInput').value.trim();
    if (term.length < 1) {
        document.getElementById('clientSearchResults').innerHTML = '<tr><td colspan="3" class="text-center text-muted">Escribe para buscar...</td></tr>';
        return;
    }

    fetch('/api/clients/search?q=' + encodeURIComponent(term))
        .then(r => r.json())
        .then(clients => {
            const tbody = document.getElementById('clientSearchResults');
            if (clients.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No se encontraron clientes.</td></tr>';
                return;
            }
            tbody.innerHTML = clients.map(c => `
                <tr>
                    <td class="fw-bold">${c.name} ${c.last_name || ''}</td>
                    <td>${c.document || c.nit || '-'}</td>
                    <td><button type="button" class="btn btn-sm btn-success" onclick="selectClient(${c.id}, '${c.name} ${c.last_name || ''}')"><i class="fas fa-check"></i></button></td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById('clientSearchResults').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al buscar.</td></tr>';
        });
}

function selectClient(id, name) {
    document.getElementById('clientId').value = id;
    document.getElementById('clientName').value = name;
    clientModal.hide();
}

function setIncognito() {
    document.getElementById('clientId').value = '';
    document.getElementById('clientName').value = 'Consumidor Final';
}

function openPaymentModal() {
    if (saleItems.length === 0) {
        alert('Agrega al menos un producto antes de continuar.');
        return;
    }

    recalcularTotal();
    const total = parseFloat(document.getElementById('totalDisplay').textContent);

    document.getElementById('paymentTotalDisplay').textContent = total.toFixed(2);
    document.getElementById('paymentRemaining').textContent = total.toFixed(2);

    document.getElementById('paymentRows').innerHTML = '';
    paymentRowCounter = 0;
    addPaymentRow(total);

    paymentModal.show();
}

function addPaymentRow(initialAmount) {
    const total = parseFloat(document.getElementById('totalDisplay').textContent) || 0;
    const existingAmounts = getPaymentTotal();
    const remaining = total - existingAmounts;
    const amount = initialAmount || Math.max(0, remaining);

    const options = paymentMethods.map(pm =>
        `<option value="${pm.id}">${pm.name}</option>`
    ).join('');

    const rowId = 'paymentRow_' + paymentRowCounter++;
    const html = `
        <div class="row g-2 mb-2 align-items-center" id="${rowId}">
            <div class="col-md-6">
                <select class="form-select form-select-sm payment-method" onchange="recalcPayment()">
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-sm payment-amount" value="${amount.toFixed(2)}" min="0.01" step="0.01" oninput="recalcPayment()">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removePaymentRow('${rowId}')"><i class="fas fa-times"></i></button>
            </div>
        </div>`;

    document.getElementById('paymentRows').insertAdjacentHTML('beforeend', html);
    recalcPayment();
}

function removePaymentRow(rowId) {
    document.getElementById(rowId)?.remove();
    recalcPayment();
}

function getPaymentTotal() {
    let sum = 0;
    document.querySelectorAll('.payment-amount').forEach(inp => {
        sum += parseFloat(inp.value) || 0;
    });
    return sum;
}

function recalcPayment() {
    const total = parseFloat(document.getElementById('totalDisplay').textContent) || 0;
    const paid = getPaymentTotal();
    const remaining = total - paid;

    document.getElementById('paymentRemaining').textContent = 'S/. ' + remaining.toFixed(2);
    document.getElementById('paymentRemaining').className = Math.abs(remaining) < 0.01 ? 'fw-bold text-success' : 'fw-bold text-danger';
    document.getElementById('confirmPaymentBtn').disabled = Math.abs(remaining) > 0.01;
}

function confirmPayment() {
    const payments = [];
    document.querySelectorAll('#paymentRows .row').forEach(row => {
        const methodId = row.querySelector('.payment-method').value;
        const amount = parseFloat(row.querySelector('.payment-amount').value) || 0;
        if (amount > 0) {
            payments.push({ payment_method_id: parseInt(methodId), amount: amount });
        }
    });

    if (payments.length === 0) {
        alert('Agrega al menos un método de pago.');
        return;
    }

    paymentModal.hide();
    submitSale(payments);
}

function submitSale(payments) {
    const form = document.getElementById('saleForm');

    document.querySelectorAll('input[name^="products"]').forEach(el => el.remove());
    document.querySelectorAll('input[name^="payments"]').forEach(el => el.remove());

    saleItems.forEach((item, idx) => {
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="products[${idx}][product_id]" value="${item.product_id}">`);
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="products[${idx}][quantity]" value="${item.quantity}">`);
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="products[${idx}][price]" value="${item.price}">`);
        item.selected_lots.forEach((lot, li) => {
            form.insertAdjacentHTML('beforeend', `<input type="hidden" name="products[${idx}][selected_lots][${li}][allotmentId]" value="${lot.allotmentId}">`);
            form.insertAdjacentHTML('beforeend', `<input type="hidden" name="products[${idx}][selected_lots][${li}][quantity]" value="${lot.quantity}">`);
        });
    });

    payments.forEach((pay, idx) => {
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="payments[${idx}][payment_method_id]" value="${pay.payment_method_id}">`);
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="payments[${idx}][amount]" value="${pay.amount}">`);
    });

    form.submit();
}

function loadDuplicateData(data) {
    if (data.client_id) {
        document.getElementById('clientId').value = data.client_id;
    }
    if (data.discount) {
        document.getElementById('discountInput').value = data.discount;
    }
    if (data.products) {
        data.products.forEach(p => {
            const product = productsMap[p.product_id];
            if (!product) return;

            saleItems.push({
                index: itemCounter,
                product_id: product.id,
                name: product.name,
                code: product.code,
                price: parseFloat(p.price),
                quantity: parseInt(p.quantity),
                is_lotes: product.type_management_id == 2,
                selected_lots: p.selected_lots || []
            });
            itemCounter++;
        });
        renderProductsTable();
    }
}
</script>
@endsection
