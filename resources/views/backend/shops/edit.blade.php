@extends('layouts.app')
@section('title', 'Do\'kon tahrirlash — CRM POS')
@section('page_title', $shop->name . ' — tahrirlash')
@section('breadcrumb', 'Do\'konlar / Tahrirlash')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Ma'lumotlar</div>
            <div class="card-body">
                <form method="POST" action="{{ route('backend.shops.update', $shop) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Do'kon nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $shop->name) }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Manzil</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $shop->address) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $shop->phone) }}">
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                               value="1" {{ $shop->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Faol</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Saqlash
                        </button>
                        <a href="{{ route('backend.shops.index') }}" class="btn btn-outline-secondary">Orqaga</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Foydalanuvchilar</span>
                <a href="{{ route('backend.users.create', ['shop_id' => $shop->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Qo'shish
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ism</th>
                            <th>Login</th>
                            <th class="text-center">Rol</th>
                            <th class="text-center">Holat</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-medium">{{ $user->name }}</td>
                            <td style="font-size:13px;color:#64748b">{{ $user->username }}</td>
                            <td class="text-center">
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'manager' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $user->is_active ? 'Faol' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('backend.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Foydalanuvchilar yo'q</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
