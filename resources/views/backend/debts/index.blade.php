@extends('layouts.app')
@section('title', 'Qarzlar — CRM POS')
@section('page_title', 'Qarzlar')
@section('breadcrumb', 'Savdo / Qarzlar')

@section('content')
<div class="d-flex justify-content-end mb-3">
    @if(request()->boolean('show_cleared'))
        <a href="{{ route('backend.debts.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-funnel me-1"></i>Faqat faollar
        </a>
    @else
        <a href="{{ route('backend.debts.index', ['show_cleared' => 1]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye me-1"></i>Hammasini ko'rish
        </a>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mijoz</th>
                    <th>Sana</th>
                    <th class="text-end">Jami summa</th>
                    <th class="text-end">To'langan</th>
                    <th class="text-end">Qoldiq</th>
                    <th class="text-center">Holat</th>
                    <th class="text-end" style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($debts as $sale)
                @php
                    $paid      = (float) $sale->debtPayments->sum('amount');
                    $remaining = (float) $sale->total_amount - $paid;
                    $cleared   = $remaining <= 0;
                @endphp
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $sale->id }}</td>
                    <td>
                        @if($sale->customer)
                            <span class="fw-medium">{{ $sale->customer->name }}</span>
                        @else
                            <span class="text-muted">Anonim</span>
                        @endif
                    </td>
                    <td style="font-size:13px">{{ $sale->created_at->format('d.m.Y') }}</td>
                    <td class="text-end">{{ number_format($sale->total_amount, 0, '.', ' ') }} <span class="text-muted" style="font-size:11px">so'm</span></td>
                    <td class="text-end text-success">{{ number_format($paid, 0, '.', ' ') }} <span class="text-muted" style="font-size:11px">so'm</span></td>
                    <td class="text-end fw-semibold {{ $cleared ? 'text-success' : 'text-danger' }}">
                        {{ number_format($remaining, 0, '.', ' ') }} <span class="fw-normal" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-center">
                        @if($cleared)
                            <span class="badge bg-success">To'langan</span>
                        @else
                            <span class="badge bg-danger">Qarzdor</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('backend.debts.show', $sale) }}" class="btn btn-sm btn-outline-secondary">
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

<div class="mt-3">{{ $debts->links() }}</div>
@endsection
