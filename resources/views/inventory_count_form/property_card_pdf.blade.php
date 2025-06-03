<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Card PDF - {{ $itemDetails->description ?? 'Property Card' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
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
            text-align: left;
            padding: 2px;
        }

        strong {
            font-weight: bold;
        }

        .remarks-cell {
            text-align: left;
            vertical-align: top;
            font-size: 7px;
            line-height: 1.3;
            padding: 3px;
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
            <strong>Entity Name:</strong> {{ $itemDetails->entity_name ?? 'N/A' }}
        </div>

        <!-- Property Info Section -->
        <div class="property-info">
            <div class="property-left">
                <strong>Property, Plant and Equipment:</strong> {{ $itemDetails->article ?? $itemDetails->description ?? 'N/A' }}
            </div>
            <div class="property-right">
                <strong>Property Number:</strong><br>{{ $itemDetails->property_no ?? 'N/A' }}
                @if($itemDetails->new_property_no)
                    <br><small>New: {{ $itemDetails->new_property_no }}</small>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="description-section">
            <strong>Description:</strong> {{ $itemDetails->description ?? 'N/A' }}
        </div>

        <!-- Main Table -->
        <table>
            <thead>
                <tr class="header-row-1">
                    <th rowspan="2" class="col-date">Date</th>
                    <th rowspan="2" class="col-ref">Reference/<br>PAR No.</th>
                    <th colspan="2">Receipt</th>
                    <th colspan="1">Issue/Transfer/Disposal</th>
                    <th rowspan="2" class="col-balance">Balance<br>Qty.</th>
                    <th rowspan="2" class="col-amount">Amount</th>
                    <th rowspan="2" class="col-remarks">Remarks</th>
                </tr>
                <tr class="header-row-2">
                    <th class="col-receipt">Qty.</th>
                    <th class="col-qty">Qty.</th>
                    <th class="col-issue">Office/Officer</th>
                </tr>
            </thead>
            <tbody>
                <!-- Main Data Row -->
                <tr class="data-row">
                    <td>{{ $itemDetails->date_acquired ? \Carbon\Carbon::parse($itemDetails->date_acquired)->format('m/d/Y') : '' }}</td>
                    <td>{{ $itemDetails->par_no ?? '' }}</td>
                    <td>{{ $itemDetails->original_quantity ?? '' }}</td>
                    <td>{{ $itemDetails->physical_quantity ?? $itemDetails->original_quantity ?? '' }}</td>
                    <td class="office-officer">
                        @if($itemDetails->received_by_name)
                            {{ $itemDetails->received_by_name }}
                        @elseif($itemDetails->issue_transfer_disposal)
                            {{ $itemDetails->issue_transfer_disposal }}
                        @elseif($itemDetails->location)
                            {{ $itemDetails->location }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $itemDetails->original_quantity ?? '' }}</td>
                    <td>{{ $itemDetails->amount ? '₱' . number_format($itemDetails->amount, 2) : '' }}</td>
                    <td class="remarks-cell">
                        @if($itemDetails->combined_remarks)
                            {{ $itemDetails->combined_remarks }}
                        @else
                            @if($itemDetails->remarks)
                                {{ $itemDetails->remarks }}
                            @endif
                            @if($itemDetails->condition && strtolower($itemDetails->condition) !== 'good')
                                @if($itemDetails->remarks)<br>@endif
                                <strong>Condition:</strong> {{ $itemDetails->condition }}
                            @endif
                        @endif
                    </td>
                </tr>

                <!-- Empty rows for additional entries -->
                @for($i = 0; $i < 11; $i++)
                <tr class="data-row">
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
