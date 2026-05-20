@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Yetkazuvchilar</h4>
        <a href="{{ route('backend.suppliers.create') }}" class="btn btn-primary">+ Qo'shish</a>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Nom</th><th>Telefon</th><th>Xaridlar</th><th></th></tr></thead>
        <tbody>
        @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->phone ?? '-' }}</td>
                <td>{{ $supplier->purchases_count }}</td>
                <td>
                    <a href="{{ route('backend.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                    <form method="POST" action="{{ route('backend.suppliers.destroy', $supplier) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('O\'chirasizmi?')">O'chirish</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $suppliers->links() }}
@endsection
