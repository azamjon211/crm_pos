@extends('layouts.app')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">Xarid kiritish</div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.purchases.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Mahsulot</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- Tanlang --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Yetkazuvchi</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- Tanlang --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label">Miqdor</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" step="0.01" min="0.01" required>
                </div>
                <div class="col mb-3">
                    <label class="form-label">Tan narxi</label>
                    <input type="number" name="unit_cost" class="form-control" value="{{ old('unit_cost') }}" step="0.01" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sana</label>
                <input type="datetime-local" name="purchased_at" class="form-control" value="{{ old('purchased_at') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Izoh</label>
                <textarea name="note" class="form-control">{{ old('note') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Saqlash</button>
            <a href="{{ route('backend.purchases.index') }}" class="btn btn-secondary">Orqaga</a>
        </form>
    </div>
</div>
@endsection
