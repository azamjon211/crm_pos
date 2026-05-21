<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chek #{{ $sale->id }}</title>
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 12px;
            min-height: 100vh;
            color: #111;
        }

        /* ── Receipt wrapper ── */
        .receipt {
            background: #fff;
            width: 302px; /* 80mm thermal */
            padding: 16px 14px 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
        }

        /* ── Header ── */
        .r-shop-name {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .r-shop-sub {
            text-align: center;
            font-size: 11px;
            color: #444;
            line-height: 1.5;
        }

        /* ── Divider ── */
        .r-div {
            border: none;
            border-top: 1px dashed #999;
            margin: 8px 0;
        }
        .r-div-solid {
            border: none;
            border-top: 1px solid #333;
            margin: 8px 0;
        }

        /* ── Meta info ── */
        .r-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            line-height: 1.7;
        }
        .r-row .label { color: #555; }
        .r-row .value { font-weight: 600; text-align: right; }

        /* ── Section title ── */
        .r-section {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            text-align: center;
            color: #333;
            margin: 2px 0;
        }

        /* ── Items ── */
        .r-item { margin: 5px 0; }
        .r-item-name {
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .r-item-calc {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #444;
            padding-left: 8px;
        }
        .r-item-calc .total { font-weight: 700; color: #111; }

        /* ── Total ── */
        .r-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            line-height: 1.8;
        }
        .r-total-row.grand {
            font-size: 15px;
            font-weight: bold;
        }
        .r-total-row.grand .amount { font-size: 16px; }

        /* ── Footer ── */
        .r-footer {
            text-align: center;
            margin-top: 4px;
        }
        .r-thank {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }
        .r-tagline {
            font-size: 11px;
            color: #555;
        }
        .r-powered {
            margin-top: 8px;
            font-size: 10px;
            color: #aaa;
            letter-spacing: .3px;
        }
        .r-powered span {
            color: #888;
            font-weight: 600;
        }

        /* ── Print button ── */
        .print-bar {
            width: 302px;
            margin-top: 14px;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            flex: 1;
            padding: 10px;
            background: #1a2535;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: .3px;
        }
        .btn-print:hover { background: #2d3f55; }
        .btn-close-win {
            padding: 10px 14px;
            background: #f1f5f9;
            color: #6b7a8f;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-close-win:hover { background: #e2e8f0; }

        /* ── Print media ── */
        @media print {
            body {
                background: none;
                padding: 0;
                display: block;
            }
            .receipt {
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
            .print-bar { display: none; }
        }
    </style>
</head>
<body>

@php
    $shop         = $sale->shop;
    $cashier      = $sale->cashier;
    $customer     = $sale->customer;
    $paymentLabel = \App\Models\Sale::PAYMENT_TYPES[$sale->payment_type] ?? $sale->payment_type;
    $grandTotal   = $sale->saleItems->sum(fn($i) => (float) $i->line_total);
@endphp

<div class="receipt">

    {{-- ── HEADER ── --}}
    <div class="r-shop-name">{{ $shop->name ?? config('app.name') }}</div>

    <hr class="r-div-solid">

    {{-- ── META ── --}}
    <div class="r-row">
        <span class="label">Chek #</span>
        <span class="value">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="r-row">
        <span class="label">Sana</span>
        <span class="value">{{ $sale->created_at->format('d.m.Y H:i') }}</span>
    </div>
    @if($cashier)
    <div class="r-row">
        <span class="label">Kassir</span>
        <span class="value">{{ $cashier->name }}</span>
    </div>
    @endif
    @if($customer)
    <div class="r-row">
        <span class="label">Mijoz</span>
        <span class="value">{{ $customer->name }}</span>
    </div>
    @endif

    <hr class="r-div">
    <div class="r-section">Mahsulotlar</div>
    <hr class="r-div">

    {{-- ── ITEMS ── --}}
    @foreach($sale->saleItems as $i => $item)
        <div class="r-item">
            <div class="r-item-name">{{ ($i + 1) }}. {{ $item->product->name ?? 'Noma\'lum' }}</div>
            <div class="r-item-calc">
                <span>{{ (float)$item->quantity }} × {{ number_format((float)$item->unit_price, 0, '.', ' ') }}</span>
                <span class="total">{{ number_format((float)$item->line_total, 0, '.', ' ') }} so'm</span>
            </div>
        </div>
    @endforeach

    <hr class="r-div-solid">

    {{-- ── TOTALS ── --}}
    <div class="r-total-row grand">
        <span>JAMI:</span>
        <span class="amount">{{ number_format($grandTotal, 0, '.', ' ') }} so'm</span>
    </div>
    <div class="r-total-row">
        <span>To'lov:</span>
        <span>{{ $paymentLabel }}</span>
    </div>
    @if($sale->note)
    <div class="r-total-row">
        <span>Izoh:</span>
        <span>{{ $sale->note }}</span>
    </div>
    @endif

    <hr class="r-div">

    {{-- ── FOOTER ── --}}
    <div class="r-footer">
        <div class="r-thank">Xaridingiz uchun rahmat!</div>
        <div class="r-tagline">Yana tashrif buyuring</div>
        <div class="r-powered">Developed by <span>Azamjon</span></div>
    </div>

</div>

<div class="print-bar">
    <button class="btn-print" onclick="window.print()">🖨 Chop etish</button>
    <button class="btn-close-win" onclick="window.close()">✕ Yopish</button>
</div>

<script>
    // Auto print after short delay
    window.addEventListener('load', () => setTimeout(() => window.print(), 600));
</script>

</body>
</html>
