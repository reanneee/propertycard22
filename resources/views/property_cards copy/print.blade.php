<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Card - {{ $groupedData->description }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .property-card {
            border: 2px solid #000;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            page-break-inside: avoid;
        }
        
        .header {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #000;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .entity-section {
            padding: 8px;
            border-bottom: 1px solid #000;
        }
        
        .entity-section strong {
            font-weight: bold;
        }
        
        .property-info {
            display: flex;
            border-bottom: 1px solid #000;
        }
        
        .property-left {
            flex: 3;
            padding: 8px;
            border-right: 1px solid #000;
        }
        
        .property-right {
            flex: 1;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .description-section {
            padding: 8px;
            border-bottom: 1px solid #000;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        
        .header-row-1 th {
            height: 25px;
        }
        
        .header-row-2 th {
            height: 20px;
        }
        
        .data-row td {
            height: 25px;
        }
        
        .col-date { width: 8%; }
        .col-ref { width: 12%; }
        .col-receipt { width: 8%; }
        .col-qty { width: 6%; }
        .col-issue { width: 20%; }
        .col-balance { width: 8%; }
        .col-amount { width: 10%; }
        .col-remarks { width: 28%; }
        
        .office-officer {
            font-size: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .property-card {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('property_cards.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="property-card">
        <!-- Header -->
        <div class="header">
            <h2>Property Card</h2>
        </div>
        
        <!-- Entity Name -->
        <div class="entity-section">
            <strong>Entity Name:</strong> {{ $groupedData->entity_name }}
        </div>
        
        <!-- Property Info Section -->
        <div class="property-info">
            <div class="property-left">
                <div style="margin-bottom: 5px;">
                    <strong>Property, Plant and Equipment:</strong> {{ $groupedData->description }}
                </div>
            </div>
            <div class="property-right">
                <div>
                    <strong>Property Number:</strong> {{ $groupedData->par_no }}
                </div>
            </div>
        </div>
        
        <!-- Description -->
        <div class="description-section">
            <strong>Description:</strong> {{ $groupedData->description }}
        </div>
        
        <!-- Main Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr class="header-row-1">
                        <th rowspan="2" class="col-date">Date</th>
                        <th rowspan="2" class="col-ref">Reference/<br>PAR No.</th>
                        <th colspan="2">Receipt</th>
                        <th colspan="2">Issue/Transfer/Disposal</th>
                        <th rowspan="2" class="col-balance">Balance<br>Qty.</th>
                        <th rowspan="2" class="col-amount">Amount</th>
                        <th rowspan="2" class="col-remarks">Remarks</th>
                    </tr>
                    <tr class="header-row-2">
                        <th class="col-receipt">Qty.</th>
                        <th class="col-qty">Qty.</th>
                        <th class="col-issue">Office/Officer</th>
                        <th class="col-qty">Qty.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($propertyCards as $index => $card)
                    <tr class="data-row">
                        <td>{{ $card->created_at ? $card->created_at->format('m/d/Y') : '' }}</td>
                        <td>{{ $groupedData->par }}</td>
                        <td>{{ $index == 0 ? $groupedData->receipt_quantity : '' }}</td>
                        <td>{{ $card->qty_physical }}</td>
                        <td class="office-officer">
                            @if($card->issue_transfer_disposal)
                                {{ $card->issue_transfer_disposal }}
                            @else
                                {{ $groupedData->office_name }}<br>
                                {{ $groupedData->officer_name }}
                            @endif
                        </td>
                        <td></td>
                        <td>{{ $card->balance ?? '' }}</td>
                        <td>{{ $index == 0 ? '₱' . number_format($groupedData->amount, 2) : '' }}</td>
                        <td style="font-size: 9px;">
                            @if($card->remarks)
                                {{ $card->remarks }}
                            @endif
                            @if($card->condition && $card->condition != 'Good')
                                <br><small><strong>Condition:</strong> {{ $card->condition }}</small>
                            @endif
                            @if($card->received_by_name)
                                <br><small><strong>Received by:</strong> {{ $card->received_by_name }}</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- Empty rows for additional entries -->
                    @for($i = count($propertyCards); $i < 15; $i++)
                    <tr class="data-row">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>