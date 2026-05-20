@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $customer->name }}</h4>
        <a href="{{ route('backend.customers.index') }}" class="btn btn-secondary">Orqaga</a>
    </div>
    <div class="row">
        <div class="col-md-4">
            <table class="table table-bordered">
                <tr><th>Telefon</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                <tr><th>Balans</th><td>{{ number_format($customer->balance, 0, '.', ' ') }}</td></tr>
                <tr><th>Holat</th><td>{{ $customer->is_active ? 'Faol' : 'Nofaol' }}</td></tr>
            </table>
        </div>
    </div>
    <h5>Oxirgi sotuvlar</h5>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Summa</th><th>Sana</th></tr></thead>
        <tbody>
        @foreach($customer->sales as $sale)
            <tr>
                <td><a href="{{ route('backend.sales.show', $sale) }}">#{{ $sale->id }}</a></td>
                <td>{{ number_format($sale->total_amount, 0, '.', ' ') }}</td>
                <td>{{ $sale->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
