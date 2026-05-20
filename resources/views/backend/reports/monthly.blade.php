@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Oylik hisobot — {{ $year }}</h4>
        <form method="GET" class="d-flex gap-2">
            <input type="number" name="year" class="form-control" value="{{ $year }}" style="width:100px">
            <button type="submit" class="btn btn-secondary">Ko'rish</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr><th>Oy</th><th>Savdo</th><th>Tannarx</th><th>Foyda</th></tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['year'] }}/{{ str_pad($row['month'], 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ number_format($row['total_sales'], 0, '.', ' ') }}</td>
                <td>{{ number_format($row['total_cost'], 0, '.', ' ') }}</td>
                <td>{{ number_format($row['profit'], 0, '.', ' ') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="table-dark">
        <tr>
            <th>Jami</th>
            <th>{{ number_format($totalSales, 0, '.', ' ') }}</th>
            <th>{{ number_format($totalCost, 0, '.', ' ') }}</th>
            <th>{{ number_format($profit, 0, '.', ' ') }}</th>
        </tr>
        </tfoot>
    </table>
@endsection
