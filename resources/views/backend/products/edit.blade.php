@extends('layouts.app')
@section('title', 'Mahsulot tahrirlash — CRM POS')
@section('page_title', 'Mahsulot tahrirlash')
@section('breadcrumb', 'Katalog / Mahsulotlar / Tahrirlash')

@section('content')
<div style="max-width:640px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('backend.products.update', $product) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Kategoriya <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">— Tanlang —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mahsulot nomi <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name) }}" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3 mb-3">
                    <div class="col">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="col">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col">
                        <label class="form-label">Tannarx <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="cost_price" class="form-control"
                                   value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" required>
                            <span class="input-group-text">so'm</span>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Sotuv narxi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="sale_price" class="form-control"
                                   value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" required>
                            <span class="input-group-text">so'm</span>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col">
                        <label class="form-label">Chegirma</label>
                        <div class="input-group">
                            <input type="number" name="discount" class="form-control"
                                   value="{{ old('discount', $product->discount) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Zaxira</label>
                        <input type="number" name="stock_quantity" class="form-control"
                               value="{{ old('stock_quantity', $product->stock_quantity) }}" step="0.01" min="0">
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Faol</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('backend.products.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
