@extends('layouts.app')
@section('title', 'Magazin tanlash — CRM POS')
@section('page_title', 'Magazin tanlash')
@section('breadcrumb', 'Magazin tanlash')

@section('content')
<div style="max-width:400px;margin:60px auto 0">
    <div class="card">
        <div class="card-header fw-semibold">
            <i class="bi bi-shop me-2"></i>Ish magazinini tanlang
        </div>
        <div class="card-body">
            @if(session('shop_id'))
            <p class="text-muted" style="font-size:13px">
                Hozirgi magazin: <strong>{{ session('shop_name') }}</strong>
            </p>
            @endif
            <form method="POST" action="{{ route('backend.switch-shop.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Magazin <span class="text-danger">*</span></label>
                    <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror" required>
                        <option value="">— Tanlang —</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ session('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('shop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Kirish
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
