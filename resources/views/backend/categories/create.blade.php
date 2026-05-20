@extends('layouts.app')
@section('title', 'Kategoriya qo\'shish — CRM POS')
@section('page_title', 'Kategoriya qo\'shish')
@section('breadcrumb', 'Katalog / Kategoriyalar / Qo\'shish')

@section('content')
<div style="max-width:520px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('backend.categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Masalan: Ichimliklar" autofocus required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Ota kategoriya <span class="text-muted fw-normal">(ixtiyoriy)</span></label>
                    <select name="parent_id" class="form-select">
                        <option value="">— Asosiy kategoriya —</option>
                        @foreach($parentCategories as $pCat)
                            <option value="{{ $pCat->id }}" {{ old('parent_id') == $pCat->id ? 'selected' : '' }}>
                                {{ $pCat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Faol</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('backend.categories.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
