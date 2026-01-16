@extends('layout.app')

@section('content')

<div class="max-w-lg mx-auto bg-white rounded-2xl shadow p-6">

    <h1 class="text-3xl font-bold text-slate-800 mb-2">
        Apertura de Caja
    </h1>

    <p class="text-slate-500 mb-6">
        Ingrese el dinero disponible al iniciar el turno
    </p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4">
        Asegúrese de contar el efectivo real antes de abrir la caja.
    </div>
</pre>

    <form method="POST" action="{{ route('cash.open') }}" onsubmit="return prepareValues()">
        @csrf

        <!-- Guaraníes -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Guaraníes (Gs)
            </label>
            <input
                type="text"
                inputmode="numeric"
                name="opening_gs_view"
                value="{{ number_format(old('opening_gs', 0), 0, ',', '.') }}"
                class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                oninput="formatGs(this)"
                required
            >
            <input type="hidden" name="opening_gs" id="opening_gs">
        </div>

        <!-- Dólares -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Dólares (USD)
            </label>
            <input
                type="text"
                inputmode="decimal"
                name="opening_usd_view"
                value="{{ number_format(old('opening_usd', 0), 2, ',', '.') }}"
                class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                oninput="formatDecimal(this)"
                required
            >
            <input type="hidden" name="opening_usd" id="opening_usd">
        </div>

        <!-- Reales -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Reales (BRL)
            </label>
            <input
                type="text"
                inputmode="decimal"
                name="opening_brl_view"
                value="{{ number_format(old('opening_brl', 0), 2, ',', '.') }}"
                class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                oninput="formatDecimal(this)"
                required
            >
            <input type="hidden" name="opening_brl" id="opening_brl">
        </div>

        <!-- ================= TIPOS DE CAMBIO ================= -->
        <div class="mb-4 bg-slate-50 border rounded-xl p-4">
            <p class="font-semibold text-slate-700 mb-2">
                Tipo de cambio del día
            </p>
            <p class="text-xs text-slate-500 mb-4">
                Definí a cuánto vas a tomar cada moneda (en Guaraníes por 1 unidad).
            </p>

            <!-- USD -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    1 USD = ¿cuántos Gs?
                </label>
                <input
                    type="text"
                    inputmode="numeric"
                    name="rate_usd_view"
                    value="{{ number_format(old('rate_usd', 0), 0, ',', '.') }}"
                    class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    oninput="formatGs(this)"
                    required
                >
                <input type="hidden" name="rate_usd" id="rate_usd">
            </div>

            <!-- BRL -->
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    1 BRL = ¿cuántos Gs?
                </label>
                <input
                    type="text"
                    inputmode="numeric"
                    name="rate_brl_view"
                    value="{{ number_format(old('rate_brl', 0), 0, ',', '.') }}"
                    class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    oninput="formatGs(this)"
                    required
                >
                <input type="hidden" name="rate_brl" id="rate_brl">
            </div>
        </div>

        <button
            type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-lg font-semibold"
        >
            Abrir Caja
        </button>
    </form>
</div>

<script>
    function formatGs(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function formatDecimal(input) {
        let value = input.value.replace(/[^0-9,]/g, '');

        if ((value.match(/,/g) || []).length > 1) {
            value = value.replace(/,(?=.*?,)/g, '');
        }

        let parts = value.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        if (parts[1]) {
            parts[1] = parts[1].substring(0, 2);
        }

        input.value = parts.join(',');
    }

   function prepareValues() {
    document.getElementById('opening_gs').value =
        document.querySelector('[name="opening_gs_view"]').value.replace(/\./g, '');

    document.getElementById('opening_usd').value =
        document.querySelector('[name="opening_usd_view"]').value
            .replace(/\./g, '')
            .replace(',', '.');

    document.getElementById('opening_brl').value =
        document.querySelector('[name="opening_brl_view"]').value
            .replace(/\./g, '')
            .replace(',', '.');

    // ✅ tipos de cambio (Gs por 1)
    document.getElementById('rate_usd').value =
        document.querySelector('[name="rate_usd_view"]').value.replace(/\./g, '');

    document.getElementById('rate_brl').value =
        document.querySelector('[name="rate_brl_view"]').value.replace(/\./g, '');

    return true; // ✅ CLAVE: asegura que se complete antes del submit
}

</script>

@endsection
