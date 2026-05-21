@extends('layouts.app')
@section('content')
    <div class="row mb-3">
        <div class="col">
            <h4>Kunlik yopish</h4>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Bugungi savdo</h6>
                    <h4>{{ number_format($todayStats['totalSales'], 0, '.', ' ') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6>Qaytarishlar</h6>
                    <h4>{{ number_format($todayStats['totalReturns'], 0, '.', ' ') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $todayStats['profit'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                <div class="card-body">
                    <h6>Foyda</h6>
                    <h4>{{ number_format($todayStats['profit'], 0, '.', ' ') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('backend.daily-closing.close') }}" class="mb-4">
        @csrf
        <div class="d-flex gap-2 align-items-center">
            <input type="date" name="date" class="form-control" style="width:200px" value="{{ today()->toDateString() }}">
            <button type="submit" class="btn btn-danger">Kunni yopish</button>
            @if($todayClosing)
                <button type="submit" name="force" value="1" class="btn btn-warning">Qayta hisoblash</button>
            @endif
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sana</th>
                <th>Savdo</th>
                <th>Qaytarishlar</th>
                <th>Tannarx</th>
                <th>Foyda</th>
            </tr>
        </thead>
        <tbody>
        @foreach($closings as $closing)
            <tr>
                <td>{{ $closing->date->format('d.m.Y') }}</td>
                <td>{{ number_format($closing->total_sales, 0, '.', ' ') }}</td>
                <td>{{ number_format($closing->total_returns, 0, '.', ' ') }}</td>
                <td>{{ number_format($closing->total_cost, 0, '.', ' ') }}</td>
                <td class="{{ $closing->total_profit < 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                    {{ number_format($closing->total_profit, 0, '.', ' ') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $closings->links() }}
@endsection
