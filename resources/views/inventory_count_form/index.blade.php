
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Inventory Count Forms</h1>
            <p class="text-muted mb-0">Manage and view all inventory count forms</p>
        </div>
        <a href="{{ route('descriptions.index') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Inventory
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white stat-card" onclick="filterBy('all')" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $totalForms }}</h4>
                            <p class="mb-0">Total Forms</p>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white stat-card" onclick="filterBy('items')" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $totalItems }}</h4>
                            <p class="mb-0">Total Items</p>
                        </div>
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white stat-card" onclick="filterBy('month')" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $thisMonth }}</h4>
                            <p class="mb-0">This Month</p>
                        </div>
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white stat-card" onclick="filterBy('value')" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">₱{{ number_format($totalValue, 2) }}</h4>
                            <p class="mb-0">Total Value</p>
                        </div>
                        <i class="fas fa-peso-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search_title" class="form-label">Search Title</label>
                    <input type="text" class="form-control" id="search_title" name="search_title" 
                           value="{{ request('search_title') }}" placeholder="Enter title keywords...">
                </div>
                <div class="col-md-4">
                    <label for="filter_entity" class="form-label">Entity</label>
                    <select class="form-select" id="filter_entity" name="filter_entity">
                        <option value="">All Entities</option>
                        @foreach($entities as $entity)
                        <option value="{{ $entity->entity_id }}" 
                                {{ request('filter_entity') == $entity->entity_id ? 'selected' : '' }}>
                            {{ $entity->entity_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_fund" class="form-label">Fund/Account</label>
                    <select class="form-select" id="filter_fund" name="filter_fund">
                        <option value="">All Funds</option>
                        @foreach($funds as $fund)
                        <option value="{{ $fund->id }}" 
                                {{ request('filter_fund') == $fund->id ? 'selected' : '' }}>
                            {{ $fund->account_code }} - {{ Str::limit($fund->account_title, 30) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date Range</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ request('date_from') }}" placeholder="From">
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ request('date_to') }}" placeholder="To">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quick Filters</label>
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="setDateRange('today')">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="setDateRange('week')">Week</button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="setDateRange('month')">Month</button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="setDateRange('year')">Year</button>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Forms Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table"></i> Inventory Forms</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-success" onclick="printSelected()" title="Print Selected">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()" title="Delete Selected">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        @if($inventoryForms->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="3%">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th width="5%">ID</th>
                        <th width="25%">Title</th>
                        <th width="15%">Entity</th>
                        <th width="15%">Fund/Account</th>
                        <th width="10%">Date</th>
                        <th width="8%">Items</th>
                        <th width="10%">Prepared By</th>
                        <th width="9%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventoryForms as $form)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $form->id }}">
                        </td>
                        <td>
                            <span class="badge bg-primary">#{{ $form->id }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $form->title }}</div>
                            <small class="text-muted">{{ $form->created_at->format('M d, Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-2"></i>
                                <span class="fw-semibold">{{ $form->entity->entity_name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                <div>
                                    <div class="fw-semibold">{{ $form->fund->account_code ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ Str::limit($form->fund->account_title ?? 'N/A', 25) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($form->inventory_date)->format('M d, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($form->inventory_date)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $form->items_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($form->prepared_by_name)
                            <div class="fw-semibold">{{ $form->prepared_by_name }}</div>
                            <small class="text-muted">{{ $form->prepared_by_position }}</small>
                            @else
                            <span class="text-muted">Not specified</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventory.show', $form->id) }}" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $form->id) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-success" onclick="printInventory({{ $form->id }})" title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteInventory({{ $form->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $inventoryForms->firstItem() ?? 0 }} to {{ $inventoryForms->lastItem() ?? 0 }} 
                    of {{ $inventoryForms->total() }} results
                </small>
                {{ $inventoryForms->appends(request()->query())->links() }}
            </div>
        </div>
        @else
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Inventory Forms Found</h5>
            <p class="text-muted">Create your first inventory count form to get started.</p>
            <a href="{{ route('descriptions.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Inventory Form
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this inventory form? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will also delete all associated inventory items.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.active-filter {
    border: 2px solid #fff !important;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCheckboxes();
    highlightActiveFilter();
});

function initializeCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(rowCheckboxes).every(cb => !cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            }
        });
    });
}

function highlightActiveFilter() {
    const urlParams = new URLSearchParams(window.location.search);
    
    document.querySelectorAll('.stat-card').forEach(card => {
        card.classList.remove('active-filter');
    });
    
    if (urlParams.has('has_items')) {
        document.querySelector('.stat-card[onclick="filterBy(\'items\')"]')?.classList.add('active-filter');
    } else if (urlParams.has('date_from') && urlParams.has('date_to')) {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
        
        if (urlParams.get('date_from') === startOfMonth && urlParams.get('date_to') === endOfMonth) {
            document.querySelector('.stat-card[onclick="filterBy(\'month\')"]')?.classList.add('active-filter');
        }
    } else if (urlParams.has('min_value')) {
        document.querySelector('.stat-card[onclick="filterBy(\'value\')"]')?.classList.add('active-filter');
    } else if (!urlParams.toString()) {
        document.querySelector('.stat-card[onclick="filterBy(\'all\')"]')?.classList.add('active-filter');
    }
}

function filterBy(type) {
    const currentUrl = new URL(window.location.href);
    
    // Check if we should navigate to items view or stay in forms view
    if (type === 'items') {
        // Navigate to items view with current filters
        currentUrl.searchParams.set('view_mode', 'items');
        currentUrl.searchParams.set('has_items', '1');
    } else {
        // Clear all filter params first
        currentUrl.search = '';
        
        const today = new Date();
        
        switch(type) {
            case 'month':
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                currentUrl.searchParams.set('date_from', startOfMonth.toISOString().split('T')[0]);
                currentUrl.searchParams.set('date_to', endOfMonth.toISOString().split('T')[0]);
                break;
            case 'value':
                currentUrl.searchParams.set('min_value', '10000');
                break;
            case 'all':
            default:
                // No additional params needed for 'all'
                break;
        }
    }
    
    window.location.href = currentUrl.toString();
}

// New function specifically for navigating to items view with filters
function viewItemsWithFilter(filterType) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('view_mode', 'items');
    
    // Clear existing filters
    currentUrl.searchParams.delete('has_items');
    currentUrl.searchParams.delete('date_from');
    currentUrl.searchParams.delete('date_to');
    currentUrl.searchParams.delete('min_value');
    
    const today = new Date();
    
    switch(filterType) {
        case 'items':
            currentUrl.searchParams.set('has_items', '1');
            break;
        case 'month':
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            currentUrl.searchParams.set('date_from', startOfMonth.toISOString().split('T')[0]);
            currentUrl.searchParams.set('date_to', endOfMonth.toISOString().split('T')[0]);
            break;
        case 'value':
            currentUrl.searchParams.set('min_value', '10000');
            break;
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            currentUrl.searchParams.set('date_from', todayStr);
            currentUrl.searchParams.set('date_to', todayStr);
            break;
    }
    
    window.location.href = currentUrl.toString();
}

// Enhanced function to handle both form filtering and item navigation
function handleCardClick(cardType, event) {
    // Check if user wants to see items (you can add a modifier key check)
    const shouldViewItems = event.ctrlKey || event.metaKey || event.shiftKey;
    
    if (shouldViewItems) {
        viewItemsWithFilter(cardType);
    } else {
        filterBy(cardType);
    }
}

// Add event listeners for enhanced card interaction
document.addEventListener('DOMContentLoaded', function() {
    initializeCheckboxes();
    highlightActiveFilter();
    setupEnhancedCardInteraction();
});

function setupEnhancedCardInteraction() {
    // Add tooltips to stat cards to show navigation options
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.setAttribute('title', 'Click to filter forms, Ctrl+Click to view items');
        
        // Add double-click to view items
        card.addEventListener('dblclick', function(e) {
            const onclickAttr = this.getAttribute('onclick');
            const filterType = onclickAttr.match(/filterBy\('([^']+)'\)/)?.[1];
            if (filterType) {
                viewItemsWithFilter(filterType);
            }
        });
    });
}

function setDateRange(range) {
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo = document.querySelector('input[name="date_to"]');
    const today = new Date();
    
    switch(range) {
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            dateFrom.value = todayStr;
            dateTo.value = todayStr;
            break;
        case 'week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            dateFrom.value = startOfWeek.toISOString().split('T')[0];
            dateTo.value = endOfWeek.toISOString().split('T')[0];
            break;
        case 'month':
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            dateFrom.value = startOfMonth.toISOString().split('T')[0];
            dateTo.value = endOfMonth.toISOString().split('T')[0];
            break;
        case 'year':
            const startOfYear = new Date(today.getFullYear(), 0, 1);
            const endOfYear = new Date(today.getFullYear(), 11, 31);
            dateFrom.value = startOfYear.toISOString().split('T')[0];
            dateTo.value = endOfYear.toISOString().split('T')[0];
            break;
    }
}

function deleteInventory(formId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    
    document.getElementById('confirmDelete').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/inventory/${formId}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    };
}

function printInventory(formId) {
    window.open(`/inventory/${formId}/print`, '_blank');
}

function printSelected() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one inventory form to print.');
        return;
    }
    
    selectedIds.forEach(id => {
        window.open(`/inventory/${id}/print`, '_blank');
    });
}

function bulkDelete() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one inventory form to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} inventory form(s)? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("inventory.bulk-delete") }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let formHtml = `<input type="hidden" name="_token" value="${csrfToken}">`;
        
        selectedIds.forEach(id => {
            formHtml += `<input type="hidden" name="selected_ids[]" value="${id}">`;
        });
        
        form.innerHTML = formHtml;
        document.body.appendChild(form);
        form.submit();
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
}
</script>
@endsection