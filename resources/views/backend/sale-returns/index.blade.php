@extends('layouts.app')
@section('content')
    <h4 class="mb-3">Qaytarishlar</h4>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Sotuv</th><th>Mahsulot</th><th>Miqdor</th><th>Turi</th><th>Sana</th></tr></thead>
        <tbody>
        @foreach($returns as $return)
            <tr>
                <td>{{ $return->id }}</td>
                <td>#{{ $return->sale_id }}</td>
                <td>{{ $return->product->name ?? '-' }}</td>
                <td>{{ $return->quantity }}</td>
                <td>{{ \App\Models\SaleReturn::TYPES[$return->return_type] }}</td>
                <td>{{ $return->returned_at?->format('d.m.Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $returns->links() }}
@endsection
