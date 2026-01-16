@extends('layout.app')

@section('content')

@php
  // Flags: mostrar solo monedas que se abrieron (mayor a 0)
  $showGs  = (float) $cashRegister->opening_gs  > 0;
  $showUsd = (float) $cashRegister->opening_usd > 0;
  $showBrl = (float) $cashRegister->opening_brl > 0;

  // Helpers de formato (solo para mostrar)
  $fmtGs  = fn($n) => number_format((float)$n, 0, ',', '.');
  $fmtFx  = fn($n) => number_format((float)$n, 2, ',', '.'); // USD/BRL con centavos

  // Esperado (viene del controller closeForm)
  $expected = $expected ?? ['PYG'=>0,'USD'=>0,'BRL'=>0];
@endphp

<div class="max-w-5xl mx-auto">

  <!-- Header -->
  <div class="mb-5">
    <h1 class="text-3xl font-bold text-slate-800 mb-1">Cierre de Caja</h1>
    <p class="text-slate-500">Ingrese el efectivo contado al finalizar el turno</p>
  </div>

  <!-- Errores -->
  @if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="bg-white rounded-2xl shadow p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- =========================
         COLUMNA IZQUIERDA (RESUMEN)
         ========================= --}}
      <div class="space-y-4">

        <!-- Apertura -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
          <p class="font-semibold text-slate-700 mb-2">Apertura de caja</p>

          @if($showGs)
            <div class="flex justify-between">
              <span class="text-slate-600">Guaraníes</span>
              <span class="font-semibold text-slate-800">₲ {{ $fmtGs($cashRegister->opening_gs) }}</span>
            </div>
          @endif

          @if($showUsd)
            <div class="flex justify-between">
              <span class="text-slate-600">Dólares</span>
              <span class="font-semibold text-slate-800">USD {{ $fmtFx($cashRegister->opening_usd) }}</span>
            </div>
          @endif

          @if($showBrl)
            <div class="flex justify-between">
              <span class="text-slate-600">Reales</span>
              <span class="font-semibold text-slate-800">R$ {{ $fmtFx($cashRegister->opening_brl) }}</span>
            </div>
          @endif
        </div>

        <!-- Esperado -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm">
          <p class="font-semibold text-emerald-800 mb-2">Esperado según sistema</p>

          @if($showGs)
            <div class="flex justify-between">
              <span class="text-emerald-700">Guaraníes</span>
              <span class="font-semibold text-emerald-900">₲ {{ $fmtGs($expected['PYG'] ?? 0) }}</span>
            </div>
          @endif

          @if($showUsd)
            <div class="flex justify-between">
              <span class="text-emerald-700">Dólares</span>
              <span class="font-semibold text-emerald-900">USD {{ $fmtFx($expected['USD'] ?? 0) }}</span>
            </div>
          @endif

          @if($showBrl)
            <div class="flex justify-between">
              <span class="text-emerald-700">Reales</span>
              <span class="font-semibold text-emerald-900">R$ {{ $fmtFx($expected['BRL'] ?? 0) }}</span>
            </div>
          @endif
        </div>

        <!-- Diferencia -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
          <p class="font-semibold text-slate-700 mb-2">Diferencia (contado − esperado)</p>

          @if($showGs)
            <div class="flex justify-between">
              <span class="text-slate-600">Guaraníes</span>
              <span id="diff_gs" class="font-semibold text-slate-900">₲ 0</span>
            </div>
          @endif

          @if($showUsd)
            <div class="flex justify-between">
              <span class="text-slate-600">Dólares</span>
              <span id="diff_usd" class="font-semibold text-slate-900">$ 0,00</span>
            </div>
          @endif

          @if($showBrl)
            <div class="flex justify-between">
              <span class="text-slate-600">Reales</span>
              <span id="diff_brl" class="font-semibold text-slate-900">R$ 0,00</span>
            </div>
          @endif

          <p class="text-xs text-slate-400 mt-2">
            Positivo = sobrante • Negativo = faltante
          </p>
        </div>

      </div>

      {{-- =========================
         COLUMNA DERECHA (FORM)
         ========================= --}}
      <div>
        <form method="POST" action="{{ route('cash.close') }}" class="space-y-4">
          @csrf

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Gs --}}
            @if($showGs)
              <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Guaraníes contados (₲)
                </label>
                <input
                  type="text"
                  inputmode="numeric"
                  autocomplete="off"
                  id="closing_gs_view"
                  placeholder="Ej: 20.000"
                  class="w-full rounded-lg border-slate-300 text-lg focus:border-red-500 focus:ring-red-500"
                >
                <input type="hidden" name="closing_gs" id="closing_gs" value="0">
                <p class="text-xs text-slate-400 mt-1">Se formatea automáticamente: 20000 → 20.000</p>
              </div>
            @else
              <input type="hidden" name="closing_gs" value="0">
            @endif

            {{-- USD --}}
            @if($showUsd)
              <div class="md:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Dólares contados (USD)
                </label>
                <input
                  type="text"
                  inputmode="decimal"
                  autocomplete="off"
                  id="closing_usd_view"
                  placeholder="Ej: 10,50"
                  class="w-full rounded-lg border-slate-300 text-lg focus:border-red-500 focus:ring-red-500"
                >
                <input type="hidden" name="closing_usd" id="closing_usd" value="0">
                <p class="text-xs text-slate-400 mt-1">Acepta coma: 10,50</p>
              </div>
            @else
              <input type="hidden" name="closing_usd" value="0">
            @endif

            {{-- BRL --}}
            @if($showBrl)
              <div class="md:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Reales contados (BRL)
                </label>
                <input
                  type="text"
                  inputmode="decimal"
                  autocomplete="off"
                  id="closing_brl_view"
                  placeholder="Ej: 50,00"
                  class="w-full rounded-lg border-slate-300 text-lg focus:border-red-500 focus:ring-red-500"
                >
                <input type="hidden" name="closing_brl" id="closing_brl" value="0">
                <p class="text-xs text-slate-400 mt-1">Acepta coma: 50,00</p>
              </div>
            @else
              <input type="hidden" name="closing_brl" value="0">
            @endif

            {{-- Spacer si solo hay una moneda --}}
            <div class="hidden md:block md:col-span-1"></div>
          </div>

          <!-- Observaciones -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Observaciones
            </label>
            <textarea
              name="notes"
              rows="3"
              class="w-full rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500"
              placeholder="Ej: faltante, sobrante, error de vuelto..."
            >{{ old('notes') }}</textarea>
          </div>

          <!-- Botón -->
          <button
            type="submit"
            class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl text-lg font-semibold"
          >
            Cerrar Caja
          </button>
        </form>
      </div>

    </div>
  </div>

</div>

<script>
(function(){
  const expected = {
    PYG: Number(@json((float)($expected['PYG'] ?? 0))),
    USD: Number(@json((float)($expected['USD'] ?? 0))),
    BRL: Number(@json((float)($expected['BRL'] ?? 0))),
  };

  const onlyDigits = (s) => (s || '').replace(/[^\d]/g, '');
  const fmtGsView = (digits) => (!digits ? '' : Number(digits).toLocaleString('es-PY'));

  const normalizeDecimal = (s) => {
    s = (s || '').trim();
    if (!s) return '0';
    s = s.replace(/\s/g,'').replace(/\./g,'');
    s = s.replace(',', '.');
    s = s.replace(/[^0-9.]/g,'');
    const parts = s.split('.');
    if (parts.length > 2) s = parts[0] + '.' + parts.slice(1).join('');
    return s === '' ? '0' : s;
  };

  const fmtFxView = (val) => {
    const num = Number(val || 0);
    return num.toLocaleString('es-PY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  const setDiffColor = (el, diff) => {
    if (!el) return;
    el.classList.remove('text-emerald-700','text-red-600','text-slate-900');
    if (diff > 0) el.classList.add('text-emerald-700');
    else if (diff < 0) el.classList.add('text-red-600');
    else el.classList.add('text-slate-900');
  };

  const recalcDiffs = () => {
    const gsVal  = Number(document.getElementById('closing_gs')?.value || 0);
    const usdVal = Number(document.getElementById('closing_usd')?.value || 0);
    const brlVal = Number(document.getElementById('closing_brl')?.value || 0);

    const diffGs  = gsVal  - expected.PYG;
    const diffUsd = usdVal - expected.USD;
    const diffBrl = brlVal - expected.BRL;

    const elGs  = document.getElementById('diff_gs');
    const elUsd = document.getElementById('diff_usd');
    const elBrl = document.getElementById('diff_brl');

    if (elGs) {
      elGs.textContent = `₲ ${Number(diffGs).toLocaleString('es-PY')}`;
      setDiffColor(elGs, diffGs);
    }
    if (elUsd) {
      elUsd.textContent = `$ ${fmtFxView(diffUsd)}`;
      setDiffColor(elUsd, diffUsd);
    }
    if (elBrl) {
      elBrl.textContent = `R$ ${fmtFxView(diffBrl)}`;
      setDiffColor(elBrl, diffBrl);
    }
  };

  const viewGs = document.getElementById('closing_gs_view');
  const hidGs  = document.getElementById('closing_gs');
  if (viewGs && hidGs) {
    viewGs.addEventListener('input', () => {
      const digits = onlyDigits(viewGs.value);
      hidGs.value = digits ? String(Number(digits)) : '0';
      viewGs.value = fmtGsView(digits);
      recalcDiffs();
    });
  }

  const bindFx = (viewId, hidId) => {
    const view = document.getElementById(viewId);
    const hid  = document.getElementById(hidId);
    if (!view || !hid) return;

    view.addEventListener('input', () => {
      const norm = normalizeDecimal(view.value);
      hid.value = norm;
      view.value = fmtFxView(norm);
      recalcDiffs();
    });
  };

  bindFx('closing_usd_view', 'closing_usd');
  bindFx('closing_brl_view', 'closing_brl');

  recalcDiffs();
})();
</script>

@endsection
