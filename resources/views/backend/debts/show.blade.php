@extends('layouts.app')
@section('title', 'Qarz #' . $sale->id . ' — CRM POS')
@section('page_title', 'Qarz #' . $sale->id)
@section('breadcrumb', 'Savdo / Qarzlar / #' . $sale->id)

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('backend.debts.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Orqaga
    </a>
</div>

<div class="row g-3">

    {{-- Left: sale info + items --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header fw-semibold">Sotuv ma'lumoti</div>
            <div class="card-body pb-2">
                <dl class="row mb-0" style="font-size:14px">
                    <dt class="col-5 text-muted fw-normal">Mijoz</dt>
                    <dd class="col-7 fw-medium">{{ $sale->customer?->name ?? 'Anonim' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Kassir</dt>
                    <dd class="col-7">{{ $sale->cashier?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Sana</dt>
                    <dd class="col-7">{{ $sale->created_at->format('d.m.Y H:i') }}</dd>

                    <dt class="col-5 text-muted fw-normal">Jami summa</dt>
                    <dd class="col-7 fw-semibold">{{ number_format($sale->total_amount, 0, '.', ' ') }} so'm</dd>

                    <dt class="col-5 text-muted fw-normal">To'langan</dt>
                    <dd class="col-7 text-success fw-semibold">{{ number_format($sale->debtPayments->sum('amount'), 0, '.', ' ') }} so'm</dd>

                    <dt class="col-5 text-muted fw-normal">Qoldiq</dt>
                    <dd class="col-7 fw-bold {{ $sale->isDebtCleared() ? 'text-success' : 'text-danger' }}">
                        {{ number_format($sale->debtBalance(), 0, '.', ' ') }} so'm
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-semibold">Mahsulotlar</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:13px">
                    <thead class="table-light">
                        <tr>
                            <th>Mahsulot</th>
                            <th class="text-end">Miqdor</th>
                            <th class="text-end">Narx</th>
                            <th class="text-end">Jami</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleItems as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? '—' }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 0, '.', ' ') }}</td>
                            <td class="text-end fw-medium">{{ number_format($item->line_total, 0, '.', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: pay form + history --}}
    <div class="col-lg-7">

        @if(!$sale->isDebtCleared())
        <div class="card mb-3">
            <div class="card-header fw-semibold">To'lov qilish</div>
            <div class="card-body">
                <form method="POST" action="{{ route('backend.debts.pay', $sale) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-sm-5">
                            <label class="form-label mb-1" style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Summa <span class="text-danger">*</span></label>
                            <input type="number" name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   step="0.01" min="0.01" max="{{ $sale->debtBalance() }}"
                                   value="{{ old('amount', $sale->debtBalance()) }}" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label mb-1" style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Sana</label>
                            <input type="date" name="paid_at" class="form-control"
                                   value="{{ old('paid_at', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-sm-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-lg me-1"></i>Qabul
                            </button>
                        </div>
                        <div class="col-12">
                            <input type="text" name="note" class="form-control"
                                   placeholder="Izoh (ixtiyoriy)"
                                   value="{{ old('note') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill fs-5"></i>
            Bu qarz to'liq to'langan.
        </div>
        @endif

        <div class="card">
            <div class="card-header fw-semibold">To'lovlar tarixi</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:13px">
                    <thead class="table-light">
                        <tr>
                            <th>Sana</th>
                            <th class="text-end">Summa</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->debtPayments->sortByDesc('paid_at') as $payment)
                        <tr>
                            <td>{{ $payment->paid_at->format('d.m.Y') }}</td>
                            <td class="text-end fw-medium text-success">{{ number_format($payment->amount, 0, '.', ' ') }} so'm</td>
                            <td class="text-muted">{{ $payment->note ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-clock-history d-block fs-4 mb-1"></i>
                                To'lovlar yo'q
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
