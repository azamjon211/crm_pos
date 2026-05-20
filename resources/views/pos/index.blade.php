@extends('layouts.app')
@section('title', 'POS Kassir')
@section('page_title', 'POS Kassir')

@section('content')
<style>
    /* ── Reset topbar page-body padding for POS ── */
    .page-body { padding: 0 !important; background: #f0f4f8; }

    /* ── Root layout ── */
    .pos-root {
        display: flex;
        height: calc(100vh - 60px); /* topbar height */
        overflow: hidden;
        gap: 0;
    }

    /* ══════════════════════════════════════════
       LEFT — CART PANEL
    ══════════════════════════════════════════ */
    .pos-cart {
        width: 420px;
        min-width: 380px;
        max-width: 480px;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-right: 1px solid #e4eaf2;
        height: 100%;
    }

    /* Cart header */
    .cart-header {
        padding: 14px 16px 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .cart-tab {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid #e4eaf2;
        font-size: 13px; font-weight: 600;
        color: #6b7a8f;
        text-decoration: none;
        background: #f8fafc;
        transition: all .15s;
        position: relative;
    }
    .cart-tab:hover { background: #f1f5f9; color: #1a2535; }
    .cart-tab.active {
        background: #6366f1;
        border-color: #6366f1;
        color: #fff;
        box-shadow: 0 3px 10px rgba(99,102,241,.3);
    }
    .cart-tab .tab-count {
        min-width: 18px; height: 18px;
        background: rgba(255,255,255,.3);
        border-radius: 10px;
        font-size: 10px; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0 4px;
    }
    .cart-tab:not(.active) .tab-count {
        background: #ef4444; color: #fff;
    }
    .cart-cashier {
        margin-left: auto;
        font-size: 12px; color: #94a3b8;
        display: flex; align-items: center; gap: 5px;
    }

    /* Cart table area */
    .cart-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }
    .cart-body::-webkit-scrollbar { width: 4px; }
    .cart-body::-webkit-scrollbar-track { background: transparent; }
    .cart-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table thead th {
        position: sticky; top: 0; z-index: 2;
        background: #f8fafc;
        padding: 9px 12px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: #94a3b8;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .cart-table td {
        padding: 9px 12px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
        font-size: 13px;
    }
    .cart-table tbody tr:hover td { background: #fafbff; }
    .cart-empty {
        text-align: center; padding: 48px 20px;
        color: #b0bec5;
    }
    .cart-empty i { font-size: 40px; display: block; margin-bottom: 10px; opacity: .5; }
    .cart-empty span { font-size: 13px; }

    /* Cart total row */
    .cart-total {
        border-top: 2px solid #f1f5f9;
        padding: 12px 16px;
        display: flex; justify-content: space-between; align-items: center;
        flex-shrink: 0;
        background: #fff;
    }
    .cart-total-label { font-size: 13px; font-weight: 600; color: #6b7a8f; }
    .cart-total-amount {
        font-size: 22px; font-weight: 800;
        color: #1a2535; letter-spacing: -.5px;
    }

    /* Checkout panel */
    .cart-checkout {
        background: #f8fafc;
        border-top: 1px solid #e4eaf2;
        padding: 14px 16px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .checkout-row { display: flex; gap: 8px; }
    .checkout-row .form-select,
    .checkout-row .form-control {
        font-size: 13px; padding: 8px 12px; border-radius: 8px;
        border-color: #e4eaf2;
    }
    .checkout-row .form-select:focus,
    .checkout-row .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.1);
    }
    .change-bar {
        display: flex; justify-content: space-between; align-items: center;
        background: #fff; border-radius: 8px; padding: 8px 12px;
        border: 1px solid #e4eaf2;
        font-size: 13px;
    }
    .change-bar .label { color: #6b7a8f; font-weight: 500; }
    .change-bar .amount { font-weight: 700; color: #10b981; font-size: 15px; }
    .btn-complete {
        width: 100%; padding: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none; border-radius: 10px;
        color: #fff; font-size: 16px; font-weight: 800;
        cursor: pointer; letter-spacing: -.2px;
        box-shadow: 0 4px 14px rgba(16,185,129,.3);
        transition: all .18s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-complete:hover {
        background: linear-gradient(135deg, #059669, #047857);
        box-shadow: 0 6px 18px rgba(16,185,129,.45);
        transform: translateY(-1px);
    }
    .btn-complete:active { transform: scale(.98); }
    .btn-save {
        flex: 1; padding: 9px;
        background: #fff; border: 1px solid #e4eaf2;
        border-radius: 8px; font-size: 13px; font-weight: 600;
        color: #6b7a8f; cursor: pointer; transition: all .15s;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-save:hover { background: #f1f5f9; color: #1a2535; }
    .btn-receipt {
        padding: 9px 14px;
        background: #fff; border: 1px solid #e4eaf2;
        border-radius: 8px; font-size: 13px; font-weight: 600;
        color: #6366f1; cursor: pointer; transition: all .15s;
        text-decoration: none;
        display: flex; align-items: center; gap: 6px;
    }
    .btn-receipt:hover { background: #f5f3ff; border-color: #6366f1; color: #6366f1; }

    /* ══════════════════════════════════════════
       RIGHT — PRODUCT BROWSER
    ══════════════════════════════════════════ */
    .pos-browser {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        height: 100%;
        background: #f0f4f8;
    }

    /* Search bar */
    .browser-search {
        padding: 12px 16px;
        background: #fff;
        border-bottom: 1px solid #e4eaf2;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
    }
    .search-wrap { position: relative; }
    .search-input-group {
        display: flex; align-items: center;
        background: #f8fafc;
        border: 1.5px solid #e4eaf2;
        border-radius: 10px;
        overflow: hidden;
        transition: border-color .18s, box-shadow .18s;
    }
    .search-input-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.1);
        background: #fff;
    }
    .search-icon { padding: 0 12px; color: #94a3b8; font-size: 16px; flex-shrink: 0; }
    .search-field {
        flex: 1; border: none; background: transparent;
        padding: 10px 0; font-size: 14px; color: #1a2535;
        outline: none; font-family: inherit;
    }
    .search-field::placeholder { color: #b0bec5; }
    .search-clear {
        padding: 0 12px; color: #b0bec5; font-size: 14px;
        cursor: pointer; border: none; background: none;
        transition: color .15s; flex-shrink: 0;
    }
    .search-clear:hover { color: #6366f1; }

    /* Search dropdown */
    .search-dropdown {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0;
        z-index: 500;
        background: #fff; border-radius: 12px;
        border: 1px solid #e4eaf2;
        box-shadow: 0 12px 40px rgba(0,0,0,.14);
        max-height: 340px; overflow-y: auto;
        display: none;
    }
    .search-dropdown.open { display: block; }
    .sd-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; cursor: pointer;
        border-bottom: 1px solid #f8fafc; gap: 10px;
        transition: background .1s;
    }
    .sd-item:last-child { border-bottom: none; }
    .sd-item:hover, .sd-item.focused { background: #f5f3ff; }
    .sd-item.out { cursor: not-allowed; opacity: .5; }
    .sd-item.out:hover { background: #fff; }
    .sd-name {
        font-size: 13.5px; font-weight: 600; flex: 1; min-width: 0;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: #1a2535;
    }
    .sd-right { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; flex-shrink: 0; }
    .sd-price { font-size: 13.5px; font-weight: 700; color: #10b981; white-space: nowrap; }
    .sd-stock { font-size: 11.5px; white-space: nowrap; }
    .sd-empty, .sd-loading { padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; }

    /* Category + product selects row */
    .sel-row { display: flex; gap: 8px; align-items: center; }
    .sel-row .form-select {
        font-size: 13px; padding: 8px 12px; border-radius: 8px;
        border-color: #e4eaf2; background: #f8fafc;
    }
    .sel-row .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); background: #fff; }
    .btn-sel-add {
        padding: 8px 18px; border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none; color: #fff; font-size: 13px; font-weight: 700;
        cursor: pointer; white-space: nowrap;
        box-shadow: 0 2px 8px rgba(99,102,241,.3);
        transition: all .15s;
    }
    .btn-sel-add:disabled { opacity: .45; cursor: not-allowed; box-shadow: none; transform: none; }
    .btn-sel-add:not(:disabled):hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); }

    /* Category pills */
    .cat-bar {
        padding: 10px 16px;
        background: #fff;
        border-bottom: 1px solid #e4eaf2;
        flex-shrink: 0;
    }
    .cat-bar-label {
        font-size: 10px; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;
    }
    .cat-pills { display: flex; flex-wrap: wrap; gap: 6px; }
    .cat-pill {
        padding: 5px 14px; border-radius: 20px;
        border: 1.5px solid #e4eaf2;
        font-size: 13px; font-weight: 500;
        color: #6b7a8f; background: #f8fafc;
        cursor: pointer; transition: all .15s;
        white-space: nowrap;
    }
    .cat-pill:hover:not(.active) { background: #f5f3ff; border-color: #c7d2fe; color: #6366f1; }
    .cat-pill.active {
        background: #6366f1; border-color: #6366f1;
        color: #fff; font-weight: 600;
        box-shadow: 0 2px 8px rgba(99,102,241,.3);
    }

    /* Product grid header */
    .prod-header {
        padding: 8px 16px;
        display: flex; align-items: center; gap: 8px;
        flex-shrink: 0;
    }
    .prod-header-label {
        font-size: 10px; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #94a3b8;
    }

    /* Product grid body */
    .prod-body {
        flex: 1; overflow-y: auto; padding: 0 12px 12px;
        min-height: 0;
    }
    .prod-body::-webkit-scrollbar { width: 4px; }
    .prod-body::-webkit-scrollbar-track { background: transparent; }
    .prod-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .prod-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
        padding-top: 12px;
    }
    .prod-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #e8edf5;
        padding: 14px 12px;
        text-align: center;
        cursor: pointer;
        transition: all .18s;
        display: flex; flex-direction: column; align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        position: relative;
    }
    .prod-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 16px rgba(99,102,241,.18);
        transform: translateY(-2px);
        background: #fefeff;
    }
    .prod-card:active { transform: scale(.97); }
    .prod-card.out {
        cursor: not-allowed; opacity: .55;
        background: #f8fafc;
    }
    .prod-card.out:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border-color: #e8edf5; }

    .prod-card-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 8px; font-size: 18px;
    }
    .prod-card-icon.out-icon { background: #f1f5f9; }
    .prod-card-name {
        font-size: 12.5px; font-weight: 600; line-height: 1.35;
        color: #1a2535; margin-bottom: 6px;
        display: -webkit-box; -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    .prod-card-price {
        font-size: 14px; font-weight: 800;
        color: #10b981; letter-spacing: -.3px;
        margin-bottom: 4px;
    }
    .prod-card-price.out-price { color: #94a3b8; }
    .prod-card-stock { font-size: 11px; }
    .prod-card-badge {
        position: absolute; top: 8px; right: 8px;
    }

    .prod-state {
        display: flex; align-items: center; justify-content: center;
        height: 200px; color: #94a3b8;
        font-size: 13px; flex-direction: column; gap: 8px;
    }
    .prod-state i { font-size: 36px; opacity: .4; }
    .prod-count { font-size: 11.5px; color: #b0bec5; text-align: center; padding: 6px 0 4px; }

    /* qty input in cart */
    .qty-input {
        width: 72px; border: 1.5px solid #e4eaf2; border-radius: 7px;
        padding: 4px 8px; font-size: 13px; font-weight: 600;
        text-align: center; outline: none;
        transition: border-color .15s;
        font-family: inherit;
    }
    .qty-input:focus { border-color: #6366f1; }

    /* F2 hint */
    .f2-hint {
        font-size: 11px; color: #b0bec5; padding: 0 2px;
    }
    kbd {
        background: #f1f5f9; border: 1px solid #e4eaf2;
        border-radius: 4px; padding: 1px 5px;
        font-size: 10.5px; color: #6b7a8f;
        font-family: inherit;
    }

    /* ─── POS mobile tabs ──────────────────────────────────── */
    .pos-mobile-tabs { display: none; }

    @media (max-width: 767px) {
        .pos-mobile-tabs {
            display: flex;
            background: #fff;
            border-bottom: 2px solid #e4eaf2;
            flex-shrink: 0;
        }
        .pos-mobile-tab {
            flex: 1; padding: 11px 8px;
            border: none; background: none;
            font-size: 13px; font-weight: 600;
            color: #6b7a8f; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .15s;
        }
        .pos-mobile-tab.active { color: #6366f1; border-bottom-color: #6366f1; }
        .pos-mobile-tab .tab-badge {
            background: #ef4444; color: #fff;
            border-radius: 10px; font-size: 10px; font-weight: 700;
            padding: 1px 5px; min-width: 16px; text-align: center;
        }

        .pos-root { height: calc(100vh - 60px - 47px); }

        .pos-cart {
            width: 100% !important;
            min-width: unset !important;
            max-width: unset !important;
            border-right: none;
        }
        .pos-cart.tab-hidden, .pos-browser.tab-hidden { display: none; }

        /* Smaller checkout button on mobile */
        .btn-complete { padding: 12px; font-size: 15px; }

        /* Hide keyboard hint on mobile */
        .f2-hint { display: none; }
    }
</style>

@if(session('low_stock_warning'))
<div style="position:fixed;top:70px;right:20px;z-index:9999;max-width:340px">
    <div class="alert alert-warning alert-dismissible shadow-sm d-flex gap-2 align-items-start" role="alert" style="font-size:13px">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1" style="font-size:16px"></i>
        <div>
            <strong>Zaxira kam qoldi!</strong><br>
            @foreach(session('low_stock_warning') as $name)
                <span>• {{ $name }}</span><br>
            @endforeach
        </div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

<div class="pos-mobile-tabs" id="posMobileTabs">
    <button class="pos-mobile-tab" data-target="browser" id="tabBrowser">
        <i class="bi bi-grid-fill"></i> Mahsulotlar
    </button>
    <button class="pos-mobile-tab active" data-target="cart" id="tabCart">
        <i class="bi bi-cart-fill"></i> Savat
        <span class="tab-badge" id="mobileCartBadge" style="display:none">0</span>
    </button>
</div>

<div class="pos-root">

    {{-- ═══════════ LEFT: CART ═══════════ --}}
    <div class="pos-cart" id="posCart">

        {{-- Cart tabs --}}
        <div class="cart-header">
            @for($i = 1; $i <= 3; $i++)
                @php $tabItems = session('pos_cart_'.$i, ['items' => []])['items']; @endphp
                <a href="{{ route('pos.index', ['cart' => $i]) }}"
                   class="cart-tab {{ $cart == $i ? 'active' : '' }}">
                    <i class="bi bi-cart{{ $cart == $i ? '-fill' : '' }}" style="font-size:13px"></i>
                    Savat {{ $i }}
                    @if(count($tabItems) > 0)
                        <span class="tab-count">{{ count($tabItems) }}</span>
                    @endif
                </a>
            @endfor
            <div class="cart-cashier">
                <i class="bi bi-person-circle" style="font-size:15px"></i>
                {{ auth()->user()->name }}
            </div>
        </div>

        {{-- Cart form --}}
        <form method="POST" action="{{ route('pos.complete') }}" id="checkoutForm"
              style="display:contents">
            @csrf
            <input type="hidden" name="cart" value="{{ $cart }}">

            {{-- Items --}}
            <div class="cart-body">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Mahsulot</th>
                            <th style="width:80px;text-align:center">Miqdor</th>
                            <th style="width:100px;text-align:right">Jami</th>
                            <th style="width:32px"></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody"></tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="cart-total">
                <div>
                    <div class="cart-total-label">Jami to'lov</div>
                    <div class="cart-total-amount" id="cartTotal">0 so'm</div>
                </div>
                <i class="bi bi-cash-stack" style="font-size:32px;color:#e4eaf2"></i>
            </div>

            {{-- Checkout --}}
            <div class="cart-checkout">

                <select name="customer_id" class="form-select" style="font-size:13px;padding:8px 12px;border-radius:8px;border-color:#e4eaf2">
                    <option value="">— Mijoz (ixtiyoriy) —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}{{ $c->phone ? ' · '.$c->phone : '' }}</option>
                    @endforeach
                </select>

                <div class="checkout-row">
                    <select name="payment_type" id="paymentType"
                            class="form-select" style="flex:1"
                            onchange="toggleCashFields()">
                        @foreach(\App\Models\Sale::PAYMENT_TYPES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="number" id="cashReceived"
                           class="form-control" style="flex:1"
                           placeholder="Qabul qilindi" step="100" min="0"
                           oninput="updateChange()">
                </div>

                <div class="change-bar" id="changeGroup">
                    <span class="label"><i class="bi bi-arrow-return-left me-1"></i>Qaytim:</span>
                    <span class="amount" id="changeDisplay">0 so'm</span>
                </div>

                <input type="text" name="note" value="{{ $cartData['note'] ?? '' }}"
                       class="form-control" style="font-size:13px;padding:8px 12px;border-radius:8px;border-color:#e4eaf2"
                       placeholder="Izoh (ixtiyoriy)">

                <button type="submit" class="btn-complete" id="btnComplete">
                    <i class="bi bi-check-lg" style="font-size:18px"></i>
                    Yakunlash
                </button>

                <div class="d-flex gap-2">
                    <button type="submit" formaction="{{ route('pos.save-cart') }}" class="btn-save">
                        <i class="bi bi-floppy-fill"></i> Saqlash
                    </button>
                    @if($lastSaleId)
                        <a href="{{ route('pos.receipt', $lastSaleId) }}" class="btn-receipt" target="_blank">
                            <i class="bi bi-printer-fill"></i> Chek
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ═══════════ RIGHT: BROWSER ═══════════ --}}
    <div class="pos-browser" id="posBrowser">

        {{-- Search --}}
        <div class="browser-search">
            <div class="search-wrap">
                <div class="search-input-group">
                    <span class="search-icon"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="search-field"
                           placeholder="Barcode, nom yoki SKU..." autofocus autocomplete="off">
                    <button type="button" class="search-clear" id="btnClearSearch" title="Tozalash">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div id="searchDropdown" class="search-dropdown"></div>
            </div>

            <div class="sel-row">
                <select id="selCategory" class="form-select" style="flex:1">
                    <option value="">— Kategoriya bo'yicha —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select id="selProduct" class="form-select" style="flex:1.4" disabled>
                    <option value="">— Mahsulot —</option>
                </select>
                <button type="button" id="btnSelAdd" class="btn-sel-add" disabled>
                    <i class="bi bi-plus-lg"></i> Qo'shish
                </button>
            </div>

            <div class="f2-hint">
                <kbd>F2</kbd> — qidiruvga o'tish &nbsp;·&nbsp;
                <kbd>↑↓</kbd> — ro'yxatda navigatsiya &nbsp;·&nbsp;
                <kbd>Enter</kbd> — qo'shish
            </div>
        </div>

        {{-- Category pills --}}
        <div class="cat-bar">
            <div class="cat-bar-label">Kategoriya</div>
            <div class="cat-pills" id="catPills">
                <button type="button" class="cat-pill active" data-cat="0">Barchasi</button>
                @foreach($categories as $cat)
                    <button type="button" class="cat-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>

        {{-- Product area --}}
        <div class="prod-header">
            <span class="prod-header-label">Mahsulotlar</span>
            <span id="activeCatBadge" class="badge"
                  style="background:#ede9fe;color:#7c3aed;display:none;font-size:11.5px"></span>
            <span id="prodCountBadge" class="prod-count ms-auto"></span>
        </div>

        <div class="prod-body" id="prodBody">
            <div class="prod-state">
                <i class="bi bi-box-seam"></i>
                <span>Yuklanmoqda...</span>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const CSRF       = '{{ csrf_token() }}';
    const CART_NUM   = {{ $cart }};
    const ADD_URL    = '{{ route("pos.add-to-cart") }}';
    const REMOVE_URL = '{{ route("pos.remove-row") }}';
    const SEARCH_URL = '{{ route("pos.search") }}';
    const CASH_KEY   = '{{ \App\Models\Sale::PAYMENT_CASH }}';

    let cartItems   = @json(array_values($cartData['items']));
    let activeCatId = 0;
    let searchTimer = null;

    /* ── Utilities ──────────────────────────────────────────── */

    function fmt(n) { return Math.round(n).toLocaleString('ru-RU'); }

    function esc(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    async function postJson(url, body) {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        return r.ok ? r.json() : null;
    }

    /* ── Cart rendering ─────────────────────────────────────── */

    function renderCart() {
        const tbody = document.getElementById('cartBody');

        if (!cartItems.length) {
            tbody.innerHTML = `
                <tr><td colspan="4">
                    <div class="cart-empty">
                        <i class="bi bi-cart3"></i>
                        <span>Savat bo'sh — mahsulot qo'shing</span>
                    </div>
                </td></tr>`;
            updateTotal();
            updateMobileCartBadge();
            return;
        }

        tbody.innerHTML = cartItems.map((item, i) => `
            <tr>
                <td>
                    <input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">
                    <div style="font-size:13px;font-weight:600;line-height:1.3;color:#1a2535">${esc(item.name)}</div>
                    <div style="font-size:11.5px;color:#94a3b8">${fmt(item.unit_price)} so'm/dona</div>
                </td>
                <td style="text-align:center">
                    <input type="number" name="items[${i}][quantity]"
                           value="${item.quantity}"
                           class="qty-input"
                           step="0.01" min="0.01"
                           data-index="${i}" data-price="${item.unit_price}"
                           oninput="onQtyChange(this)">
                </td>
                <td style="text-align:right;font-weight:700;font-size:13.5px;color:#1a2535" id="line-${i}">
                    ${fmt(item.unit_price * item.quantity)}
                </td>
                <td style="text-align:center">
                    <button type="button"
                            onclick="removeItem(${i})"
                            style="border:none;background:none;color:#fca5a5;font-size:16px;cursor:pointer;padding:2px;line-height:1;transition:color .15s"
                            onmouseover="this.style.color='#ef4444'"
                            onmouseout="this.style.color='#fca5a5'"
                            title="O'chirish">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        updateTotal();
        updateMobileCartBadge();
    }

    function updateMobileCartBadge() {
        const badge = document.getElementById('mobileCartBadge');
        if (!badge) return;
        const count = cartItems.length;
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }

    function onQtyChange(input) {
        const i     = parseInt(input.dataset.index);
        const price = parseFloat(input.dataset.price);
        const qty   = parseFloat(input.value) || 0;
        cartItems[i].quantity = qty;
        const el = document.getElementById(`line-${i}`);
        if (el) el.textContent = fmt(price * qty);
        updateTotal();
    }

    function updateTotal() {
        const total = cartItems.reduce((s, it) => s + it.unit_price * (it.quantity || 0), 0);
        document.getElementById('cartTotal').textContent = fmt(total) + ' so\'m';
        updateChange();
    }

    /* ── Cart AJAX ──────────────────────────────────────────── */

    async function addProduct(productId) {
        const d = await postJson(ADD_URL, { product_id: productId, cart: CART_NUM });
        if (!d) return;
        cartItems = d.cart.items;
        renderCart();
    }

    async function removeItem(index) {
        const d = await postJson(REMOVE_URL, { deleteRow: index, cart: CART_NUM });
        if (!d) return;
        cartItems = d.cart.items;
        renderCart();
    }

    /* ── Payment UI ─────────────────────────────────────────── */

    function toggleCashFields() {
        const isCash = document.getElementById('paymentType').value === CASH_KEY;
        const rcv    = document.getElementById('cashReceived');
        const chg    = document.getElementById('changeGroup');
        rcv.style.display = isCash ? '' : 'none';
        chg.style.display = isCash ? '' : 'none';
    }

    function updateChange() {
        const total    = cartItems.reduce((s, it) => s + it.unit_price * (it.quantity || 0), 0);
        const received = parseFloat(document.getElementById('cashReceived')?.value || 0);
        const change   = received - total;
        const el       = document.getElementById('changeDisplay');
        if (!el) return;
        el.textContent = fmt(Math.max(0, change)) + ' so\'m';
        el.style.color = change < 0 ? '#ef4444' : '#10b981';
    }

    /* ── Unified refresh ────────────────────────────────────── */

    async function refresh() {
        const q       = document.getElementById('searchInput').value.trim();
        const showAll = (q === '' && activeCatId === 0);
        setProdLoading();

        try {
            const p   = new URLSearchParams({ q, category_id: activeCatId, show_all: showAll ? '1' : '0' });
            const res = await fetch(`${SEARCH_URL}?${p}`, { headers: { Accept: 'application/json' } });
            if (!res.ok) { renderProducts([]); return; }
            const items = (await res.json()).items ?? [];

            if (items.length === 1 && items[0].barcode && q === items[0].barcode) {
                await addProduct(items[0].id);
                document.getElementById('searchInput').value = '';
                activeCatId = 0; setActiveCat(0); refresh();
                return;
            }
            renderProducts(items);
        } catch { renderProducts([]); }
    }

    /* ── Search dropdown ────────────────────────────────────── */

    const searchInput    = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    let   dropdownItems  = [];
    let   focusedIdx     = -1;

    function openDropdown(items) {
        dropdownItems = items; focusedIdx = -1;
        if (!items.length) {
            searchDropdown.innerHTML = '<div class="sd-empty"><i class="bi bi-search" style="font-size:20px;display:block;margin-bottom:6px;opacity:.4"></i>Mahsulot topilmadi</div>';
        } else {
            searchDropdown.innerHTML = items.map((p, i) => {
                const out      = p.stock_quantity <= 0;
                const lowStock = p.stock_quantity > 0 && p.stock_quantity <= 5;
                const stockEl  = out
                    ? `<span style="font-size:11px;color:#ef4444;font-weight:600">Tugagan</span>`
                    : lowStock
                        ? `<span style="font-size:11px;color:#f59e0b;font-weight:600">⚠ ${p.stock_quantity} ta</span>`
                        : `<span style="font-size:11px;color:#94a3b8">${p.stock_quantity} ta</span>`;
                return `<div class="sd-item${out?' out':''}" data-index="${i}" data-id="${p.id}" data-out="${out?1:0}">
                    <span class="sd-name" title="${esc(p.name)}">${esc(p.name)}</span>
                    <span class="sd-right">
                        <span class="sd-price">${fmt(p.final_price)} so'm</span>
                        ${stockEl}
                    </span>
                </div>`;
            }).join('');
        }
        searchDropdown.classList.add('open');
    }

    function closeDropdown() { searchDropdown.classList.remove('open'); focusedIdx = -1; }

    function setFocus(idx) {
        const els = searchDropdown.querySelectorAll('.sd-item');
        els.forEach(e => e.classList.remove('focused'));
        if (idx >= 0 && idx < els.length) {
            els[idx].classList.add('focused');
            els[idx].scrollIntoView({ block: 'nearest' });
            focusedIdx = idx;
        }
    }

    searchDropdown.addEventListener('mousedown', async function(e) {
        const item = e.target.closest('.sd-item');
        if (!item || item.dataset.out === '1') return;
        e.preventDefault();
        const id = parseInt(item.dataset.id, 10);
        closeDropdown();
        searchInput.value = '';
        await addProduct(id);
        searchInput.focus();
        refresh();
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setFocus(focusedIdx + 1 < dropdownItems.length ? focusedIdx + 1 : 0); return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            setFocus(focusedIdx - 1 >= 0 ? focusedIdx - 1 : dropdownItems.length - 1); return;
        }
        if (e.key === 'Escape') { closeDropdown(); return; }
        if (e.key === 'Enter') {
            e.preventDefault(); clearTimeout(searchTimer);
            if (searchDropdown.classList.contains('open') && focusedIdx >= 0) {
                const f = searchDropdown.querySelector('.sd-item.focused');
                if (f && f.dataset.out !== '1') {
                    const id = parseInt(f.dataset.id, 10);
                    closeDropdown(); searchInput.value = '';
                    addProduct(id).then(() => { searchInput.focus(); refresh(); });
                    return;
                }
            }
            doSearch(true);
        }
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        if (!searchInput.value.trim()) { closeDropdown(); refresh(); return; }
        searchTimer = setTimeout(() => doSearch(false), 250);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.search-wrap')) closeDropdown();
    });

    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim() && dropdownItems.length)
            searchDropdown.classList.add('open');
    });

    /* ── Search core ────────────────────────────────────────── */

    async function doSearch(isEnter) {
        const q = searchInput.value.trim();
        if (!q) { closeDropdown(); refresh(); return; }

        searchDropdown.innerHTML = '<div class="sd-loading"><i class="bi bi-arrow-repeat" style="animation:spin .6s linear infinite;display:inline-block"></i> Qidirilmoqda...</div>';
        searchDropdown.classList.add('open');

        try {
            const p    = new URLSearchParams({ q, category_id: activeCatId, show_all: '0' });
            const res  = await fetch(`${SEARCH_URL}?${p}`, { headers: { Accept: 'application/json' } });
            if (!res.ok) { openDropdown([]); return; }
            const items = (await res.json()).items ?? [];

            if (isEnter && items.length === 1 && items[0].barcode && q === items[0].barcode) {
                closeDropdown(); searchInput.value = '';
                await addProduct(items[0].id); searchInput.focus(); refresh(); return;
            }
            if (isEnter && items.length === 1 && items[0].stock_quantity > 0) {
                closeDropdown(); searchInput.value = '';
                await addProduct(items[0].id); searchInput.focus(); refresh(); return;
            }

            openDropdown(items);
            renderProducts(items);
        } catch { openDropdown([]); }
    }

    document.getElementById('btnClearSearch').addEventListener('click', () => {
        searchInput.value = ''; closeDropdown(); searchInput.focus(); refresh();
    });

    /* ── Category pills ─────────────────────────────────────── */

    document.getElementById('catPills').addEventListener('click', e => {
        const btn = e.target.closest('.cat-pill');
        if (!btn) return;
        activeCatId = parseInt(btn.dataset.cat, 10);
        setActiveCat(activeCatId);
        refresh();
    });

    function setActiveCat(catId) {
        let name = '';
        document.querySelectorAll('.cat-pill').forEach(b => {
            const on = parseInt(b.dataset.cat, 10) === catId;
            b.classList.toggle('active', on);
            if (on && catId !== 0) name = b.textContent.trim();
        });
        const badge = document.getElementById('activeCatBadge');
        badge.textContent = name;
        badge.style.display = name ? '' : 'none';
    }

    /* ── Product grid ───────────────────────────────────────── */

    function setProdLoading() {
        document.getElementById('prodBody').innerHTML =
            '<div class="prod-state"><i class="bi bi-arrow-repeat" style="animation:spin .8s linear infinite"></i><span>Yuklanmoqda...</span></div>';
    }

    function renderProducts(items) {
        const body = document.getElementById('prodBody');
        const cnt  = document.getElementById('prodCountBadge');

        if (!items.length) {
            body.innerHTML = '<div class="prod-state"><i class="bi bi-search"></i><span>Mahsulot topilmadi</span></div>';
            cnt.textContent = '';
            return;
        }

        cnt.textContent = items.length + ' ta mahsulot';

        body.innerHTML = `<div class="prod-grid">${items.map(p => {
            const out      = p.stock_quantity <= 0;
            const lowStock = !out && p.stock_quantity <= 5;
            const cls      = 'prod-card' + (out ? ' out' : '');
            const click    = out ? '' : `onclick="addProduct(${p.id})"`;

            const stockEl = out
                ? `<span class="badge bg-danger" style="font-size:10px">Tugagan</span>`
                : lowStock
                    ? `<span class="badge bg-warning text-dark" style="font-size:10px">⚠ ${p.stock_quantity}</span>`
                    : `<span style="font-size:11px;color:#b0bec5">${p.stock_quantity} ta</span>`;

            const badgeEl = lowStock
                ? `<span class="prod-card-badge"><span class="badge bg-warning text-dark" style="font-size:9px">Oz qoldi</span></span>`
                : '';

            return `<div class="${cls}" ${click} title="${esc(p.name)}">
                ${badgeEl}
                <div class="prod-card-icon${out?' out-icon':''}">
                    <i class="bi bi-box-seam${out?'':'-fill'}" style="color:${out?'#94a3b8':'#7c3aed'}"></i>
                </div>
                <div class="prod-card-name">${esc(p.name)}</div>
                <div class="prod-card-price${out?' out-price':''}">${fmt(p.final_price)} so'm</div>
                <div class="prod-card-stock">${stockEl}</div>
            </div>`;
        }).join('')}</div>`;
    }

    /* ── Select row ─────────────────────────────────────────── */

    const selCat  = document.getElementById('selCategory');
    const selProd = document.getElementById('selProduct');
    const btnAdd  = document.getElementById('btnSelAdd');

    selCat.addEventListener('change', async function() {
        const catId = parseInt(this.value, 10);
        selProd.innerHTML = '<option value="">Yuklanmoqda...</option>';
        selProd.disabled  = true; btnAdd.disabled = true;
        if (!catId) { selProd.innerHTML = '<option value="">— Mahsulot —</option>'; return; }
        try {
            const p    = new URLSearchParams({ q: '', category_id: catId, show_all: '1' });
            const res  = await fetch(`${SEARCH_URL}?${p}`, { headers: { Accept: 'application/json' } });
            const its  = res.ok ? (await res.json()).items ?? [] : [];
            selProd.innerHTML = '<option value="">— Mahsulot tanlang —</option>' +
                its.map(p => {
                    const label = p.stock_quantity <= 0
                        ? `${p.name} — Tugagan`
                        : `${p.name}  |  ${fmt(p.final_price)} so'm  (${p.stock_quantity} ta)`;
                    return `<option value="${p.id}" data-out="${p.stock_quantity<=0?1:0}">${label}</option>`;
                }).join('');
            selProd.disabled = its.length === 0;
        } catch { selProd.innerHTML = '<option value="">Xatolik</option>'; }
    });

    selProd.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        btnAdd.disabled = !this.value || opt.dataset.out === '1';
    });

    btnAdd.addEventListener('click', async function() {
        const pid = parseInt(selProd.value, 10);
        if (!pid) return;
        btnAdd.disabled = true; btnAdd.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin .5s linear infinite"></i>';
        await addProduct(pid);
        selCat.value = ''; selProd.innerHTML = '<option value="">— Mahsulot —</option>';
        selProd.disabled = true; btnAdd.innerHTML = '<i class="bi bi-plus-lg"></i> Qo\'shish'; btnAdd.disabled = true;
    });

    /* ── Keyboard shortcuts ─────────────────────────────────── */

    document.addEventListener('keydown', e => {
        if (e.key === 'F2') { e.preventDefault(); searchInput.select(); searchInput.focus(); }
    });

    /* ── Mobile tab switching ───────────────────────────────── */

    (function() {
        const posCart    = document.getElementById('posCart');
        const posBrowser = document.getElementById('posBrowser');
        const tabs       = document.querySelectorAll('.pos-mobile-tab');

        function switchTab(target) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.target === target));
            posCart.classList.toggle('tab-hidden',    target !== 'cart');
            posBrowser.classList.toggle('tab-hidden', target !== 'browser');
        }

        tabs.forEach(tab => tab.addEventListener('click', () => switchTab(tab.dataset.target)));

        // Default: show browser on mobile, cart on desktop
        if (window.innerWidth < 768) switchTab('browser');
    })();

    /* ── Init ───────────────────────────────────────────────── */

    renderCart();
    toggleCashFields();
    refresh();
</script>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection
