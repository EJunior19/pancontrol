@extends('layout.app')

@section('content')

<div class="max-w-lg mx-auto bg-white rounded-2xl shadow p-6">

    {{-- ENCABEZADO --}}
    <h1 class="text-2xl font-bold text-slate-800 mb-1">
        Ingreso de insumo
    </h1>
    <p class="text-slate-500 mb-6">
        Registrar entrada de stock
    </p>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('supplies.movements.store') }}">
        @csrf

        {{-- INSUMO --}}
        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1">
                Insumo
            </label>

            <select name="supply_id" required
                    class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500">
                <option value="">Seleccione</option>

                @foreach ($supplies as $s)
                    <option value="{{ $s->id }}">
                        {{ $s->name }} ({{ strtoupper($s->unit) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- CANTIDAD --}}
        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1">
                Cantidad
            </label>

            <input type="number"
                   step="0.001"
                   min="0.001"
                   name="amount"
                   required
                   class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
                   placeholder="Ej: 50">
        </div>

        {{-- OBSERVACIÓN --}}
        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-1">
                Observación
            </label>

            <input type="text"
                   name="description"
                   placeholder="Ej: Compra proveedor X"
                   class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500">
        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('supplies.index') }}"
               class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg text-sm">
                Cancelar
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700
                           text-white rounded-lg text-sm font-semibold">
                Guardar
            </button>
        </div>
    </form>

</div>

@endsection
