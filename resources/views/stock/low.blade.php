@extends('layout.app')

@section('content')

<h1 class="text-2xl font-bold mb-6 text-red-600">
    ⚠️ Productos con stock bajo
</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="p-3 text-left">Producto</th>
                <th class="p-3 text-right">Stock disponible</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                <tr class="border-t">
                    <td class="p-3">{{ $p->name }}</td>
                    <td class="p-3 text-right font-semibold text-red-600">
                        {{ $p->stock_qty }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-4 text-center text-slate-400">
                        No hay productos con stock bajo 🎉
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
