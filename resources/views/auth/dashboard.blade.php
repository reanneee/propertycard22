@extends('layouts.app')

@section('content')
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
    <p class="content-subtitle">Property Stock Card Management System</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-number">{{ $totalEntities }}</div>
        <div class="stat-label">Total Entities</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-laptop"></i>
        </div>
        <div class="stat-number">{{ $totalEquipmentItems }}</div>
        <div class="stat-label">Equipment Items</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-number">{{ $totalPropertyCards }}</div>
        <div class="stat-label">Property Cards</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-number">{{ $availableItems }}</div>
        <div class="stat-label">Available Items</div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="stats-grid mt-4">
    <div class="stat-card">
        <div class="stat-icon secondary">
            <i class="fas fa-code-branch"></i>
        </div>
        <div class="stat-number">{{ $activeBranches }}</div>
        <div class="stat-label">Active Branches</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-number">{{ $assignedItems }}</div>
        <div class="stat-label">Assigned Items</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon dark">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-number">{{ $totalReceivedEquipment }}</div>
        <div class="stat-label">Received Equipment</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-number">{{ $totalInventoryForms }}</div>
        <div class="stat-label">Inventory Forms</div>
    </div>
</div>
@endsection