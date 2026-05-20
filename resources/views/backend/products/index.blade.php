@extends('layouts.app')
@section('title', 'Mahsulotlar — CRM POS')
@section('page_title', 'Mahsulotlar')
@section('breadcrumb', 'Katalog / Mahsulotlar')

@section('topbar_actions')
    <a href="{{ route('backend.products.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Mahsulot qo'shish
    </a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nom, barcode, SKU bo'yicha qidirish..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Barcha kategoriyalar</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Filtr
                </button>
            </div>
            @if(request()->hasAny(['search','category_id']))
                <div class="col-md-1">
                    <a href="{{ route('backend.products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Mahsulot</th>
                    <th>Kategoriya</th>
                    <th class="text-end">Narx</th>
                    <th class="text-end">Chegirma</th>
                    <th class="text-center">Zaxira</th>
                    <th class="text-center">Holat</th>
                    <th class="text-end" style="width:120px">Amallar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <div class="fw-medium">{{ $product->name }}</div>
                        @if($product->barcode)
                            <div style="font-size:11.5px;color:#94a3b8">{{ $product->barcode }}</div>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:13px">{{ $product->category->name ?? '—' }}</td>
                    <td class="text-end fw-medium">{{ number_format($product->sale_price, 0, '.', ' ') }}</td>
                    <td class="text-center">
                        @if($product->discount > 0)
                            <span class="badge bg-warning text-dark">{{ $product->discount }}%</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $product->stock_quantity <= 5 ? 'bg-danger' : ($product->stock_quantity <= 20 ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('backend.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('backend.products.destroy', $product) }}" style="display:inline"
                              onsubmit="return confirm('{{ addslashes($product->name) }} ni o\'chirasizmi?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-3 d-block mb-2 text-secondary"></i>
                        Mahsulotlar topilmadi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
