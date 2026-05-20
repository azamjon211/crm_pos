@extends('layouts.app')
@section('title', 'Do\'konlar — CRM POS')
@section('page_title', 'Do\'konlar')

@section('topbar_actions')
    <a href="{{ route('backend.shops.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Do'kon qo'shish
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nomi</th>
                    <th>Manzil / Telefon</th>
                    <th class="text-center">Foydalanuvchilar</th>
                    <th class="text-center">Mahsulotlar</th>
                    <th class="text-center">Sotuvlar</th>
                    <th class="text-center">Holat</th>
                    <th class="text-end" style="width:100px">Amallar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($shops as $shop)
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $shop->id }}</td>
                    <td class="fw-medium">{{ $shop->name }}</td>
                    <td style="font-size:13px">
                        @if($shop->address)<div>{{ $shop->address }}</div>@endif
                        @if($shop->phone)<div class="text-muted">{{ $shop->phone }}</div>@endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $shop->users_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $shop->products_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $shop->sales_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $shop->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $shop->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('backend.shops.edit', $shop) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('backend.shops.destroy', $shop) }}" style="display:inline"
                              onsubmit="return confirm('{{ $shop->name }} ni o\'chirasizmi?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-shop fs-3 d-block mb-2 text-secondary"></i>
                        Hozircha do'konlar yo'q
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $shops->links() }}</div>
@endsection
