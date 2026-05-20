@extends('layouts.app')
@section('title', 'Kategoriyalar — CRM POS')
@section('page_title', 'Kategoriyalar')
@section('breadcrumb', 'Katalog / Kategoriyalar')

@section('topbar_actions')
    <a href="{{ route('backend.categories.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Kategoriya qo'shish
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
                   <!--  <th>Ota kategoriya</th> -->
                    <th class="text-center">Mahsulotlar</th>
                    <th class="text-center">Holat</th>
                    <th class="text-end" style="width:130px">Amallar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $cat->id }}</td>
                    <td class="fw-medium">
                        @if($cat->parent_id)
                            <span class="text-muted me-1" style="font-size:12px">└─</span>
                        @endif
                        {{ $cat->name }}
                    </td>
                    <!--<td class="text-muted" style="font-size:13px">{{ $cat->parent->name ?? '—' }}</td> -->
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $cat->products_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $cat->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('backend.categories.edit', $cat) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('backend.categories.destroy', $cat) }}" style="display:inline"
                              onsubmit="return confirm('{{ $cat->name }} kategoriyasini o\'chirasizmi?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-folder2-open fs-3 d-block mb-2 text-secondary"></i>
                        Hozircha kategoriyalar yo'q
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
