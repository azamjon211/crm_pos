@extends('layouts.app')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-header">Sotuv #{{ $sale->id }} — Qaytarish</div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.sales.process-return', $sale) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Mahsulot</label>
                    <select name="sale_item_id" id="saleItemSelect" class="form-select" required>
                        <option value="">-- Tanlang --</option>
                        @foreach($sale->saleItems as $item)
                            <option value="{{ $item->id }}" data-max="{{ (float) $item->quantity }}">
                                {{ $item->product->name ?? '-' }} ({{ $item->quantity }} dona)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Miqdor <small id="maxHint" class="text-muted"></small></label>
                    <div class="qty-wrap">
                        <button type="button" class="qty-btn" id="qtyMinus">−</button>
                        <input type="number" name="quantity" id="qtyInput"
                               class="qty-input form-control" step="any" required>
                        <button type="button" class="qty-btn" id="qtyPlus">+</button>
                    </div>
                </div>

                <style>
                    .qty-wrap { display: flex; align-items: center; gap: 6px; }
                    .qty-btn {
                        width: 36px; height: 38px; flex-shrink: 0;
                        border: 1.5px solid #dee2e6; border-radius: 6px;
                        background: #f8f9fa; color: #495057;
                        font-size: 18px; font-weight: 700; cursor: pointer;
                        display: flex; align-items: center; justify-content: center;
                        transition: all .15s;
                    }
                    .qty-btn:hover { background: #6366f1; border-color: #6366f1; color: #fff; }
                    .qty-input {
                        width: 100px; text-align: center;
                        font-weight: 600; font-size: 15px;
                    }
                    .qty-input::-webkit-outer-spin-button,
                    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
                    .qty-input[type=number] { -moz-appearance: textfield; }
                </style>

                <script>
                    const qtyInput  = document.getElementById('qtyInput');
                    const qtyMinus  = document.getElementById('qtyMinus');
                    const qtyPlus   = document.getElementById('qtyPlus');
                    const itemSelect = document.getElementById('saleItemSelect');
                    const maxHint   = document.getElementById('maxHint');

                    let maxQty = Infinity;

                    itemSelect.addEventListener('change', function () {
                        const opt = this.options[this.selectedIndex];
                        maxQty = opt.dataset.max ? parseFloat(opt.dataset.max) : Infinity;
                        maxHint.textContent = maxQty !== Infinity ? `(max: ${maxQty})` : '';
                        qtyInput.value = maxQty !== Infinity ? 1 : '';
                    });

                    qtyMinus.addEventListener('click', function () {
                        const cur = parseFloat(qtyInput.value) || 1;
                        const next = cur - 1;
                        if (next <= 0) return;
                        qtyInput.value = next;
                    });

                    qtyPlus.addEventListener('click', function () {
                        const cur = parseFloat(qtyInput.value) || 0;
                        const next = cur + 1;
                        if (next > maxQty) return;
                        qtyInput.value = next;
                    });

                    qtyInput.addEventListener('input', function () {
                        const val = parseFloat(this.value.replace(',', '.')) || 0;
                        if (val <= 0) { this.value = ''; return; }
                        if (val > maxQty) { this.value = maxQty; }
                    });

                    qtyInput.addEventListener('blur', function () {
                        const raw = this.value.replace(',', '.');
                        const val = parseFloat(raw);
                        if (!isNaN(val)) this.value = val;
                    });
                </script>
                <div class="mb-3">
                    <label class="form-label">Qaytarish turi</label>
                    <select name="return_type" class="form-select">
                        <option value="refund">Pul qaytarish</option>
                        <option value="exchange">Almashtirish</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sabab</label>
                    <textarea name="reason" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-warning">Qaytarish</button>
                <a href="{{ route('backend.sales.show', $sale) }}" class="btn btn-secondary">Orqaga</a>
            </form>
        </div>
    </div>
@endsection
