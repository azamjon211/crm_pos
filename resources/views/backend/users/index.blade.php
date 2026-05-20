@extends('layouts.app')
@section('title', 'Foydalanuvchilar — CRM POS')
@section('page_title', 'Foydalanuvchilar')

@section('topbar_actions')
    <a href="{{ route('backend.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Foydalanuvchi qo'shish
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ism</th>
                    <th>Login</th>
                    @if($isSuperAdmin)<th>Do'kon</th>@endif
                    <th class="text-center">Rol</th>
                    <th class="text-center">Holat</th>
                    <th class="text-end" style="width:100px">Amallar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="fw-medium">{{ $user->name }}</td>
                    <td style="font-size:13px;color:#64748b">{{ $user->username }}</td>
                    @if($isSuperAdmin)
                        <td style="font-size:13px">{{ $user->shop->name ?? '—' }}</td>
                    @endif
                    <td class="text-center">
                        @php $roleColors = ['superadmin'=>'bg-dark','admin'=>'bg-danger','manager'=>'bg-warning text-dark','cashier'=>'bg-secondary']; @endphp
                        <span class="badge {{ $roleColors[$user->role] ?? 'bg-secondary' }}">
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
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('backend.users.destroy', $user) }}" style="display:inline"
                                  onsubmit="return confirm('{{ $user->name }} ni o\'chirasizmi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isSuperAdmin ? 6 : 5 }}" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                        Foydalanuvchilar yo'q
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
