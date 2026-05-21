@extends('layouts.app')
@section('title', $customer->name . ' — Qarzlar')
@section('page_title', $customer->name)
@section('breadcrumb', 'Savdo / Qarzlar / ' . $customer->name)

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
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

{{-- Customer summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">Mijoz</div>
                <div class="fw-bold fs-6">{{ $customer->name }}</div>
                @if($customer->phone)
                    <div class="text-muted" style="font-size:13px">{{ $customer->phone }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">Jami qarz</div>
                <div class="fs-5 fw-bold">{{ number_format($totalDebt, 0, '.', ' ') }} <span class="text-muted fw-normal" style="font-size:13px">so'm</span></div>
                <div class="text-success" style="font-size:13px">To'langan: {{ number_format($totalPaid, 0, '.', ' ') }} so'm</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">Qoldiq</div>
                <div class="fs-4 fw-bold {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($remaining, 0, '.', ' ') }}
                    <span class="fw-normal text-muted" style="font-size:13px">so'm</span>
                </div>
                @if($remaining <= 0)
                    <div class="text-success" style="font-size:12px"><i class="bi bi-check-circle-fill me-1"></i>To'liq to'langan</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Debts table --}}
<div class="card shadow-sm">
    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
        <span>Qarzlar tarixi</span>
        <span class="badge bg-secondary">{{ $sales->count() }} ta</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Chek #</th>
                    <th>Sana</th>
                    <th>Kassir</th>
                    <th class="text-end">Jami</th>
                    <th class="text-end">To'langan</th>
                    <th class="text-end">Qoldiq</th>
                    <th class="text-center">Holat</th>
                    <th style="width:100px"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
                @php
                    $paid      = (float) $sale->debtPayments->sum('amount');
                    $bal       = (float) $sale->total_amount - $paid;
                    $cleared   = $bal <= 0;
                @endphp
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-size:13px">{{ $sale->created_at->format('d.m.Y H:i') }}</td>
                    <td style="font-size:13px">{{ $sale->cashier?->name ?? '—' }}</td>
                    <td class="text-end" style="font-size:13px">
                        {{ number_format($sale->total_amount, 0, '.', ' ') }}
                        <span class="text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-end text-success" style="font-size:13px">
                        {{ number_format($paid, 0, '.', ' ') }}
                        <span class="text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-end fw-semibold {{ $cleared ? 'text-success' : 'text-danger' }}" style="font-size:13px">
                        {{ number_format($bal, 0, '.', ' ') }}
                        <span class="fw-normal text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-center">
                        @if($cleared)
                            <span class="badge bg-success">To'langan</span>
                        @else
                            <span class="badge bg-danger">Qarzdor</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('backend.debts.show', $sale) }}"
                           class="btn btn-sm btn-outline-secondary" title="Ko'rish / To'lov">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-credit-card-2-back fs-3 d-block mb-2 text-secondary"></i>
                        Qarz topilmadi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
