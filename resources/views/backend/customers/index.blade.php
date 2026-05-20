@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Mijozlar</h4>
        <a href="{{ route('backend.customers.create') }}" class="btn btn-primary">+ Qo'shish</a>
    </div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Ism yoki telefon..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Qidirish</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>Nom</th><th>Telefon</th><th>Sotuvlar</th><th>Balans</th><th></th></tr></thead>
        <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ $customer->sales_count }}</td>
                <td>{{ number_format($customer->balance, 0, '.', ' ') }}</td>
                <td>
                    <a href="{{ route('backend.customers.edit', $customer) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                    <form method="POST" action="{{ route('backend.customers.destroy', $customer) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('O\'chirasizmi?')">O'chirish</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $customers->links() }}
@endsection
