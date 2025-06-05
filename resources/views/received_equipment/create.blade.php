@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold">Create Received Equipment</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Entity Information</h5>
            <div class="row">
                <div class="col-md-6"><strong>Branch:</strong> {{ $entity->branch->branch_name }}</div>
                <div class="col-md-6"><strong>Fund Cluster:</strong> {{ $entity->fundCluster->name }}</div>
                <div class="col-md-6"><strong>Entity Name:</strong> {{ $entity->entity_name }}</div>
            </div>
        </div>
    </div>

    <!-- Form Start -->
    <form action="{{ route('received_equipment.store') }}" method="POST" id="equipmentForm">
        @csrf
        <input type="hidden" name="entity_id" value="{{ $entity->entity_id }}">

        <div class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label for="par_no" class="form-label fw-semibold">PAR Number</label>
                <input type="text" name="par_no" class="form-control" value="{{ $par_no }}" readonly>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Equipment Entry</h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="quantity" class="form-control" min="1">
                        <div id="quantity-error" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <input type="text" id="unit" class="form-control">
                        <div id="unit-error" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <textarea id="description" class="form-control" rows="1" style="resize:none; overflow:hidden;" oninput="autoResizeTextarea(this)"></textarea>
                        <div id="description-error" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date Acquired</label>
                        <input type="date" id="date_acquired" class="form-control">
                        <div id="date_acquired-error" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Amount</label>
                        <input type="number" id="amount" class="form-control" step="0.01" min="0">
                        <div id="amount-error" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-success" onclick="generateRows()">Generate Rows</button>
                    <button type="button" class="btn btn-outline-primary" onclick="addManualRow()">Add One Row</button>
                </div>
            </div>
        </div>
        
        @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some errors with your input:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Equipment Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Generated Equipment</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="equipmentTable">
                        <thead class="table-light">
                            <tr>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Property No</th>
                                <th>Serial No</th>
                                <th>Date Acquired</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Dynamic rows will be inserted here --}}
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">Total</td>
                                <td><input type="text" id="totalAmount" class="form-control" readonly></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Receiving Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Receiving & Verification</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Received By (Name)</label>
                        <input type="text" name="received_by_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Received By (Designation)</label>
                        <input type="text" name="received_by_designation" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Verified By (Name)</label>
                        <input type="text" name="verified_by_name" class="form-control" value="Ernobille Placo" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Verified By (Designation)</label>
                        <input type="text" name="verified_by_designation" class="form-control" value="Supply Officer" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Receipt</label>
                    <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg" onclick="validateBeforeSubmit(event)">Save Equipment</button>
        </div>
    </form>
</div>

<script>
    let equipmentIndex = 0;

    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Initialize auto resize for any existing textareas on page load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('textarea.auto-resize-textarea').forEach(autoResizeTextarea);
    });

    // Function to clear validation errors
    function clearValidationErrors() {
        const errorElements = [
            'quantity-error',
            'unit-error', 
            'description-error',
            'date_acquired-error',
            'amount-error'
        ];
        
        errorElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';
                element.textContent = '';
            }
        });
        
        // Remove error styling from input fields
        document.querySelectorAll('#quantity, #unit, #description, #date_acquired, #amount').forEach(input => {
            input.classList.remove('is-invalid');
        });
    }

    // Function to show validation error
    function showValidationError(fieldId, message) {
        const errorElement = document.getElementById(fieldId + '-error');
        const inputElement = document.getElementById(fieldId);
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
        
        if (inputElement) {
            inputElement.classList.add('is-invalid');
        }
    }

    // Function to validate form fields before generating rows
    function validateFormFields() {
        clearValidationErrors();
        
        let isValid = true;
        const qty = document.getElementById('quantity').value;
        const unit = document.getElementById('unit').value.trim();
        const desc = document.getElementById('description').value.trim();
        const date = document.getElementById('date_acquired').value;
        const amount = document.getElementById('amount').value;

        // Validate quantity
        if (!qty) {
            showValidationError('quantity', 'Quantity is required.');
            isValid = false;
        } else if (parseInt(qty) === -1) {
            showValidationError('quantity', 'Quantity cannot be -1. Please enter a valid positive number.');
            isValid = false;
        } else if (parseInt(qty) < 1) {
            showValidationError('quantity', 'Quantity must be at least 1.');
            isValid = false;
        }

        // Validate unit
        if (!unit) {
            showValidationError('unit', 'Unit is required.');
            isValid = false;
        }

        // Validate description
        if (!desc) {
            showValidationError('description', 'Description is required.');
            isValid = false;
        }

        // Validate date acquired
        if (!date) {
            showValidationError('date_acquired', 'Date acquired is required.');
            isValid = false;
        }

        // Validate amount
        if (!amount) {
            showValidationError('amount', 'Amount is required.');
            isValid = false;
        } else if (parseFloat(amount) < 0) {
            showValidationError('amount', 'Amount must be at least 0.');
            isValid = false;
        }

        return isValid;
    }

    function generateRows() {
        // Validate form fields first
        if (!validateFormFields()) {
            return;
        }

        const qty = parseInt(document.getElementById('quantity').value);
        const unit = document.getElementById('unit').value.trim();
        const desc = document.getElementById('description').value.trim();
        const date = document.getElementById('date_acquired').value;
        const amount = parseFloat(document.getElementById('amount').value);

        const tbody = document.querySelector('#equipmentTable tbody');

        for (let i = 0; i < qty; i++) {
            const tr = document.createElement('tr');
            tr.dataset.group = equipmentIndex;

            if (i === 0) {
                tr.innerHTML += `
                    <td rowspan="${qty}">
                        <input type="number" name="equipments[${equipmentIndex}][quantity]" class="form-control" value="${qty}" readonly>
                    </td>
                    <td rowspan="${qty}">
                        <input type="text" name="equipments[${equipmentIndex}][unit]" class="form-control" value="${unit}" readonly>
                    </td>
                    <td rowspan="${qty}">
                        <textarea 
                            name="equipments[${equipmentIndex}][description]" 
                            class="form-control auto-resize-textarea" 
                            rows="1" 
                            style="resize:none; overflow:hidden;" 
                            oninput="autoResizeTextarea(this)"
                            readonly
                        >${desc}</textarea>
                    </td>
                `;
            }

            tr.innerHTML += `
                <td>
                    <input type="text" name="equipments[${equipmentIndex}][items][${i}][property_no]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="equipments[${equipmentIndex}][items][${i}][serial_no]" class="form-control">
                </td>
                <td>
                    <input type="date" name="equipments[${equipmentIndex}][items][${i}][date_acquired]" class="form-control" value="${date}" required>
                </td>
                <td>
                    <input type="number" name="equipments[${equipmentIndex}][items][${i}][amount]" class="form-control amount-field" value="${amount}" step="0.01" oninput="calculateTotal()" required>
                </td>
            `;

            if (i === 0) {
                tr.innerHTML += `
                    <td rowspan="${qty}">
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRowGroup(${equipmentIndex})">Delete</button>
                    </td>
                `;
            }

            tbody.appendChild(tr);
        }

        equipmentIndex++;
        calculateTotal();
        
        // Clear form fields
        document.getElementById('quantity').value = '';
        document.getElementById('unit').value = '';
        document.getElementById('description').value = '';
        document.getElementById('date_acquired').value = '';
        document.getElementById('amount').value = '';

        // Clear any validation errors after successful generation
        clearValidationErrors();

        // Auto-resize newly created textareas
        document.querySelectorAll('.auto-resize-textarea').forEach(autoResizeTextarea);
    }

    function addManualRow() {
        const tbody = document.querySelector('#equipmentTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="number" name="equipments[${equipmentIndex}][quantity]" class="form-control" value="1" required></td>
            <td><input type="text" name="equipments[${equipmentIndex}][unit]" class="form-control" required></td>
            <td><textarea name="equipments[${equipmentIndex}][description]" class="form-control auto-resize-textarea" rows="1" style="resize:none; overflow:hidden;" oninput="autoResizeTextarea(this)" required></textarea></td>
            <td><input type="text" name="equipments[${equipmentIndex}][items][0][property_no]" class="form-control" required></td>
            <td><input type="text" name="equipments[${equipmentIndex}][items][0][serial_no]" class="form-control"></td>
            <td><input type="date" name="equipments[${equipmentIndex}][items][0][date_acquired]" class="form-control" required></td>
            <td><input type="number" name="equipments[${equipmentIndex}][items][0][amount]" class="form-control amount-field" step="0.01" oninput="calculateTotal()" required></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calculateTotal()">Delete</button></td>
        `;
        tbody.appendChild(tr);
        equipmentIndex++;

        // Auto-resize textarea in new manual row
        document.querySelectorAll('.auto-resize-textarea').forEach(autoResizeTextarea);
    }

    function deleteRowGroup(groupId) {
        document.querySelectorAll(`tr[data-group="${groupId}"]`).forEach(row => row.remove());
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-field').forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });
        document.getElementById('totalAmount').value = total.toFixed(2);
    }
    
    function validateBeforeSubmit(event) {
        const tbody = document.querySelector('#equipmentTable tbody');
        if (tbody.children.length === 0) {
            event.preventDefault();
            alert('Please add at least one equipment item before saving.');
            return false;
        }
        
        const form = document.getElementById('equipmentForm');
        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return false;
        }
        
        return true;
    }
</script>

<style>
    .is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>
@endsection