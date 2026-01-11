<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt - {{ $donation->donation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #a01d62;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #a01d62;
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .receipt-subtitle {
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .info-value {
            color: #333;
            width: 60%;
            text-align: right;
        }
        .amount-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #a01d62;
        }
        .message-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #fff3cd;
            border-left: 4px solid #f89f3d;
            border-radius: 4px;
        }
        .message-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .footer-contact {
            margin-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-recurring {
            background-color: #cfe2ff;
            color: #084298;
        }
        .thank-you {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #a01d62 0%, #f03733 100%);
            color: white;
            border-radius: 8px;
        }
        .thank-you h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .thank-you p {
            font-size: 14px;
            opacity: 0.95;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Score Beyond Leadership</div>
            <div class="receipt-title">DONATION RECEIPT</div>
            <div class="receipt-subtitle">Official Tax Receipt for Charitable Contribution</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Receipt Number:</div>
                <div class="info-value">{{ $donation->donation_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date of Donation:</div>
                <div class="info-value">{{ $donation->paid_at?->format('F d, Y') ?? $donation->created_at->format('F d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Status:</div>
                <div class="info-value">
                    <span class="badge badge-success">{{ strtoupper($donation->payment_status) }}</span>
                </div>
            </div>
            @if($donation->is_recurring)
            <div class="info-row">
                <div class="info-label">Recurring Donation:</div>
                <div class="info-value">
                    <span class="badge badge-recurring">{{ strtoupper($donation->frequency ?? 'RECURRING') }}</span>
                </div>
            </div>
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-label">Donation Amount</div>
            <div class="amount-value">
                @if($donation->currency === 'UGX')
                    {{ number_format($donation->amount, 0) }} UGX
                @elseif($donation->currency === 'USD')
                    ${{ number_format($donation->amount, 2) }} USD
                @else
                    €{{ number_format($donation->amount, 2) }} EUR
                @endif
            </div>
        </div>

        <div class="info-section">
            <h3 style="margin-bottom: 15px; color: #a01d62; font-size: 16px;">Donor Information</h3>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $donation->first_name }} {{ $donation->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $donation->email }}</div>
            </div>
            @if($donation->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $donation->phone }}</div>
            </div>
            @endif
            @if($donation->country)
            <div class="info-row">
                <div class="info-label">Country:</div>
                <div class="info-value">{{ $donation->country }}</div>
            </div>
            @endif
            @if($donation->organization)
            <div class="info-row">
                <div class="info-label">Organization:</div>
                <div class="info-value">{{ $donation->organization }}</div>
            </div>
            @endif
            @if($donation->address)
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value" style="text-align: right;">{{ $donation->address }}</div>
            </div>
            @endif
        </div>

        @if($donation->message)
        <div class="message-section">
            <div class="message-title">Donor Message:</div>
            <div>{{ $donation->message }}</div>
        </div>
        @endif

        <div class="thank-you">
            <h2>Thank You for Your Generosity!</h2>
            <p>Your donation directly supports programs that empower women and girls through sports, education, health, and livelihood opportunities.</p>
        </div>

        <div class="info-section">
            <h3 style="margin-bottom: 15px; color: #a01d62; font-size: 16px;">Organization Information</h3>
            <div class="info-row">
                <div class="info-label">Organization Name:</div>
                <div class="info-value">Score Beyond Leadership</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact:</div>
                <div class="info-value">info@scorebeyondleadership.org</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">+256 772 319503</div>
            </div>
            <div class="info-row">
                <div class="info-label">Website:</div>
                <div class="info-value">www.scorebeyondleadership.org</div>
            </div>
        </div>

        <div class="footer">
            <p><strong>This is an official receipt for tax purposes.</strong></p>
            <p>Please retain this receipt for your records. This donation may be tax-deductible in your jurisdiction.</p>
            <div class="footer-contact">
                <p>For questions about this receipt, please contact us at info@scorebeyondleadership.org</p>
                <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

