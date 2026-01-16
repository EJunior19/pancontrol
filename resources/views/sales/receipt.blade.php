<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo #{{ $sale->id }}</title>

    <style>
        body {
            font-family: monospace;
            width: 280px;              /* ✅ 58 mm */
            margin: 0 auto;            /* ✅ centrado real */
            padding: 0;
            font-size: 14px;
        }

        .ticket {
            width: 100%;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin: 8px 0;
            font-size: 17px;
            font-weight: bold;
        }

        .center { text-align: center; }
        .left   { text-align: left; }
        .right  { text-align: right; }

        .separator {
            width: 100%;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 14px;
        }

        .item-name {
            font-weight: bold;
        }

        .total {
            width: 100%;
            border-top: 1px dashed #000;
            margin-top: 12px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 17px;
            text-align: right;        /* ✅ TOTAL alineado */
        }

        .small {
            font-size: 12px;
        }
    </style>
</head>

<body onload="window.print(); window.onafterprint = () => window.close();">

<div class="ticket">

    <!-- =========================
         🏪 DATOS DEL NEGOCIO
    ========================== -->
    <h2>PANCONTROL</h2>

    <div class="center small">
        Panadería & Minimarket<br>
        Av. San Martín 1234<br>
        Tel: (0981) 123-456<br>
        Salto del Guairá – Paraguay<br>
        RUC: 1234567-8
    </div>

    <div class="separator"></div>

    <!-- =========================
         🧾 DATOS DE LA VENTA
    ========================== -->
    <div class="small left">
        Fecha: {{ $sale->sale_date->format('d/m/Y H:i') }}<br>
        Venta Nº: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
        Caja: {{ $sale->cash_register_id }}<br>
        Comprobante: {{ strtoupper($sale->tipo_comprobante ?? 'TICKET') }}
    </div>

    <div class="separator"></div>

    <!-- =========================
         📦 ITEMS
    ========================== -->
    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="item-name left">
                    {{ $item->variant->name }}
                </td>
            </tr>
            <tr>
                <td class="small left">
                    {{ rtrim(rtrim(number_format($item->quantity, 3, ',', '.'), '0'), ',') }}
                    x ₲ {{ number_format($item->unit_price, 0, ',', '.') }}
                </td>
                <td class="right">
                    ₲ {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <!-- =========================
         💰 TOTAL
    ========================== -->
    <div class="total">
        TOTAL: ₲ {{ number_format($sale->total_amount, 0, ',', '.') }}
    </div>

    <!-- =========================
         💳 PAGO / VUELTO
    ========================== -->
    @php
        $symbols = ['PYG' => '₲', 'USD' => '$', 'BRL' => 'R$'];
        $symbol  = $symbols[$sale->payment_currency] ?? '';
        $dec     = $sale->payment_currency === 'PYG' ? 0 : 2;

        $pagado = (float) $sale->paid_amount;
        $totalEnMoneda = ($sale->payment_currency === 'PYG')
            ? $sale->total_amount
            : round($sale->total_amount / $sale->exchange_rate, 2);

        $vuelto = max($pagado - $totalEnMoneda, 0);
    @endphp

    <div class="small left">
        Forma de pago: {{ ucfirst($sale->payment_method) }}<br>
        Moneda: {{ $sale->payment_currency }}<br>

        @if ($sale->payment_currency !== 'PYG')
            Tipo de cambio: ₲ {{ number_format($sale->exchange_rate, 0, ',', '.') }}<br>
        @endif

        Cliente paga: {{ $symbol }} {{ number_format($pagado, $dec, ',', '.') }}<br>
        Vuelto: {{ $symbol }} {{ number_format($vuelto, $dec, ',', '.') }}
    </div>

    <div class="separator"></div>

    <!-- =========================
         🙏 MENSAJE FINAL
    ========================== -->
    <div class="center small">
        ¡Gracias por su compra!<br>
        Vuelva pronto 🙂
    </div>

</div>
</body>
</html>
