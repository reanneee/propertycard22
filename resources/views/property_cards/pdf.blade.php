<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Card PDF - {{ $groupedData->description }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .property-card {
            border: 2px solid #000;
            width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #000;
        }
        
        .header h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .entity-section {
            padding: 6px;
            border-bottom: 1px solid #000;
        }
        
        .property-info {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .property-left {
            display: table-cell;
            width: 75%;
            padding: 6px;
            border-right: 1px solid #000;
            vertical-align: top;
        }
        
        .property-right {
            display: table-cell;
            width: 25%;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
        }
        
        .description-section {
            padding: 6px;
            border-bottom: 1px solid #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8px;
        }
        
        .header-row-1 th {
            height: 20px;
        }
        
        .header-row-2 th {
            height: 16px;
        }
        
        .data-row td {
            height: 20px;
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
            font-size: 8px;
            line-height: 1.1;
        }
        
        .small-text {
            font-size: 7px;
        }
        
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
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
                <strong>Property, Plant and Equipment:</strong> {{ $groupedData->description }}
            </div>
            <div class="property-right">
                <strong>Property Number:</strong><br>{{ $groupedData->par_no }}
            </div>
        </div>
        
        <!-- Description -->
        <div class="description-section">
            <strong>Description:</strong> {{ $groupedData->description }}
        </div>
        
        <!-- Main Table -->
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
                    <td class="small-text">
                        @if($card->remarks)
                            {{ $card->remarks }}
                        @endif
                        @if($card->condition && $card->condition != 'Good')
                            <br><strong>Condition:</strong> {{ $card->condition }}
                        @endif
                        @if($card->received_by_name)
                            <br><strong>Received by:</strong> {{ $card->received_by_name }}
                        @endif
                    </td>
                </tr>
                @endforeach
                
                <!-- Empty rows for additional entries -->
                @for($i = count($propertyCards); $i < 12; $i++)
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
</body>
</html>