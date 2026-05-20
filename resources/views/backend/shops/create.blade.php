@extends('layouts.app')
@section('title', 'Do\'kon qo\'shish — CRM POS')
@section('page_title', 'Do\'kon qo\'shish')
@section('breadcrumb', 'Do\'konlar / Qo\'shish')

@section('content')
<div style="max-width:520px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('backend.shops.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Do'kon nomi <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Masalan: Baraka Market" autofocus required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Manzil</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="ixtiyoriy">
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+998 90 000 00 00">
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Faol</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('backend.shops.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
