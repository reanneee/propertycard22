<!-- inventory_count_form.items -->
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Inventory Items</h1>
            <p class="text-muted mb-0">View all individual inventory items</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('inventory.index', ['view_mode' => 'forms'] + request()->except('view_mode')) }}" 
               class="btn btn-outline-primary {{ $viewMode === 'forms' ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> Forms View
            </a>
            <a href="{{ route('inventory.index', ['view_mode' => 'items'] + request()->except('view_mode')) }}" 
               class="btn btn-outline-primary {{ $viewMode === 'items' ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Items View
            </a>
        </div>
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
                <input type="hidden" name="view_mode" value="items">
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
                    <a href="{{ route('inventory.index', ['view_mode' => 'items']) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Items Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Inventory Items</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-success" onclick="exportItems()" title="Export Items">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="printItems()" title="Print Items">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </div>
        
       @if($inventoryItems->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="5%">Item ID</th>
                    <th width="15%">Item Description</th>
                    <th width="15%">Form Title</th>
                    <th width="12%">Entity</th>
                    <th width="10%">Fund</th>
                    <th width="8%">Qty Physical</th>
                    <th width="8%">Unit Cost</th>
                    <th width="10%">Total Value</th>
                    <th width="10%">Date</th>
                    <th width="7%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryItems as $item)
                <tr>
                    <td>
                        <span class="badge bg-secondary">#{{ $item->id }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">
                            {{ $item->receivedEquipmentItem->description->description ?? 'N/A' }}
                        </div>
                        <small class="text-muted">
                            @if($item->receivedEquipmentItem->serial_no)
                                S/N: {{ $item->receivedEquipmentItem->serial_no }}
                            @endif
                            @if($item->receivedEquipmentItem->property_no)
                                | P/N: {{ $item->receivedEquipmentItem->property_no }}
                            @endif
                        </small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->inventoryCountForm->title ?? 'N/A' }}</div>
                        <small class="text-muted">Form #{{ $item->inventory_count_form_id }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building text-primary me-2"></i>
                            <span class="fw-semibold">{{ $item->inventoryCountForm->entity->entity_name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-money-bill text-success me-2"></i>
                            <div>
                                <div class="fw-semibold">{{ $item->inventoryCountForm->fund->account_code ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $item->qty_physical ?? 0 }}</span>
                    </td>
                    <td>
                        <span class="fw-semibold">₱{{ number_format($item->receivedEquipmentItem->amount ?? 0, 2) }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-success">
                            ₱{{ number_format(($item->qty_physical ?? 0) * ($item->receivedEquipmentItem->amount ?? 0), 2) }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($item->inventoryCountForm->inventory_date ?? now())->format('M d, Y') }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($item->inventoryCountForm->inventory_date ?? now())->diffForHumans() }}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('inventory.show', $item->inventory_count_form_id) }}" class="btn btn-outline-primary" title="View Form">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-outline-info" onclick="showItemDetails({{ $item->id }})" title="Details">
                                <i class="fas fa-info-circle"></i>
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
                Showing {{ $inventoryItems->firstItem() ?? 0 }} to {{ $inventoryItems->lastItem() ?? 0 }} 
                of {{ $inventoryItems->total() }} results
            </small>
            {{ $inventoryItems->appends(request()->query())->links() }}
        </div>
    </div>
@else
    <div class="card-body text-center py-5">
        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No Inventory Items Found</h5>
        <p class="text-muted">No items match your current filter criteria.</p>
        <a href="{{ route('inventory.index', ['view_mode' => 'items']) }}" class="btn btn-primary">
            <i class="fas fa-times"></i> Clear Filters
        </a>
    </div>
@endif

<!-- Item Details Modal -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showItemDetails(itemId) {
    // Load item details via AJAX
    fetch(`/inventory/item/${itemId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('itemDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Item Information</h6>
                        <p><strong>Description:</strong> ${data.description || 'N/A'}</p>
                        <p><strong>Serial No:</strong> ${data.serial_no || 'N/A'}</p>
                        <p><strong>Property No:</strong> ${data.property_no || 'N/A'}</p>
                        <p><strong>Amount:</strong> ₱${data.amount || '0.00'}</p>
                        <p><strong>Date Acquired:</strong> ${data.date_acquired || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Inventory Information</h6>
                        <p><strong>Physical Quantity:</strong> ${data.qty_physical || '0'}</p>
                        <p><strong>Total Value:</strong> ₱${data.total_value || '0.00'}</p>
                        <p><strong>Form Title:</strong> ${data.form_title || 'N/A'}</p>
                        <p><strong>Entity:</strong> ${data.entity_name || 'N/A'}</p>
                        <p><strong>Fund:</strong> ${data.fund_code || 'N/A'}</p>
                    </div>
                </div>
            `;
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('itemDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading item details:', error);
            document.getElementById('itemDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading item details. Please try again.
                </div>
            `;
            new bootstrap.Modal(document.getElementById('itemDetailsModal')).show();
        });
}
</script>

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

.btn-group .btn.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    highlightActiveFilter();
});

function filterBy(type) {
    const currentUrl = new URL(window.location.href);
    
    // Clear all filter params first
    currentUrl.search = '';
    
    const today = new Date();
    
    switch(type) {
        case 'items':
            // Navigate to items view
            currentUrl.searchParams.set('view_mode', 'items');
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
        case 'all':
        default:
            // Stay in forms view (no additional params needed)
            break;
    }
    
    window.location.href = currentUrl.toString();
}

// For Items View (inventory_count_form.items)
function filterBy(type) {
    const currentUrl = new URL(window.location.href);
    
    if (type === 'all') {
        // Navigate to forms view
        currentUrl.searchParams.delete('view_mode');
        // Clear all other filters
        currentUrl.search = '';
    } else {
        // Stay in items view but apply filters
        currentUrl.searchParams.set('view_mode', 'items');
        
        // Remove existing filter params
        currentUrl.searchParams.delete('has_items');
        currentUrl.searchParams.delete('date_from');
        currentUrl.searchParams.delete('date_to');
        currentUrl.searchParams.delete('min_value');
        
        const today = new Date();
        
        switch(type) {
            case 'items':
                // Just keep view_mode=items, no additional filter needed
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
        }
    }
    
    window.location.href = currentUrl.toString();
}

// Enhanced version with better visual feedback
function filterBy(type) {
    const currentUrl = new URL(window.location.href);
    const currentViewMode = currentUrl.searchParams.get('view_mode');
    
    // Add loading state to clicked card
    const clickedCard = document.querySelector(`.stat-card[onclick="filterBy('${type}')"]`);
    if (clickedCard) {
        clickedCard.style.opacity = '0.7';
        clickedCard.style.pointerEvents = 'none';
    }
    
    if (type === 'all') {
        // Total Forms card - always go to forms view
        currentUrl.searchParams.delete('view_mode');
        currentUrl.search = ''; // Clear all filters
    } else if (type === 'items') {
        // Total Items card - always go to items view
        currentUrl.searchParams.set('view_mode', 'items');
        // Remove other filters but keep view_mode
        currentUrl.searchParams.delete('has_items');
        currentUrl.searchParams.delete('date_from');
        currentUrl.searchParams.delete('date_to');
        currentUrl.searchParams.delete('min_value');
    } else {
        // Other cards - apply filter in current view or default to forms view
        if (currentViewMode === 'items') {
            currentUrl.searchParams.set('view_mode', 'items');
        }
        
        // Remove existing filter params
        currentUrl.searchParams.delete('has_items');
        currentUrl.searchParams.delete('date_from');
        currentUrl.searchParams.delete('date_to');
        currentUrl.searchParams.delete('min_value');
        
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
        }
    }
    
    window.location.href = currentUrl.toString();
}

// Updated highlightActiveFilter function for both views
function highlightActiveFilter() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentViewMode = urlParams.get('view_mode');
    
    document.querySelectorAll('.stat-card').forEach(card => {
        card.classList.remove('active-filter');
    });
    
    // Highlight based on current view and filters
    if (currentViewMode === 'items') {
        if (urlParams.has('date_from') && urlParams.has('date_to')) {
            const today = new Date();
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            
            if (urlParams.get('date_from') === startOfMonth && urlParams.get('date_to') === endOfMonth) {
                document.querySelector('.stat-card[onclick="filterBy(\'month\')"]')?.classList.add('active-filter');
            }
        } else if (urlParams.has('min_value')) {
            document.querySelector('.stat-card[onclick="filterBy(\'value\')"]')?.classList.add('active-filter');
        } else {
            // Highlight "Total Items" card when in items view without specific filters
            document.querySelector('.stat-card[onclick="filterBy(\'items\')"]')?.classList.add('active-filter');
        }
    } else {
        // Forms view
        if (urlParams.has('date_from') && urlParams.has('date_to')) {
            const today = new Date();
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            
            if (urlParams.get('date_from') === startOfMonth && urlParams.get('date_to') === endOfMonth) {
                document.querySelector('.stat-card[onclick="filterBy(\'month\')"]')?.classList.add('active-filter');
            }
        } else if (urlParams.has('min_value')) {
            document.querySelector('.stat-card[onclick="filterBy(\'value\')"]')?.classList.add('active-filter');
        } else {
            // Highlight "Total Forms" card when in forms view without specific filters
            document.querySelector('.stat-card[onclick="filterBy(\'all\')"]')?.classList.add('active-filter');
        }
    }
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

function showItemDetails(itemId) {
    // You can implement this to show detailed item information
    // For now, it's a placeholder
    const modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
    document.getElementById('itemDetailsContent').innerHTML = '<p>Loading item details...</p>';
    modal.show();
    
    // You could make an AJAX call here to fetch item details
    // fetch(`/inventory/item/${itemId}/details`)
    //     .then(response => response.html())
    //     .then(html => {
    //         document.getElementById('itemDetailsContent').innerHTML = html;
    //     });
}

function exportItems() {
    // Add export functionality
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `{{ route('inventory.index') }}?${params.toString()}`;
}

function printItems() {
    window.print();
}
</script>
@endsection