<!DOCTYPE html>
@php
    use App\Models\CashRegister;

    $cajaAbierta = CashRegister::where('status', 'open')->first();
@endphp

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PanControl</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/js/app.js'])

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-slate-100 min-h-screen">

<!-- ================= NAVBAR ================= -->
<header class="bg-slate-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <span class="text-2xl">🥖</span>
            <h1 class="text-xl font-bold tracking-wide">PanControl</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex gap-6 text-sm font-medium items-center">

            <a href="{{ route('dashboard') }}" class="hover:text-amber-400">
                Dashboard
            </a>

            <!-- ================= Ventas ================= -->
            <div class="relative group">

                <!-- CLICK PRINCIPAL → VENTA -->
                <a href="{{ route('sales.index') }}"
                class="flex items-center gap-1 hover:text-amber-400 px-2 py-1">
                    Ventas ▾
                </a>

                <!-- MENU DESPLEGABLE -->
                <div class="absolute left-0 top-full pt-2
                            hidden group-hover:block
                            z-50">
                    <div class="bg-white text-slate-700 rounded-lg shadow-lg w-52 py-1">

                        <a href="{{ route('sales.history') }}"
                        class="block px-4 py-2 hover:bg-slate-100">
                            🧾 Historial de ventas
                        </a>

                    </div>
                </div>
            </div>


            <!-- ================= Caja ================= -->
            <div class="relative group">

                <!-- BOTÓN -->
                <button type="button"
                        class="hover:text-amber-400 flex items-center gap-1 px-2 py-1">
                    Caja ▾
                </button>

                <!-- MENU -->
                <div
                    class="absolute left-0 top-full
                        hidden group-hover:block
                        bg-white text-slate-700
                        rounded-lg shadow-lg
                        w-52 z-50
                        py-1
                        pt-2"
                >
                    @if($cajaAbierta)
                        <a href="{{ route('cash.summary') }}"
                        class="block px-4 py-2 hover:bg-slate-100">
                            💰 Resumen del día
                        </a>
                    @else
                        <span class="block px-4 py-2 text-slate-400 cursor-not-allowed">
                            💰 Resumen del día
                        </span>
                    @endif


                    <a href="{{ route('cash.history') }}"
                    class="block px-4 py-2 hover:bg-slate-100 text-slate-600">
                        📂 Historial de caja
                    </a>

                    @if($cajaAbierta)
                        <a href="{{ route('cash.close.form') }}"
                        class="block px-4 py-2 hover:bg-red-100 text-red-600">
                            🔒 Cerrar caja
                        </a>
                    @else
                        <a href="{{ route('cash.open.form') }}"
                        class="block px-4 py-2 hover:bg-slate-100">
                            🔓 Abrir caja
                        </a>
                    @endif
                </div>
            </div>
        <!-- ================= Producción ================= -->
        <div class="relative group">

            <!-- CLICK PRINCIPAL → PRODUCCIÓN -->
            <a href="{{ route('production.index') }}"
            class="flex items-center gap-1 hover:text-amber-400 px-2 py-1">
                Producción ▾
            </a>

            <!-- MENU DESPLEGABLE -->
            <div class="absolute left-0 top-full pt-2
                        hidden group-hover:block
                        z-50">
                <div class="bg-white text-slate-700 rounded-lg shadow-lg w-56 py-1">

                    <a href="{{ route('production.top-products') }}"
                    class="block px-4 py-2 hover:bg-slate-100">
                        🥖 Más vendidos (hoy)
                    </a>

                    <a href="{{ route('production.products-history') }}"
                    class="block px-4 py-2 hover:bg-slate-100">
                        📈 Histórico de productos
                    </a>

                </div>
            </div>
        </div>


            <!-- ================= Stock ================= -->
            <div class="relative group">
                <button type="button"
                        class="flex items-center gap-1 hover:text-amber-400">
                    Stock ▾
                </button>

                <div class="absolute left-0 top-full pt-2
                            hidden group-hover:block
                            z-50">
                    <div class="bg-white text-slate-700 rounded-lg shadow-lg w-52 py-1">
                        <a href="{{ route('products.index') }}"
                           class="block px-4 py-2 hover:bg-slate-100">
                            📦 Stock actual
                        </a>
                        <a href="{{ route('stock.low') }}"
                           class="block px-4 py-2 hover:bg-red-100 text-red-600">
                            ⚠️ Stock bajo
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('supplies.index') }}" class="hover:text-amber-400">
                Insumos
            </a>

        </nav>
    </div>
</header>

<!-- ================= CONTENT ================= -->
<main class="max-w-7xl mx-auto px-6 py-8">
    @yield('content')
</main>

</body>
</html>
