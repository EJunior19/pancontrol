@extends('layout.app')

@section('content')

<div class="max-w-lg mx-auto bg-white rounded-2xl shadow p-6">

    <h1 class="text-2xl font-bold mb-1">Crear insumo</h1>
    <p class="text-slate-500 mb-6">Registrar nuevo insumo base</p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('supplies.store') }}">
        @csrf

        {{-- Nombre --}}
        <div class="mb-4">
            <label class="text-sm text-slate-600">Nombre</label>
            <input type="text" name="name" required
                   placeholder="Ej: Harina 000"
                   class="w-full rounded-lg border-slate-300">
        </div>

        {{-- Unidad --}}
        <div class="mb-4">
            <label class="text-sm text-slate-600">Unidad</label>
            <select name="unit" required
                    class="w-full rounded-lg border-slate-300">
                <option value="kg">KG</option>
                <option value="g">G</option>
                <option value="l">L</option>
                <option value="u">Unidad</option>
            </select>
        </div>

        {{-- Stock mínimo --}}
        <div class="mb-6">
            <label class="text-sm text-slate-600">Stock mínimo</label>
            <input type="number" step="0.01" min="0"
                   name="minimum_stock"
                   placeholder="Ej: 10"
                   class="w-full rounded-lg border-slate-300">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('supplies.index') }}"
               class="px-4 py-2 bg-slate-200 rounded-lg">
                Cancelar
            </a>

            <button class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700
                           text-white rounded-lg font-semibold">
                Guardar
            </button>
        </div>
    </form>
</div>

@endsection
