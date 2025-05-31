{{-- resources/views/inventory_count_form/edit_item_details.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory-count-form.index') }}">Inventory Count Forms</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory-count-form.show', $inventoryFormId) }}">
                            Form Details
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory-count-form.item-details', [$inventoryFormId, $itemDetails->item_id]) }}">
                            Item Details
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Item</li>
                </ol>
            </nav>

            <!-- Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('inventory-count-form.update-item-details', [$inventoryFormId, $itemDetails->item_id]) }}"
                method="POST" id="editItemForm">
                @csrf
                @method('PUT')

                <!-- Property Card Header -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Edit Property Card Details
                            </h4>
                            <div class="card-tools">
                                <a href="{{ route('inventory-count-form.item-details', [$inventoryFormId, $itemDetails->item_id]) }}"
                                    class="btn btn-light btn-sm">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entity Information (Read-only Header) -->
                <div class="card mb-4">
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">Entity Name:</label>
                                    <span class="info-value">{{ $itemDetails->entity_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">Property Number:</label>
                                    <span class="info-value property-no">{{ $itemDetails->property_no ?? 'N/A' }}</span>
                                    @if($itemDetails->new_property_no)
                                    <br><small class="text-primary">New: {{ $itemDetails->new_property_no }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Property Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i>Property Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="article" class="form-label required">Property, Plant and Equipment (Article)</label>
                                    <input type="text" name="article" id="article" class="form-control"
                                        value="{{ old('article', $itemDetails->article) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control bg-light" value="{{ $itemDetails->unit ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3"
                                        placeholder="Enter detailed description of the property...">{{ old('description', $itemDetails->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serial_no" class="form-label">Serial Number</label>
                                    <input type="text" name="serial_no" id="serial_no" class="form-control"
                                        value="{{ old('serial_no', $itemDetails->serial_no) }}"
                                        placeholder="Enter serial number if available">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="condition" class="form-label">Current Condition</label>
                                    <select name="condition" id="condition" class="form-select">
                                        <option value="">Select Condition</option>
                                        <option value="Good" {{ old('condition', $itemDetails->condition) == 'Good' ? 'selected' : '' }}>
                                            Good
                                        </option>
                                        <option value="Fair" {{ old('condition', $itemDetails->condition) == 'Fair' ? 'selected' : '' }}>
                                            Fair
                                        </option>
                                        <option value="Poor" {{ old('condition', $itemDetails->condition) == 'Poor' ? 'selected' : '' }}>
                                            Poor
                                        </option>
                                        <option value="New" {{ old('condition', $itemDetails->condition) == 'New' ? 'selected' : '' }}>
                                            New
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction & Quantity Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exchange-alt me-2"></i>Transaction & Quantity Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_acquired" class="form-label">Date Acquired</label>
                                    <input type="date" name="date_acquired" id="date_acquired" class="form-control"
                                        value="{{ old('date_acquired', $itemDetails->date_acquired) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">PAR Number</label>
                                    <input type="text" class="form-control bg-light" value="{{ $itemDetails->par_no ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (₱)</label>
                                    <input type="number" name="amount" id="amount" class="form-control"
                                        step="0.01" min="0" value="{{ old('amount', $itemDetails->amount) }}"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Original Quantity</label>
                                    <input type="text" class="form-control bg-light" value="{{ $itemDetails->original_quantity ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="physical_quantity" class="form-label">Physical Quantity</label>
                                    <input type="number" name="physical_quantity" id="physical_quantity" class="form-control"
                                        min="0" value="{{ old('physical_quantity', $itemDetails->physical_quantity) }}"
                                        placeholder="Enter current physical count">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="locations_id" class="form-label">Current Location</label>
                                    <select name="locations_id" id="locations_id" class="form-select">
                                        <option value="">Select Location</option>
                                        @foreach($locationOptions as $location)
                                        <option value="{{ $location['id'] }}"
                                            {{ old('locations_id', $itemDetails->locations_id) == $location['id'] ? 'selected' : '' }}>
                                            {{ $location['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer & Disposal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-share-alt me-2"></i>Transfer & Disposal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_transfer_disposal" class="form-label">Issue/Transfer/Disposal</label>
                                    <select name="issue_transfer_disposal" id="issue_transfer_disposal" class="form-control">
                                        <option value="">Select an option...</option>
                                        <option value="Issue" {{ old('issue_transfer_disposal', $itemDetails->issue_transfer_disposal) == 'Issue' ? 'selected' : '' }}>Issue</option>
                                        <option value="Transferred" {{ old('issue_transfer_disposal', $itemDetails->issue_transfer_disposal) == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                                        <option value="Disposed" {{ old('issue_transfer_disposal', $itemDetails->issue_transfer_disposal) == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="received_by_name" class="form-label">Received By (Office/Officer)</label>
                                    <input type="text" name="received_by_name" id="received_by_name" class="form-control"
                                        value="{{ old('received_by_name', $itemDetails->received_by_name) }}"
                                        placeholder="Enter name of receiving officer/office">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Leave empty if property card has not been issued/transferred
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information & Remarks -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="4"
                                placeholder="Enter any additional notes, observations, or special instructions...">{{ old('remarks', $itemDetails->remarks) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('inventory-count-form.item-details', [$inventoryFormId, $itemDetails->item_id]) }}"
                                class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancel Changes
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="saveButton">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Enhanced Form Styling */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .card-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        border-bottom: 2px solid #004085;
    }

    .card-title {
        font-weight: 600;
        color: #495057;
    }

    .card-header.bg-primary .card-title {
        color: white !important;
    }

    .card-body {
        padding: 2rem;
    }

    .card-footer {
        border-top: 2px solid #dee2e6;
        padding: 1.5rem 2rem;
    }

    /* Info Groups for Read-only Fields */
    .info-group {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 140px;
        margin-right: 1rem;
        margin-bottom: 0;
    }

    .info-value {
        color: #212529;
        font-weight: 500;
    }

    .property-no {
        font-family: 'Courier New', monospace;
        font-size: 1.1em;
        color: #007bff;
    }

    /* Form Controls */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
    }

    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15);
        transform: translateY(-1px);
    }

    .form-control.bg-light {
        background-color: #f8f9fa !important;
        border-color: #dee2e6;
        color: #6c757d;
    }

    .form-control::placeholder {
        color: #adb5bd;
        font-style: italic;
    }

    /* Buttons */
    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        color: white;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        color: white;
    }

    .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #007bff;
    }

    .btn-light:hover {
        background: white;
        color: #0056b3;
    }

    /* Alert Styling */
    .alert {
        border: none;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    /* Small Text Styling */
    .text-muted {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .text-muted .fas {
        margin-right: 0.25rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            padding: 1rem 1.5rem;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .btn-lg {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .info-group {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-label {
            min-width: auto;
            margin-bottom: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .card-header {
            padding: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-footer {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
        }
    }

    /* Loading State */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Animation for form interactions */
    .card {
        animation: slideInUp 0.3s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission handling
        const form = document.getElementById('editItemForm');
        const saveButton = document.getElementById('saveButton');

        form.addEventListener('submit', function(e) {
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Changes...';
            saveButton.disabled = true;
        });

        // Auto-resize textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            // Set initial height
            adjustTextareaHeight(textarea);

            textarea.addEventListener('input', function() {
                adjustTextareaHeight(this);
            });
        });

        function adjustTextareaHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        // Form validation feedback
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });

            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    if (this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                }
            });
        });

        // Number input formatting
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('blur', function() {
                if (this.value) {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                }
            });
        }

        // Condition badge preview
        const conditionSelect = document.getElementById('condition');
        if (conditionSelect) {
            conditionSelect.addEventListener('change', function() {
                const value = this.value;
                const colors = {
                    'Good': 'success',
                    'Fair': 'warning',
                    'Poor': 'danger'
                };

                // Remove existing classes
                this.classList.remove('border-success', 'border-warning', 'border-danger');

                // Add appropriate border color
                if (colors[value]) {
                    this.classList.add(`border-${colors[value]}`);
                }
            });

            // Set initial state
            conditionSelect.dispatchEvent(new Event('change'));
        }

        // Smooth scroll to errors
        const errorAlert = document.querySelector('.alert-danger');
        if (errorAlert) {
            errorAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });
</script>
@endpush