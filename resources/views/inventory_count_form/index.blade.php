@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Inventory Count Forms</h1>
            <p class="text-muted mb-0">Manage and view all inventory count forms</p>
        </div>
        <div>
            <a href="{{ route('descriptions.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Inventory
            </a>
        </div>
    </div>

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

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search_title" class="form-label">Search Title</label>
                    <input type="text" class="form-control" id="search_title" name="search_title" 
                           value="{{ request('search_title') }}" placeholder="Enter title keywords...">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="filter_date" class="form-label">Date Range</label>
                    <select class="form-select" id="filter_date" name="filter_date">
                        <option value="">All Dates</option>
                        <option value="today" {{ request('filter_date') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('filter_date') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('filter_date') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('filter_date') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalForms }}</h4>
                            <p class="mb-0">Total Forms</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalItems }}</h4>
                            <p class="mb-0">Total Items</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $thisMonth }}</h4>
                            <p class="mb-0">This Month</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">₱{{ number_format($totalValue, 2) }}</h4>
                            <p class="mb-0">Total Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-peso-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Forms Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inventory Count Forms</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSelected()">
                                <i class="fas fa-print"></i> Print Selected
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="bulkDelete()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
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
                                <input type="checkbox" class="form-check-input row-checkbox" 
                                       value="{{ $form->id }}">
                            </td>
                            <td>
                                <span class="badge bg-primary">#{{ $form->id }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $form->title }}</div>
                                <small class="text-muted">
                                    Created: {{ $form->created_at->format('M d, Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $form->entity->entity_name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill text-success me-2"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $form->fund->account_code ?? 'N/A' }}</div>
                                        <small class="text-muted">
                                            {{ Str::limit($form->fund->account_title ?? 'N/A', 25) }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($form->inventory_date)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($form->inventory_date)->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $form->items_count ?? 0 }} items</span>
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
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('inventory.show', $form->id) }}" 
                                       class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.edit', $form->id) }}" 
                                       class="btn btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="printInventory({{ $form->id }})" title="Print">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteInventory({{ $form->id }})" title="Delete">
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
                    <div>
                        Showing {{ $inventoryForms->firstItem() ?? 0 }} to {{ $inventoryForms->lastItem() ?? 0 }} 
                        of {{ $inventoryForms->total() }} results
                    </div>
                    <div>
                        {{ $inventoryForms->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-5">
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
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
}

.badge {
    font-size: 0.75em;
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
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Update select all when individual checkboxes change
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
});

// Delete inventory form
function deleteInventory(formId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    
    document.getElementById('confirmDelete').onclick = function() {
        // Create form and submit for deletion
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

// Print inventory form
function printInventory(formId) {
    window.open(`/inventory/${formId}/print`, '_blank');
}

// Export to Excel
function exportToExcel() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one inventory form to export.');
        return;
    }
    
    // Create form for export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("inventory.export") }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let formHtml = `<input type="hidden" name="_token" value="${csrfToken}">`;
    
    selectedIds.forEach(id => {
        formHtml += `<input type="hidden" name="selected_ids[]" value="${id}">`;
    });
    
    form.innerHTML = formHtml;
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Print selected forms
function printSelected() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one inventory form to print.');
        return;
    }
    
    selectedIds.forEach(id => {
        window.open(`/inventory/${id}/print`, '_blank');
    });
}

// Bulk delete
function bulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one inventory form to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} inventory form(s)? This action cannot be undone.`)) {
        // Create form for bulk delete
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
        document.body.removeChild(form);
    }
}
</script>
@endsection