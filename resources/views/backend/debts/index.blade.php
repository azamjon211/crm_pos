@extends('layouts.app')
@section('title', 'Qarzdor mijozlar — CRM POS')
@section('page_title', 'Qarzdor mijozlar')
@section('breadcrumb', 'Savdo / Qarzlar')

@section('content')

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">Qarzdor mijozlar</div>
                <div class="fs-4 fw-bold">{{ $customerCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">Jami qarz summasi</div>
                <div class="fs-5 fw-bold">{{ number_format($summaryTotal, 0, '.', ' ') }} <span class="text-muted fw-normal" style="font-size:14px">so'm</span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em">To'lanmagan qoldiq</div>
                <div class="fs-5 fw-bold text-danger">{{ number_format($summaryRemaining, 0, '.', ' ') }} <span class="fw-normal text-muted" style="font-size:14px">so'm</span></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    @if($showAll)
        <a href="{{ route('backend.debts.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-funnel me-1"></i>Faqat faollar
        </a>
    @else
        <a href="{{ route('backend.debts.index', ['show_cleared' => 1]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye me-1"></i>Hammasini ko'rish
        </a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Mijoz</th>
                    <th>Telefon</th>
                    <th class="text-center">Qarzlar soni</th>
                    <th class="text-end">Jami qarz</th>
                    <th class="text-end">To'langan</th>
                    <th class="text-end">Qoldiq</th>
                    <th class="text-muted" style="font-size:12px">Oxirgi qarz</th>
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($customerDebts as $i => $row)
                @php $cleared = $row['remaining'] <= 0; @endphp
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $i + 1 }}</td>
                    <td>
                        @if($row['customer'])
                            <span class="fw-semibold">{{ $row['customer']->name }}</span>
                        @else
                            <span class="text-muted fst-italic">Anonim</span>
                        @endif
                    </td>
                    <td style="font-size:13px">
                        {{ $row['customer']?->phone ?? '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $row['debt_count'] }}</span>
                    </td>
                    <td class="text-end" style="font-size:13px">
                        {{ number_format($row['total_debt'], 0, '.', ' ') }}
                        <span class="text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-end text-success" style="font-size:13px">
                        {{ number_format($row['total_paid'], 0, '.', ' ') }}
                        <span class="text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-end fw-bold {{ $cleared ? 'text-success' : 'text-danger' }}" style="font-size:13px">
                        {{ number_format($row['remaining'], 0, '.', ' ') }}
                        <span class="fw-normal text-muted" style="font-size:11px">so'm</span>
                    </td>
                    <td class="text-muted" style="font-size:12px">
                        {{ $row['last_at'] ? \Carbon\Carbon::parse($row['last_at'])->format('d.m.Y') : '—' }}
                    </td>
                    <td>
                        @if($row['customer'])
                            <a href="{{ route('backend.debts.customer', $row['customer']) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-credit-card-2-back fs-3 d-block mb-2 text-secondary"></i>
                        Qarzdor mijoz topilmadi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
