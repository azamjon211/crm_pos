@extends('layouts.app')
@section('title', 'Dashboard — CRM POS')
@section('page_title', 'Dashboard')

@section('topbar_actions')
    <span style="font-size:12.5px;color:#6b7a8f"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d.m.Y') }}</span>
    <div style="width:1px;height:20px;background:#e4eaf2"></div>
    <a href="{{ route('backend.products.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Mahsulot qo'shish
    </a>
@endsection

@section('content')

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi kpi-blue card">
            <div class="kpi-label">Bugungi savdo</div>
            <div class="kpi-val">{{ number_format($todayStats['totalSales'], 0, '.', ' ') }}</div>
            <div class="kpi-sub">so'm tushum</div>
            <i class="bi bi-currency-exchange kpi-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi kpi-green card">
            <div class="kpi-label">Sof foyda</div>
            <div class="kpi-val">{{ number_format($todayStats['profit'], 0, '.', ' ') }}</div>
            <div class="kpi-sub">so'm foyda</div>
            <i class="bi bi-graph-up-arrow kpi-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi kpi-amber card">
            <div class="kpi-label">Bugungi tranzaksiya</div>
            <div class="kpi-val">{{ $todayCount }}</div>
            <div class="kpi-sub">ta sotuv</div>
            <i class="bi bi-bag-check kpi-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi kpi-rose card">
            <div class="kpi-label">Faol qarzlar</div>
            <div class="kpi-val">{{ $activeDebts->cnt ?? 0 }}</div>
            <div class="kpi-sub">{{ number_format($activeDebts->total ?? 0, 0, '.', ' ') }} so'm</div>
            <i class="bi bi-credit-card-2-back kpi-icon"></i>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="row g-3 mb-4">

    {{-- 7-day sales bar chart --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-bar-chart-fill text-primary me-2"></i>So'nggi 7 kunlik savdo</span>
                <span class="text-muted" style="font-size:12px">so'm</span>
            </div>
            <div class="card-body">
                <canvas id="weekSalesChart" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- Top products horizontal bar chart --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-trophy-fill text-warning me-2"></i>Top mahsulotlar</span>
                <span class="text-muted" style="font-size:12px">{{ now()->format('F Y') }}</span>
            </div>
            <div class="card-body">
                @if($topProducts->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted" style="min-height:180px">
                        <i class="bi bi-bar-chart fs-2 mb-2"></i>
                        <span style="font-size:13px">Bu oy savdo yo'q</span>
                    </div>
                @else
                    <canvas id="topProductsChart" height="180"></canvas>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Bottom row --}}
<div class="row g-3">

    {{-- Low stock --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                Kam qolgan mahsulotlar
            </div>
            <div style="overflow:hidden;border-radius:0 0 14px 14px">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Mahsulot nomi</th>
                            <th>Kategoriya</th>
                            <th class="text-center">Zaxira</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($lowStockProducts as $p)
                        <tr>
                            <td class="fw-600">{{ $p->name }}</td>
                            <td style="color:#6b7a8f;font-size:12.5px">{{ $p->category->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $p->stock_quantity <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $p->stock_quantity }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('backend.products.edit', $p) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:12px">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color:#94a3b8">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:28px;display:block;margin-bottom:8px"></i>
                                Barcha mahsulotlar yetarli
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                Tezkor amallar
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('pos.index') }}" target="_blank"
                   class="btn btn-primary d-flex align-items-center gap-2 justify-content-center py-3"
                   style="font-size:15px;font-weight:600">
                    <i class="bi bi-display-fill" style="font-size:18px"></i> POS Kassir ochish
                </a>
                <a href="{{ route('backend.products.create') }}"
                   class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam-fill"></i> Mahsulot qo'shish
                </a>
                <a href="{{ route('backend.purchases.create') }}"
                   class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-bag-plus-fill"></i> Xarid kiritish
                </a>
                <a href="{{ route('backend.customers.create') }}"
                   class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i> Mijoz qo'shish
                </a>
                <a href="{{ route('backend.debts.index') }}"
                   class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card-2-back-fill"></i> Qarzlar
                </a>
                <a href="{{ route('backend.reports.monthly') }}"
                   class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill"></i> Oylik hisobot
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
Chart.defaults.color = '#6b7a8f';

// ── 7-day sales bar chart ──────────────────────────────
const weekLabels = @json($weekSales->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d.m')));
const weekTotals = @json($weekSales->pluck('total')->map(fn($v) => (float) $v));

// Fill missing days with 0
const allDays = [];
const allTotals = [];
for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const label = d.toLocaleDateString('ru-RU', { day:'2-digit', month:'2-digit' }).replace('.','.').substring(0,5);
    allDays.push(label);
    const idx = weekLabels.indexOf(label);
    allTotals.push(idx !== -1 ? weekTotals[idx] : 0);
}

new Chart(document.getElementById('weekSalesChart'), {
    type: 'bar',
    data: {
        labels: allDays,
        datasets: [{
            label: 'Savdo (so\'m)',
            data: allTotals,
            backgroundColor: 'rgba(59,130,246,0.15)',
            borderColor: 'rgba(59,130,246,0.9)',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: 'rgba(59,130,246,0.3)',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.parsed.y.toLocaleString('uz-UZ') + ' so\'m'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    callback: v => v >= 1000000
                        ? (v/1000000).toFixed(1) + 'M'
                        : v >= 1000 ? (v/1000).toFixed(0) + 'K' : v
                }
            },
            x: { grid: { display: false } }
        }
    }
});

// ── Top products horizontal bar chart ─────────────────
@if($topProducts->isNotEmpty())
const productLabels = @json($topProducts->map(fn($p) => $p->product?->name ?? 'Noma\'lum'));
const productQtys   = @json($topProducts->pluck('qty_sold')->map(fn($v) => (float) $v));

const colors = [
    '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
    '#06b6d4','#84cc16','#f97316','#ec4899','#6366f1'
];

new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Sotilgan (dona)',
            data: productQtys,
            backgroundColor: colors.map(c => c + '33'),
            borderColor: colors,
            borderWidth: 2,
            borderRadius: 4,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.parsed.x + ' dona'
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { precision: 0 }
            },
            y: { grid: { display: false } }
        }
    }
});
@endif
</script>
@endsection
