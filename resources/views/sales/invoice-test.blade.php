<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0 auto;
        }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
    </style>
</head>
<body>

<div class="center">
    <strong>PANCONTROL</strong><br>
    Panadería & Minimarket<br>
    Salto del Guairá - Paraguay<br>
    RUC: 1234567-8
</div>

<div class="line"></div>

<strong>FACTURA (PRUEBA)</strong><br>
Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}<br>
Venta Nº: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>

<div class="line"></div>

<strong>Cliente:</strong><br>
RUC: {{ $sale->factura_ruc }}<br>
Nombre: {{ $sale->factura_nombre }}<br>
Dirección: {{ $sale->factura_direccion }}<br>

<div class="line"></div>

@foreach ($sale->items as $item)
    {{ $item->name }}<br>
    {{ $item->quantity }} x ₲ {{ number_format($item->unit_price, 0, ',', '.') }}
    <span style="float:right">
        ₲ {{ number_format($item->subtotal, 0, ',', '.') }}
    </span>
    <br>
@endforeach

<div class="line"></div>

<strong>TOTAL:</strong>
<span style="float:right">
    ₲ {{ number_format($sale->total, 0, ',', '.') }}
</span>

<br><br>
Pago: {{ ucfirst($sale->payment_method) }}

<div class="line"></div>

<div class="center">
    Documento NO válido como factura legal<br>
    Uso interno / Pruebas<br><br>
    ¡Gracias por su compra!
</div>

<script>
    window.onload = () => window.print();
</script>

</body>
</html>
