@extends('layouts.app')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-header">Sotuv #{{ $sale->id }} — Qaytarish</div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.sales.process-return', $sale) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Mahsulot</label>
                    <select name="sale_item_id" class="form-select" required>
                        <option value="">-- Tanlang --</option>
                        @foreach($sale->saleItems as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->product->name ?? '-' }} ({{ $item->quantity }} dona)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Miqdor</label>
                    <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
                </div>
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
