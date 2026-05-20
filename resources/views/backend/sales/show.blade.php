@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Sotuv #{{ $sale->id }}</h4>
        <a href="{{ route('backend.sales.index') }}" class="btn btn-secondary">Orqaga</a>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><th>Kassir</th><td>{{ $sale->cashier->name ?? '-' }}</td></tr>
                <tr><th>Mijoz</th><td>{{ $sale->customer->name ?? 'Anonim' }}</td></tr>
                <tr><th>To'lov</th><td>{{ \App\Models\Sale::PAYMENT_TYPES[$sale->payment_type] }}</td></tr>
                <tr><th>Jami</th><td>{{ number_format($sale->total_amount, 0, '.', ' ') }}</td></tr>
                <tr><th>Sana</th><td>{{ $sale->created_at->format('d.m.Y H:i') }}</td></tr>
            </table>
        </div>
    </div>
    <h5>Mahsulotlar</h5>
    <table class="table table-bordered">
        <thead><tr><th>Mahsulot</th><th>Miqdor</th><th>Narx</th><th>Jami</th></tr></thead>
        <tbody>
        @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0, '.', ' ') }}</td>
                <td>{{ number_format($item->line_total, 0, '.', ' ') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
