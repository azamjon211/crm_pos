@extends('layouts.app')
@section('title', 'Sotuvlar — CRM POS')
@section('page_title', 'Sotuvlar')
@section('breadcrumb', 'Savdo / Sotuvlar')

@section('content')
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Dan</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Gacha</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em">To'lov turi</label>
                <select name="payment_type" class="form-select form-select-sm">
                    <option value="">Barcha to'lovlar</option>
                    @foreach($paymentTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('payment_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filtr
                </button>
                @if(request()->hasAny(['from','to','payment_type']))
                    <a href="{{ route('backend.sales.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kassir</th>
                    <th>Mijoz</th>
                    <th class="text-end">Summa</th>
                    <th class="text-center">To'lov</th>
                    <th>Sana</th>
                    <th class="text-end" style="width:140px">Amallar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $sale->id }}</td>
                    <td style="font-size:13px">{{ $sale->cashier->name ?? '—' }}</td>
                    <td>
                        @if($sale->customer)
                            <span class="fw-medium">{{ $sale->customer->name }}</span>
                        @else
                            <span class="text-muted">Anonim</span>
                        @endif
                    </td>
                    <td class="text-end fw-semibold">{{ number_format($sale->total_amount, 0, '.', ' ') }} <span class="text-muted fw-normal" style="font-size:11px">so'm</span></td>
                    <td class="text-center">
                        @php $pt = $sale->payment_type; @endphp
                        <span class="badge {{ $pt === 'cash' ? 'bg-success' : ($pt === 'card' ? 'bg-primary' : 'bg-info text-dark') }}">
                            {{ \App\Models\Sale::PAYMENT_TYPES[$pt] ?? $pt }}
                        </span>
                    </td>
                    <td style="font-size:13px">{{ $sale->created_at->format('d.m.Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('backend.sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('backend.sales.return', $sale) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-arrow-return-left"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-receipt fs-3 d-block mb-2 text-secondary"></i>
                        Sotuvlar topilmadi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $sales->links() }}</div>
@endsection
