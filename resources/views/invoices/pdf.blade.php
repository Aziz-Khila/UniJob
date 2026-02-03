<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #fff;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #1e40af;
        }
        .invoice-number {
            font-size: 18px;
            color: #64748b;
            margin-top: 5px;
        }
        .company-info {
            text-align: right;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 12px;
            color: #64748b;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .info-section {
            flex: 1;
        }
        .info-section h3 {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-section p {
            font-size: 14px;
            color: #0f172a;
            margin: 5px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table thead {
            background: #f8fafc;
        }
        .invoice-table th {
            padding: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .invoice-table td {
            padding: 15px;
            font-size: 14px;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }
        .invoice-table tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-bold {
            font-weight: bold;
        }
        .total-section {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        .total-row.total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
            margin-top: 10px;
            color: #10b981;
        }
        .total-label {
            color: #64748b;
        }
        .total-value {
            color: #0f172a;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-sent {
            background: #fef3c7;
            color: #92400e;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        .status-draft {
            background: #f1f5f9;
            color: #475569;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div>
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            </div>
            <div class="company-info">
                <div class="company-name">UniLife</div>
                <div class="company-details">
                    Job Platform<br>
                    Invoice Date: {{ $invoice->issued_at->format('F d, Y') }}<br>
                    Status: <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>

        <div class="invoice-info">
            <div class="info-section">
                <h3>Bill To</h3>
                <p class="text-bold">{{ $invoice->client->name }}</p>
                <p>{{ $invoice->client->email }}</p>
            </div>
            <div class="info-section">
                <h3>Job Information</h3>
                <p class="text-bold">{{ $invoice->job->title }}</p>
                <p>{{ Str::limit($invoice->job->description, 50) }}</p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Job: {{ $invoice->job->title }}</strong><br>
                        <small style="color: #64748b;">Completed by: {{ $invoice->student->name }}</small>
                    </td>
                    <td class="text-right text-bold">{{ number_format($invoice->total_amount, 2) }} TND</td>
                </tr>
                @if(isset($forStudent) && $forStudent)
                <tr style="background: #fef2f2;">
                    <td><strong>Platform Commission ({{ number_format($invoice->commission_rate, 0) }}%)</strong></td>
                    <td class="text-right" style="color: #ef4444;">-{{ number_format($invoice->commission_amount, 2) }} TND</td>
                </tr>
                <tr style="background: #f0fdf4;">
                    <td class="text-bold" style="font-size: 16px;">Amount Paid to Student</td>
                    <td class="text-right text-bold" style="font-size: 16px; color: #10b981;">{{ number_format($invoice->student_amount, 2) }} TND</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="footer">
            <p>Thank you for using UniLife platform.</p>
            <p>This is an automated invoice generated on {{ now()->format('F d, Y') }}.</p>
        </div>
    </div>
</body>
</html>

