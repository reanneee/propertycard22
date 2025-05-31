<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pangasinan State University - Inventory Count Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            background: white;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: none;
            margin: 0 auto;
            border: 2px solid #000;
        }

        /* Header Section */
        .form-header {
            text-align: center;
            padding: 15px;
            border-bottom: 2px solid #000;
            position: relative;
        }

        .annex-label {
            position: absolute;
            top: 10px;
            right: 15px;
            font-weight: bold;
            font-size: 12px;
        }

        .court-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-title {
            font-size: 13px;
            font-weight: bold;
        }

        /* PPE Account Group and Sheet Number Section */
        .form-info {
            display: flex;
            border-bottom: 1px solid #000;
        }

        .ppe-section {
            flex: 2;
            padding: 10px;
            border-right: 1px solid #000;
        }

        .sheet-section {
            flex: 1;
            padding: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .input-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 100px;
            height: 20px;
            margin-left: 5px;
        }

        /* Main Table */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .inventory-table th,
        .inventory-table td {
            border: 1px solid #000;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .inventory-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
        }

        /* Column widths optimized for long coupon size */
        .col-article { width: 6%; }
        .col-description { width: 18%; }
        .col-old-property { width: 8%; }
        .col-new-property { width: 10%; }
        .col-unit { width: 6%; }
        .col-unit-value { width: 8%; }
        .col-qty-card { width: 6%; }
        .col-qty-count { width: 6%; }
        .col-location { width: 12%; }
        .col-condition { width: 8%; }
        .col-remarks { width: 12%; }

        /* Multi-row headers */
        .header-main {
            height: 60px;
        }

        .header-sub {
            height: 40px;
        }

        /* Data rows */
        .data-row {
            height: 40px;
        }

        .data-row td {
            text-align: left;
            padding: 4px;
        }

        .data-row .text-center {
            text-align: center;
        }

        /* Footer Section */
        .form-footer {
            display: flex;
            border-top: 1px solid #000;
        }

        .prepared-section,
        .reviewed-section {
            flex: 1;
            padding: 20px;
            text-align: center;
        }

        .prepared-section {
            border-right: 1px solid #000;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 40px auto 10px;
            width: 300px;
        }

        .role-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Print styles for long coupon */
        @media print {
            body {
                padding: 5px;
                font-size: 9px;
            }
            
            .form-container {
                border: 1px solid #000;
                width: 100%;
            }
            
            .inventory-table {
                font-size: 7px;
            }
            
            .inventory-table th,
            .inventory-table td {
                padding: 2px 1px;
            }
            
            .form-header {
                padding: 10px;
            }
            
            .form-info {
                padding: 5px;
            }
            
            .ppe-section, .sheet-section {
                padding: 5px;
            }
        }

        @page {
            margin: 0.5in;
            size: 8.5in 13in;
        }

        /* Sample data styling */
        .sample-data {
            font-size: 9px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <div class="annex-label">Annex A.</div>
            <div class="court-title">Pangasinan State University</div>
            <div class="form-title">Inventory Count Form</div>
        </div>

        <!-- PPE Account Group and Sheet Number -->
        <div class="form-info">
            <div class="ppe-section">
                <strong>{{ $inventoryForm->title ?? '' }}</strong>
                <span class="input-line">{{ $inventoryForm->fund_account_code ?? '' }}</span>
            </div>
            <div class="sheet-section">
                <strong>Sheet No.</strong>
                <span class="input-line">1</span>
                <strong>of</strong>
                <span class="input-line">1</span>
            </div>
        </div>

        <!-- Main Inventory Table -->
        <table class="inventory-table">
            <thead>
                <tr class="header-main">
                    <th rowspan="2" class="col-article">Article/Item</th>
                    <th rowspan="2" class="col-description">Description</th>
                    <th rowspan="2" class="col-old-property">Old Property No. Assigned</th>
                    <th rowspan="2" class="col-new-property">New Property No. Assigned (to be filled up during validation)</th>
                    <th rowspan="2" class="col-unit">Unit of Measurement</th>
                    <th rowspan="2" class="col-unit-value">Unit Value</th>
                    <th colspan="2" class="col-qty-header">Quantity per</th>
                    <th rowspan="2" class="col-location">Location/ Whereabouts</th>
                    <th rowspan="2" class="col-condition">Condition</th>
                    <th rowspan="2" class="col-remarks">Remarks</th>
                </tr>
                <tr class="header-sub">
                    <th class="col-qty-card">Property Card</th>
                    <th class="col-qty-count">Physical Count</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic data from Laravel -->
                @forelse($inventoryItems as $index => $item)
                <tr class="data-row">
                    <td>{{ $item->article }}</td>
                    <td>{{ $item->article_description }}</td>
                    <td class="text-center">{{ $item->old_property_no }}</td>
                    <td class="text-center">{{ $item->new_property_no }}</td>
                    <td class="text-center">{{ $item->unit ?? 'pcs' }}</td>
                    <td class="text-center">
                        @if($item->unit_value)
                            ₱{{ number_format($item->unit_value, 2) }}
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity_per_property_card ?? 0 }}</td>
                    <td class="text-center">{{ $item->quantity_per_physical_count ?? 0 }}</td>
                    <td>{{ $item->location_whereabouts }}</td>
                    <td class="text-center">{{ $item->condition }}</td>
                    <td>{{ $item->remarks }}</td>
                </tr>
                @empty
                <tr class="data-row">
                    <td colspan="11" style="text-align: center; font-style: italic;">No inventory items found for this entity.</td>
                </tr>
                @endforelse
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer with Prepared By and Reviewed By -->
        <div class="form-footer">
            <div class="prepared-section">
                <div class="role-title">Prepared By:</div>
                <div class="signature-line"></div>
                <div>{{ $inventoryForm->prepared_by_name ?? 'Name' }}</div>
                <div>{{ $inventoryForm->prepared_by_position ?? 'Name' }}</div>
            </div>
            <div class="reviewed-section">
                <div class="role-title">Reviewed By:</div>
                <div class="signature-line"></div>
                <div>{{ $inventoryForm->reviewed_by_name ?? 'Name' }}</div>
                <div>{{ $inventoryForm->reviewed_by_position ?? 'Name' }}</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print functionality (optional)
        window.onload = function() {
            // Uncomment to enable auto-print
            // window.print();
        };
    </script>
</body>
</html>