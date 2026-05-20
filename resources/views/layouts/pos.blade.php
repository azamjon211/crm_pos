<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<!-- Top bar -->
<nav class="navbar">
    <span>POS Kassir</span>

    <!-- Faqat admin/manager uchun Backend link -->
    @if(auth()->user()->role !== 'cashier')
        <a href="/backend/dashboard">Backend</a>
    @endif

    <span>{{ auth()->user()->name }}</span>
    <form action="/logout" method="POST">
        <button>Chiqish</button>
    </form>
</nav>

<!-- Asosiy kontent -->
<main>
    @yield('content')
</main>
</body>
</html>
