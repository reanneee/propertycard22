<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundClusterController;
use App\Http\Controllers\InventoryCountFormController;
use App\Http\Controllers\ReceivedEquipmentController;
use App\Http\Controllers\PropertyCardController;
use App\Http\Controllers\ReceivedEquipmentDescriptionController;
use App\Models\ReceivedEquipmentItem;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('entities', EntityController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('fund_clusters', FundClusterController::class);
    Route::resource('equipment-list', ReceivedEquipmentDescriptionController::class);

    Route::resource('received_equipment', ReceivedEquipmentController::class);
    Route::resource('descriptions', InventoryCountFormController::class);
    Route::get('received_equipment/entity/{entityId}', [ReceivedEquipmentController::class, 'createWithEntity'])
        ->name('received_equipment.create_with_entity');
    Route::get('/received_equipment/{id}/generate-pdf', [ReceivedEquipmentController::class, 'generatePdf'])
        ->name('received_equipment.generate_pdf');
    Route::delete('received_equipment/descriptions/{descriptionId}/items/{itemId}', [ReceivedEquipmentController::class, 'deleteEquipmentItem'])->name('received_equipment.delete_item');
    Route::put('/received_equipment/{received_equipment}', [ReceivedEquipmentController::class, 'update'])->name('received_equipment.update');
    Route::get('/received_equipment/{received_equipment}/edit', [ReceivedEquipmentController::class, 'edit'])->name('received_equipment.edit');


    Route::prefix('property-cards')->name('property_cards.')->group(function () {
        // Main CRUD routes
        Route::get('/', [PropertyCardController::class, 'index'])->name('index');
        Route::get('/create', [PropertyCardController::class, 'create'])->name('create');
        Route::post('/', [PropertyCardController::class, 'store'])->name('store');
        Route::get('/{descriptionId}/show', [PropertyCardController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PropertyCardController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PropertyCardController::class, 'update'])->name('update');
        Route::delete('/{id}', [PropertyCardController::class, 'destroy'])->name('destroy');

        // Print and PDF routes
        Route::get('/{descriptionId}/print', [PropertyCardController::class, 'printView'])->name('print');
        Route::get('/{descriptionId}/pdf', [PropertyCardController::class, 'generatePDF'])->name('pdf');

        // API routes for AJAX calls
        Route::get('/api/{descriptionId}/data', [PropertyCardController::class, 'getPropertyCardData'])->name('api.data');
        Route::get('/api/descriptions', [PropertyCardController::class, 'getUniqueDescriptions'])->name('api.descriptions');
    });

    // Property Cards CRUD Routes
    Route::resource('property_cards', PropertyCardController::class)->parameters([
        'property_cards' => 'propertyCard'
    ]);

    // Additional route for AJAX equipment items loading
    Route::get('property_cards/get-equipment-items', [PropertyCardController::class, 'getEquipmentItems'])
        ->name('property_cards.get_equipment_items');

    // Optional: Property Cards routes with prefix and middleware
    Route::prefix('inventory')->middleware(['auth'])->group(function () {
        Route::resource('property_cards', PropertyCardController::class)->parameters([
            'property_cards' => 'propertyCard'
        ]);

        Route::get('property_cards/get-equipment-items', [PropertyCardController::class, 'getEquipmentItems'])
            ->name('property_cards.get_equipment_items');
    });

    // If you want to add property cards to specific inventory forms
    Route::get('inventory/{inventoryForm}/property_cards/create', [PropertyCardController::class, 'create'])
        ->name('inventory.property_cards.create');

    // Bulk operations (optional)
    Route::post('property_cards/bulk-delete', [PropertyCardController::class, 'bulkDelete'])
        ->name('property_cards.bulk_delete');

    Route::post('property_cards/bulk-update-condition', [PropertyCardController::class, 'bulkUpdateCondition'])
        ->name('property_cards.bulk_update_condition');

    // Inventory routes
    Route::get('/inventory/create', [InventoryCountFormController::class, 'create'])->name('inventory.create');
    Route::post('/inventory/create', [InventoryCountFormController::class, 'createInventory'])->name('inventory.create.post');
    Route::post('/inventory', [InventoryCountFormController::class, 'store'])->name('inventory.store');
    Route::get('/inventory', [InventoryCountFormController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{id}', [InventoryCountFormController::class, 'show'])->name('inventory.show');
  //  Route::get('/inventory/{id}', [InventoryCountFormController::class, 'show'])->name('inventory.show');
    Route::get('inventory/{id}/edit', [InventoryCountFormController::class, 'edit'])->name('inventory.edit');
    Route::get('/inventory/{id}/report', [InventoryCountFormController::class, 'generateReport'])->name('inventory.report');
    //

    Route::post('inventory/bulk-delete', [InventoryCountFormController::class, 'bulkDelete'])->name('inventory.bulk-delete');
    Route::post('inventory/export', [InventoryCountFormController::class, 'export'])->name('inventory.export');
    Route::get('inventory/{id}/print', [InventoryCountFormController::class, 'print'])
        ->name('inventory.print');
    // API routes
    Route::post('/api/generate-property-number', [ReceivedEquipmentDescriptionController::class, 'generatePropertyNumber']);
    Route::post('/api/save-linked-equipment-item', [InventoryCountFormController::class, 'saveLinkedEquipmentItem']);


    //
    Route::resource('inventory-count-form', InventoryCountFormController::class);
    Route::get(
        '/inventory-count-form/{inventoryFormId}/item/{itemId}/details',
        [InventoryCountFormController::class, 'showItemDetails']
    )
        ->name('inventory-count-form.item-details');

    Route::get('/inventory-count-form/{inventoryFormId}/item/{itemId}/edit', [InventoryCountFormController::class, 'editItemDetails'])
        ->name('inventory-count-form.edit-item-details');

    Route::put('/inventory-count-form/{inventoryFormId}/item/{itemId}', [InventoryCountFormController::class, 'updateItemDetails'])
        ->name('inventory-count-form.update-item-details');


    Route::get('/property-cards/by-par-no', [PropertyCardController::class, 'indexByParNo']);

    Route::get('/inventory-count-form/{inventoryFormId}/item/{itemId}/pdf', [InventoryCountFormController::class, 'generatePDF'])
    ->name('inventory-count-form.generate-pdf');

// Alternative route if you want it simpler
Route::get('/property-card/{itemId}/pdf', [InventoryCountFormController::class, 'generatePDF'])
    ->name('property-card.pdf');
});
