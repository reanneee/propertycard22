@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Stock Card Management System - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #2c3e50;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .content-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #34495e 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 8px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .content-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .content-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: var(--accent-color);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.95rem;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Color variants */
        .primary { 
            background: linear-gradient(135deg, #3498db, #2980b9); 
            --accent-color: #3498db;
        }
        .success { 
            background: linear-gradient(135deg, #27ae60, #229954); 
            --accent-color: #27ae60;
        }
        .warning { 
            background: linear-gradient(135deg, #f39c12, #e67e22); 
            --accent-color: #f39c12;
        }
        .info { 
            background: linear-gradient(135deg, #17a2b8, #138496); 
            --accent-color: #17a2b8;
        }
        .secondary { 
            background: linear-gradient(135deg, #6c757d, #5a6268); 
            --accent-color: #6c757d;
        }
        .danger { 
            background: linear-gradient(135deg, #e74c3c, #c0392b); 
            --accent-color: #e74c3c;
        }
        .dark { 
            background: linear-gradient(135deg, #343a40, #23272b); 
            --accent-color: #343a40;
        }
        .purple { 
            background: linear-gradient(135deg, #8e44ad, #7d3c98); 
            --accent-color: #8e44ad;
        }

        .stat-card.primary::before { background: #3498db; }
        .stat-card.success::before { background: #27ae60; }
        .stat-card.warning::before { background: #f39c12; }
        .stat-card.info::before { background: #17a2b8; }
        .stat-card.secondary::before { background: #6c757d; }
        .stat-card.danger::before { background: #e74c3c; }
        .stat-card.dark::before { background: #343a40; }
        .stat-card.purple::before { background: #8e44ad; }

        /* Chart Container Styles */
        .charts-section {
            margin-top: 50px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e9ecef;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .chart-wrapper {
            position: relative;
            height: 350px;
        }

        .chart-wrapper.pie {
            height: 300px;
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, #e9ecef, #dee2e6, #e9ecef);
            margin: 40px 0;
            border-radius: 1px;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .full-width-chart {
            grid-column: 1 / -1;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .metric-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .metric-label, .stat-member {
            font-size: 0.9rem;
            color: white;
            margin-top: 5px;
        }

        .chart-container, 
    .chart-container h3, 
    .metrics-grid .metric-item, 
    .metric-value, 
    .metric-label {
        color: black;
    }

    .stat-number, .content-subtitle {
    color: white !important;
}


        @media (max-width: 1024px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }

            .content-header {
                padding: 30px 20px;
                margin-bottom: 30px;
            }

            .content-title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stat-card {
                padding: 25px;
            }

            .chart-container {
                padding: 20px;
            }

            .chart-wrapper {
                height: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content-header">
            <h1 class="content-title">Dashboard</h1>
            <p class="content-subtitle">Property Stock Card Management System</p>
        </div>

        <!-- Primary Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($totalEntities) }}</div>
                    <div class="stat-label">Total Entities</div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($totalEquipmentItems) }}</div>
                    <div class="stat-label">Equipment Items</div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($totalPropertyCards) }}</div>
                    <div class="stat-label">Property Cards</div>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($availableItems) }}</div>
                    <div class="stat-label">Available Items</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <h2 class="section-title">Analytics Overview</h2>
            
            <div class="chart-grid">
                <!-- Line Chart for Equipment Tracking -->
                <div class="chart-container">
                    <h3 class="chart-title">Equipment Status Overview</h3>
                    <div class="chart-wrapper">
                        <canvas id="equipmentLineChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart for Item Status -->
                <div class="chart-container">
                    <h3 class="chart-title">Item Status Distribution</h3>
                    <div class="chart-wrapper pie">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart for System Overview -->
            <div class="chart-container full-width-chart">
                <h3 class="chart-title">System Overview Comparison</h3>
                <div class="chart-wrapper">
                    <canvas id="systemBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Secondary Stats Grid -->
        <h2 class="section-title">Additional Metrics</h2>
        <div class="stats-grid">
            <div class="stat-card secondary">
                <div class="stat-icon secondary">
                    <i class="fas fa-code-branch"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($activeBranches) }}</div>
                    <div class="stat-label">Active Branches</div>
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-icon danger">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($assignedItems) }}</div>
                    <div class="stat-label">Assigned Items</div>
                </div>
            </div>

            <div class="stat-card dark">
                <div class="stat-icon dark">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($totalReceivedEquipment) }}</div>
                    <div class="stat-label">Received Equipment</div>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon purple">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($totalInventoryForms) }}</div>
                    <div class="stat-label">Inventory Forms</div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="chart-container">
            <h3 class="chart-title">Key Performance Indicators</h3>
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value">
                        @if($totalEquipmentItems > 0)
                            {{ number_format(($assignedItems / $totalEquipmentItems) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="metric-label">Equipment Utilization</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">
                        @if($activeBranches > 0)
                            {{ number_format($totalEquipmentItems / $activeBranches, 1) }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="metric-label">Average Items per Branch</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">
                        @if($totalPropertyCards > 0)
                            {{ number_format(($totalPropertyCards / $totalReceivedEquipment) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="metric-label">Property Card Coverage</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">
                        @if($totalPropertyCards > 0)
                            {{ number_format(($availableItems / $totalPropertyCards) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="metric-label">Available Items Ratio</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart.js Configuration
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#6c757d';

        // Real data from PHP variables
        const totalEntities = {{ $totalEntities }};
        const totalEquipmentItems = {{ $totalEquipmentItems }};
        const totalPropertyCards = {{ $totalPropertyCards }};
        const availableItems = {{ $availableItems }};
        const assignedItems = {{ $assignedItems }};
        const totalReceivedEquipment = {{ $totalReceivedEquipment }};
        const totalInventoryForms = {{ $totalInventoryForms }};
        const activeBranches = {{ $activeBranches }};

        // Equipment Status Overview Chart (replacing the line chart with more relevant data)
        const equipmentCtx = document.getElementById('equipmentLineChart').getContext('2d');
        new Chart(equipmentCtx, {
            type: 'line',
            data: {
                labels: ['Total Equipment', 'Property Cards', 'Available Items', 'Assigned Items', 'Received Equipment'],
                datasets: [{
                    label: 'Current Status',
                    data: [totalEquipmentItems, totalPropertyCards, availableItems, assignedItems, totalReceivedEquipment],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });

        // Status Pie Chart with real data
        const statusCtx = document.getElementById('statusPieChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Available Items', 'Assigned Items'],
                datasets: [{
                    data: [availableItems, assignedItems],
                    backgroundColor: [
                        '#17a2b8',
                        '#e74c3c'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                }
            }
        });

        // System Overview Bar Chart with real data
        const systemCtx = document.getElementById('systemBarChart').getContext('2d');
        new Chart(systemCtx, {
            type: 'bar',
            data: {
                labels: ['Total Entities', 'Equipment Items', 'Property Cards', 'Available Items', 'Assigned Items', 'Received Equipment', 'Inventory Forms', 'Active Branches'],
                datasets: [{
                    label: 'Count',
                    data: [totalEntities, totalEquipmentItems, totalPropertyCards, availableItems, assignedItems, totalReceivedEquipment, totalInventoryForms, activeBranches],
                    backgroundColor: [
                        '#3498db',
                        '#27ae60',
                        '#f39c12',
                        '#17a2b8',
                        '#e74c3c',
                        '#343a40',
                        '#8e44ad',
                        '#6c757d'
                    ],
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
@endsection