<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #007bff;
        }

        .header h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .header-info {
            font-size: 14px;
            color: #666;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 10px;
        }

        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .summary-card.revenue .value { color: #28a745; }
        .summary-card.expenses .value { color: #dc3545; }
        .summary-card.profit .value { color: #007bff; }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
            border-top: 2px solid #007bff;
        }

        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 10px 0;
        }

        .chart-placeholder {
            width: 100%;
            height: 200px;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-style: italic;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="header-info">
            <strong>Period:</strong> {{ $period }}<br>
            <strong>Date Range:</strong> {{ $formatted_date_range }}<br>
            @if($selected_plant)
                <strong>Plant Filter:</strong> {{ $selected_plant }}<br>
            @endif
            <strong>Generated:</strong> {{ $generated_at }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Trips</h3>
            <div class="value">{{ number_format($summary['total_trips']) }}</div>
        </div>
        <div class="summary-card revenue">
            <h3>Total Revenue</h3>
            <div class="value">Rs. {{ number_format($summary['total_revenue'], 2) }}</div>
        </div>
        <div class="summary-card expenses">
            <h3>Total Expenses</h3>
            <div class="value">Rs. {{ number_format($summary['total_expenses'], 2) }}</div>
        </div>
        <div class="summary-card profit">
            <h3>Net Income</h3>
            <div class="value">Rs. {{ number_format($summary['net_income'], 2) }}</div>
        </div>
    </div>

    <!-- Driver Performance Section -->
    <div class="section no-break">
        <h2 class="section-title">Driver Performance</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Phone</th>
                    <th class="text-center">Total Trips</th>
                    <th class="text-right">Total Salary</th>
                    <th class="text-right">Avg per Trip</th>
                    <th class="text-right">Income Generated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($driver_stats as $driver)
                <tr>
                    <td>{{ $driver['name'] }}</td>
                    <td>{{ $driver['phone'] !== 'N/A' ? $driver['phone'] : '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-primary">{{ $driver['total_trips'] }}</span>
                    </td>
                    <td class="text-right">Rs. {{ number_format($driver['total_salary'], 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($driver['avg_per_trip'], 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($driver['total_income_generated'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No driver data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Plant Performance Section -->
    <div class="section no-break">
        <h2 class="section-title">Plant Performance</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Plant Name</th>
                    <th class="text-center">Total Trips</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Avg per Trip</th>
                    <th class="text-right">Total Profit</th>
                    <th class="text-center">Profit Margin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plant_stats as $plant)
                <tr>
                    <td>{{ $plant['name'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ $plant['total_trips'] }}</span>
                    </td>
                    <td class="text-right">Rs. {{ number_format($plant['total_amount'], 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($plant['avg_per_trip'], 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($plant['total_profit'], 2) }}</td>
                    <td class="text-center">{{ number_format($plant['profit_margin'], 1) }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No plant data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Batch Statistics Section -->
    @if(!empty($batch_stats))
    <div class="section page-break">
        <h2 class="section-title">Recent Batch Statistics</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Batch ID</th>
                    <th>Date</th>
                    <th>Driver</th>
                    <th>Plant</th>
                    <th class="text-center">Trips</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Profit</th>
                    <th class="text-center">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batch_stats as $batch)
                <tr>
                    <td>{{ substr($batch['batch_id'], 0, 8) }}...</td>
                    <td>{{ \Carbon\Carbon::parse($batch['delivery_date'])->format('M j, Y') }}</td>
                    <td>{{ $batch['driver_name'] }}</td>
                    <td>{{ $batch['plant_name'] }}</td>
                    <td class="text-center">{{ $batch['trips_in_batch'] }}</td>
                    <td class="text-right">Rs. {{ number_format($batch['batch_revenue'], 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($batch['batch_profit'], 2) }}</td>
                    <td class="text-center">{{ number_format($batch['profit_margin'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Revenue Trend Section -->
    @if(!empty($revenue_trend['labels']))
    <div class="section page-break">
        <h2 class="section-title">Revenue Trend Data</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Period</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Profit</th>
                    <th class="text-center">Trips</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenue_trend['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">Rs. {{ number_format($revenue_trend['revenue'][$index] ?? 0, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($revenue_trend['expenses'][$index] ?? 0, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($revenue_trend['profit'][$index] ?? 0, 2) }}</td>
                    <td class="text-center">{{ $revenue_trend['trips'][$index] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="chart-placeholder">
            📊 Chart visualization would appear here in the web version
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated on {{ $generated_at }} | Trip Management System
    </div>
</body>
</html>
