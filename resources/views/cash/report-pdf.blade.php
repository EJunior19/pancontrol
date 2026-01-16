<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Arqueo de Caja</title>

<style>
    @page {
        size: A4;
        margin: 15mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #000;
    }

    h1, h2, h3 {
        margin: 0;
        padding: 0;
    }

    h1 {
        font-size: 18px;
        margin-bottom: 5px;
    }

    h3 {
        margin-top: 20px;
        margin-bottom: 6px;
        font-size: 13px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    th {
        background: #eee;
        font-weight: bold;
        text-align: center;
    }

    .right { text-align: right; }
    .center { text-align: center; }

    .ok { color: #0f766e; font-weight: bold; }
    .bad { color: #b91c1c; font-weight: bold; }

    .footer {
        position: fixed;
        bottom: 10mm;
        width: 100%;
        text-align: center;
        font-size: 9px;
        color: #555;
    }

    .signature td {
        height: 60px;
        text-align: center;
    }
</style>
</head>

<body>

<h1>Arqueo de Caja Nº {{ $cashRegister->id }}</h1>
<p>
    <strong>Apertura:</strong> {{ $cashRegister->opened_at }} <br>
    <strong>Cierre:</strong> {{ $cashRegister->closed_at }}
</p>

{{-- ================= RESUMEN ================= --}}
<h3>Resumen por moneda</h3>

<table>
    <thead>
        <tr>
            <th>Moneda</th>
            <th>Mov.</th>
            <th>Ingresos</th>
            <th>Egresos</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($summaryByCurrency as $s)
        <tr>
            <td class="center">{{ $s->currency }}</td>
            <td class="right">{{ $s->movimientos }}</td>
            <td class="right">{{ number_format($s->ingresos, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($s->egresos, 0, ',', '.') }}</td>
            <td class="right {{ $s->saldo < 0 ? 'bad' : 'ok' }}">
                {{ number_format($s->saldo, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ================= DETALLE ================= --}}
<h3>Detalle de movimientos</h3>

<table>
    <thead>
        <tr>
            <th>Hora</th>
            <th>Moneda</th>
            <th>Tipo</th>
            <th>Monto</th>
            <th>Detalle</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($movements as $m)
        <tr>
            <td class="center">
                {{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}
            </td>
            <td class="center">{{ $m->currency }}</td>
            <td class="center">
                {{ $m->type === 'in' ? 'Ingreso' : 'Egreso' }}
            </td>
            <td class="right">
                {{ number_format($m->amount, 0, ',', '.') }}
            </td>
            <td>{{ $m->description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ================= FIRMAS ================= --}}
<h3>Firmas</h3>

<table class="signature">
    <tr>
        <td>
            _______________________________<br>
            Cajero
        </td>
        <td>
            _______________________________<br>
            Supervisor
        </td>
    </tr>
</table>

<div class="footer">
    Documento generado automáticamente por el sistema PanControl
</div>

</body>
</html>
