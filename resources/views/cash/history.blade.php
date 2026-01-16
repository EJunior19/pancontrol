@extends('layout.app')

@section('content')

@php
  $fmtGs = fn($n) => '₲ '.number_format((float)$n, 0, ',', '.');
  $fmtFx = fn($n) => number_format((float)$n, 2, ',', '.');

  $estado = function ($r) {
    $diff =
      ($r->difference_gs  ?? 0) +
      ($r->difference_usd ?? 0) +
      ($r->difference_brl ?? 0);

    if ($diff == 0) return ['OK', 'text-emerald-600'];
    if ($diff > 0)  return ['Sobrante', 'text-emerald-700'];
    return ['Faltante', 'text-red-600'];
  };
@endphp

<!-- TÍTULO -->
<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-3xl font-bold text-slate-800">
      Historial de Caja
    </h1>
    <p class="text-slate-500">
      Registro de cierres de caja y diferencias detectadas
    </p>
  </div>
</div>

<!-- TABLA -->
<div class="bg-white rounded-2xl shadow overflow-x-auto">

  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 border-b text-slate-600">
      <tr>
        <th class="px-4 py-3 text-left">Fecha</th>
        <th class="px-4 py-3 text-left">Apertura</th>
        <th class="px-4 py-3 text-left">Cierre</th>
        <th class="px-4 py-3 text-right">Dif. ₲</th>
        <th class="px-4 py-3 text-right">Dif. USD</th>
        <th class="px-4 py-3 text-right">Dif. BRL</th>
        <th class="px-4 py-3 text-center">Estado</th>
        <th class="px-4 py-3 text-center">Acciones</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse ($registers as $r)
        @php [$label, $color] = $estado($r); @endphp

        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            {{ optional($r->opened_at)->format('d/m/Y') }}
          </td>

          <td class="px-4 py-3">
            {{ optional($r->opened_at)->format('H:i') }}
          </td>

          <td class="px-4 py-3">
            {{ optional($r->closed_at)->format('H:i') ?? '-' }}
          </td>

          <td class="px-4 py-3 text-right {{ ($r->difference_gs ?? 0) < 0 ? 'text-red-600' : 'text-emerald-700' }}">
            {{ $fmtGs($r->difference_gs ?? 0) }}
          </td>

          <td class="px-4 py-3 text-right {{ ($r->difference_usd ?? 0) < 0 ? 'text-red-600' : 'text-emerald-700' }}">
            $ {{ $fmtFx($r->difference_usd ?? 0) }}
          </td>

          <td class="px-4 py-3 text-right {{ ($r->difference_brl ?? 0) < 0 ? 'text-red-600' : 'text-emerald-700' }}">
            R$ {{ $fmtFx($r->difference_brl ?? 0) }}
          </td>

          <td class="px-4 py-3 text-center font-semibold {{ $color }}">
            {{ $label }}
          </td>

          <!-- ACCIONES -->
          <td class="px-4 py-3 text-center">
            <a href="{{ route('cash.report.pdf', $r->id) }}"
               target="_blank"
               class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-900 text-white px-3 py-1 rounded-md text-xs font-medium">
              PDF
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="px-4 py-6 text-center text-slate-400">
            No hay cierres de caja registrados.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- PAGINACIÓN -->
<div class="mt-6">
  {{ $registers->links() }}
</div>

@endsection
