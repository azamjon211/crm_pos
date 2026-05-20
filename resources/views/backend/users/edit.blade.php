@extends('layouts.app')
@section('title', 'Foydalanuvchi tahrirlash — CRM POS')
@section('page_title', 'Foydalanuvchi tahrirlash')
@section('breadcrumb', 'Foydalanuvchilar / Tahrirlash')

@section('content')
<div style="max-width:520px">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('backend.users.update', $user) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Ism <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}" required autocomplete="off">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Yangi parol <span class="text-muted fw-normal">(o'zgartirmasangiz bo'sh qoldiring)</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password" minlength="6" placeholder="bo'sh = o'zgarmaydi">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @if($shops->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label">Do'kon <span class="text-danger">*</span></label>
                        <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror" required>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ old('shop_id', $user->shop_id) == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('shop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                           value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Faol</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
