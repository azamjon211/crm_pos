@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Xaridlar</h4>
        <a href="{{ route('backend.purchases.create') }}" class="btn btn-primary">+ Xarid kiritish</a>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Mahsulot</th><th>Yetkazuvchi</th><th>Miqdor</th><th>Narx</th><th>Sana</th><th></th></tr></thead>
        <tbody>
        @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->id }}</td>
                <td>{{ $purchase->product->name ?? '-' }}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td>{{ $purchase->quantity }}</td>
                <td>{{ number_format($purchase->total_cost, 0, '.', ' ') }}</td>
                <td>{{ $purchase->purchased_at?->format('d.m.Y') }}</td>
                <td>
                    <a href="{{ route('backend.purchases.edit', $purchase) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                    <form method="POST" action="{{ route('backend.purchases.destroy', $purchase) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('O\'chirasizmi?')">O'chirish</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $purchases->links() }}
@endsection
