<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM POS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sb-w:         256px;
            --topbar-h:     60px;
            --sb-bg:        #0c1a2e;
            --sb-border:    rgba(255,255,255,.06);
            --sb-text:      #8fa3bf;
            --sb-text-h:    #e2eaf4;
            --sb-active-bg: rgba(99,102,241,.18);
            --sb-active-tx: #a5b4fc;
            --sb-section:   #3d5068;
            --accent:       #6366f1;
            --accent-h:     #4f46e5;
            --body-bg:      #f0f4f8;
            --card-bg:      #ffffff;
            --card-border:  #e4eaf2;
            --text-main:    #1a2535;
            --text-muted:   #6b7a8f;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--body-bg);
            color: var(--text-main);
            display: flex;
        }

        /* ─── Sidebar ───────────────────────────────────────────────── */
        .sidebar {
            width: var(--sb-w);
            min-height: 100vh;
            height: 100%;
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--sb-border);
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sb-logo {
            padding: 20px 20px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--sb-border);
            flex-shrink: 0;
        }
        .sb-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99,102,241,.4);
        }
        .sb-logo-name {
            font-size: 16px; font-weight: 800;
            color: #f0f6ff; letter-spacing: -.3px;
        }
        .sb-logo-shop {
            font-size: 11px; color: var(--sb-text);
            margin-top: 1px; max-width: 160px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .sb-nav { flex: 1; padding: 8px 0; }

        .sb-section {
            font-size: 9.5px; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--sb-section);
            padding: 18px 20px 6px;
        }

        .sb-link {
            display: flex; align-items: center; gap: 10px;
            color: var(--sb-text);
            text-decoration: none;
            padding: 9px 20px;
            font-size: 13.5px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .18s;
            margin: 1px 0;
        }
        .sb-link:hover {
            color: var(--sb-text-h);
            background: rgba(255,255,255,.05);
        }
        .sb-link.active {
            color: var(--sb-active-tx);
            background: var(--sb-active-bg);
            border-left-color: #6366f1;
            font-weight: 600;
        }
        .sb-link i {
            font-size: 16px; flex-shrink: 0;
            opacity: .8;
        }
        .sb-link.active i { opacity: 1; }

        .sb-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--sb-border);
            flex-shrink: 0;
        }
        .sb-user {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 6px 10px;
        }
        .sb-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; font-weight: 700;
            flex-shrink: 0;
        }
        .sb-uname { color: #e2eaf4; font-size: 13px; font-weight: 600; line-height: 1.3; }
        .sb-urole { color: var(--sb-text); font-size: 11px; }
        .btn-logout {
            width: 100%; padding: 7px;
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 8px;
            color: #fca5a5;
            font-size: 12.5px; font-weight: 500;
            cursor: pointer; transition: all .18s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,.2);
            color: #f87171;
        }

        /* ─── Main wrapper ──────────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sb-w);
            width: calc(100% - var(--sb-w));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── Topbar ────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--card-border);
            display: flex; align-items: center;
            padding: 0 28px; gap: 16px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .topbar-title {
            font-size: 17px; font-weight: 700;
            color: var(--text-main); letter-spacing: -.3px;
        }
        .topbar-crumb {
            font-size: 12px; color: var(--text-muted); margin-top: 1px;
        }
        .topbar-sep {
            width: 1px; height: 24px;
            background: var(--card-border);
        }

        /* ─── Page body ─────────────────────────────────────────────── */
        .page-body { padding: 28px; flex: 1; }

        /* ─── Alerts ────────────────────────────────────────────────── */
        .alert {
            border-radius: 10px; font-size: 13.5px;
            border: none; padding: 12px 16px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .alert-success { background: #f0fdf4; color: #15803d; }
        .alert-danger  { background: #fef2f2; color: #b91c1c; }
        .alert-warning { background: #fffbeb; color: #b45309; }
        .alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-body { flex: 1; }

        /* ─── Cards ─────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            font-weight: 700; font-size: 14px;
            color: var(--text-main);
            padding: 16px 20px;
            border-radius: 14px 14px 0 0 !important;
            display: flex; align-items: center; gap: 8px;
        }
        .card-body { padding: 20px; }

        /* ─── Stat cards ─────────────────────────────────────────────── */
        .kpi {
            border-radius: 16px;
            padding: 22px 24px;
            position: relative; overflow: hidden;
            border: none;
        }
        .kpi::after {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 110px; height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
        }
        .kpi-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; opacity: .8; }
        .kpi-val   { font-size: 28px; font-weight: 800; letter-spacing: -.5px; line-height: 1.1; margin: 6px 0 4px; }
        .kpi-sub   { font-size: 12px; opacity: .75; }
        .kpi-icon  {
            position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
            font-size: 40px; opacity: .2;
        }
        .kpi-blue   { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; }
        .kpi-green  { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .kpi-amber  { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
        .kpi-rose   { background: linear-gradient(135deg, #f43f5e, #e11d48); color: #fff; }

        /* ─── Tables ─────────────────────────────────────────────────── */
        .table { margin: 0; font-size: 13.5px; border-collapse: separate; border-spacing: 0; }
        .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid var(--card-border) !important;
            border-top: none !important;
            font-weight: 700; font-size: 11px;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--text-muted); padding: 11px 16px;
            white-space: nowrap;
        }
        .table td {
            padding: 12px 16px; vertical-align: middle;
            color: var(--text-main);
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
        }
        .table tbody tr:last-child td { border-bottom: none !important; }
        .table tbody tr { transition: background .12s; }
        .table tbody tr:hover td { background: #f8fbff; }

        /* ─── Badges ─────────────────────────────────────────────────── */
        .badge {
            font-weight: 600; font-size: 11.5px;
            padding: 4px 9px; border-radius: 20px;
        }

        /* ─── Buttons ────────────────────────────────────────────────── */
        .btn { border-radius: 9px; font-size: 13.5px; font-weight: 500; transition: all .18s; }
        .btn:active { transform: scale(.97); }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none; color: #fff;
            box-shadow: 0 3px 10px rgba(99,102,241,.3);
        }
        .btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 4px 14px rgba(99,102,241,.45); color: #fff; }
        .btn-outline-secondary { border-color: var(--card-border); color: var(--text-muted); }
        .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: var(--text-main); }
        .btn-sm { padding: 5px 12px; font-size: 12.5px; }

        /* ─── Forms ──────────────────────────────────────────────────── */
        .form-control, .form-select {
            border-radius: 9px; border-color: var(--card-border);
            font-size: 13.5px; padding: 9px 14px;
            background: #fff; color: var(--text-main);
            transition: border-color .18s, box-shadow .18s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
            outline: none;
        }
        .form-control::placeholder { color: #b0bec5; }
        .form-label { font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .input-group-text {
            border-color: var(--card-border); border-radius: 9px;
            background: #f8fafc; color: var(--text-muted); font-size: 13.5px;
        }
        .input-group > .form-control { border-radius: 0; }
        .input-group > .form-control:first-child { border-radius: 9px 0 0 9px; }
        .input-group > .form-control:last-child  { border-radius: 0 9px 9px 0; }
        .input-group > .input-group-text:first-child { border-radius: 9px 0 0 9px; }
        .input-group > .input-group-text:last-child  { border-radius: 0 9px 9px 0; }
        .input-group > .form-control:not(:first-child):not(:last-child) { border-radius: 0; }

        /* ─── Pagination ──────────────────────────────────────────────── */
        .pagination { gap: 3px; }
        .pagination .page-link {
            border-radius: 8px !important; border-color: var(--card-border);
            color: #6366f1; font-size: 13px; padding: 6px 12px;
        }
        .pagination .page-item.active .page-link {
            background: #6366f1; border-color: #6366f1;
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-icon"><i class="bi bi-shop-window"></i></div>
            <div>
                <div class="sb-logo-name">CRM POS</div>
                @auth
                    <div class="sb-logo-shop">{{ auth()->user()->shop->name ?? 'Do\'kon' }}</div>
                @endauth
            </div>
        </div>

        @auth
        <div class="sb-nav">
            <div class="sb-section">Asosiy</div>
            @if(auth()->user()->isManagerOrAdmin())
                <a href="{{ route('backend.dashboard') }}" class="sb-link {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            @endif
            <a href="{{ route('pos.index') }}" class="sb-link">
                <i class="bi bi-display-fill"></i> POS Kassir
            </a>

            @if(auth()->user()->isSuperAdmin())
                <div class="sb-section">Super Admin</div>
                <a href="{{ route('backend.shops.index') }}" class="sb-link {{ request()->routeIs('backend.shops.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Do'konlar
                </a>
                <a href="{{ route('backend.users.index') }}" class="sb-link {{ request()->routeIs('backend.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Foydalanuvchilar
                </a>
            @endif

            @if(auth()->user()->isManagerOrAdmin())
                <div class="sb-section">Katalog</div>
                <a href="{{ route('backend.categories.index') }}" class="sb-link {{ request()->routeIs('backend.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i> Kategoriyalar
                </a>
                <a href="{{ route('backend.products.index') }}" class="sb-link {{ request()->routeIs('backend.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Mahsulotlar
                    @if(!empty($lowStockCount) && $lowStockCount > 0)
                        <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;line-height:16px">{{ $lowStockCount }}</span>
                    @endif
                </a>
                <a href="{{ route('backend.suppliers.index') }}" class="sb-link {{ request()->routeIs('backend.suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck-front-fill"></i> Yetkazuvchilar
                </a>

                <div class="sb-section">Savdo</div>
                <a href="{{ route('backend.sales.index') }}" class="sb-link {{ request()->routeIs('backend.sales.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Sotuvlar
                </a>
                <a href="{{ route('backend.customers.index') }}" class="sb-link {{ request()->routeIs('backend.customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Mijozlar
                </a>
                <a href="{{ route('backend.purchases.index') }}" class="sb-link {{ request()->routeIs('backend.purchases.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-plus-fill"></i> Xaridlar
                </a>
                <a href="{{ route('backend.debts.index') }}" class="sb-link {{ request()->routeIs('backend.debts.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-back-fill"></i> Qarzlar
                </a>
                @if(!auth()->user()->isSuperAdmin())
                    <a href="{{ route('backend.users.index') }}" class="sb-link {{ request()->routeIs('backend.users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> Xodimlar
                    </a>
                @endif

                <div class="sb-section">Hisobot</div>
                <a href="{{ route('backend.daily-closing.index') }}" class="sb-link {{ request()->routeIs('backend.daily-closing.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-check-fill"></i> Kunlik yopish
                </a>
                <a href="{{ route('backend.reports.monthly') }}" class="sb-link {{ request()->routeIs('backend.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i> Oylik hisobot
                </a>
            @endif
        </div>

        @if(auth()->user()->isSuperAdmin())
        <div style="padding:8px 16px;border-top:1px solid rgba(255,255,255,.1);font-size:12px">
            <div style="color:rgba(255,255,255,.45);margin-bottom:3px">Aktiv magazin</div>
            <a href="{{ route('backend.switch-shop') }}" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px">
                <i class="bi bi-shop" style="font-size:13px"></i>
                <span>{{ session('shop_name') ?? 'Tanlanmagan' }}</span>
                <i class="bi bi-arrow-left-right" style="margin-left:auto;opacity:.4;font-size:10px"></i>
            </a>
        </div>
        @endif
        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="sb-uname">{{ auth()->user()->name }}</div>
                    <div class="sb-urole">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-left"></i> Chiqish
                </button>
            </form>
        </div>
        @endauth
    </div>

    {{-- Main --}}
    <div class="main-wrap">
        <div class="topbar">
            <div>
                <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
                @hasSection('breadcrumb')
                    <div class="topbar-crumb">@yield('breadcrumb')</div>
                @endif
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                @yield('topbar_actions')
            </div>
        </div>

        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                    <div class="alert-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible mb-4">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div class="alert-body">{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div class="alert-body">{{ session('warning') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible mb-4">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div class="alert-body">
                        @foreach($errors->all() as $err) {{ $err }}<br> @endforeach
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
