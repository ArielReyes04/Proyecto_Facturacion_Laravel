<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .invoice-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            width: 48%;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .products-table th,
        .products-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .products-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .products-table td:nth-child(2),
        .products-table td:nth-child(3),
        .products-table td:nth-child(4) {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .totals .total-row {
            border-top: 2px solid #333;
            padding-top: 10px;
            font-weight: bold;
            font-size: 16px;
        }
        .status {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status.active {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .cancellation-info {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEMA DE FACTURACIÓN</h1>
        <div class="invoice-number">FACTURA {{ $invoice->invoice_number }}</div>
        <p>Fecha: {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="status {{ $invoice->status }}">
        {{ $invoice->status == 'active' ? 'FACTURA ACTIVA' : 'FACTURA CANCELADA' }}
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>INFORMACIÓN DEL CLIENTE</h3>
            <p><strong>Nombre:</strong> {{ $invoice->client?->name ?? 'Cliente no disponible' }}</p>
            <p><strong>Email:</strong> {{ $invoice->client->email }}</p>
            <p><strong>Documento:</strong> {{ $invoice->client->document_type }}: {{ $invoice->client->document_number }}</p>
            @if($invoice->client->phone)
                <p><strong>Teléfono:</strong> {{ $invoice->client->phone }}</p>
            @endif
            @if($invoice->client->address)
                <p><strong>Dirección:</strong> {{ $invoice->client->address }}</p>
            @endif
        </div>

        <div class="info-box">
            <h3>INFORMACIÓN DEL VENDEDOR</h3>
            <p><strong>Nombre:</strong> {{ $invoice->user?->name ?? 'Usuario no disponible' }}</p>
            <p><strong>Email:</strong> {{ $invoice->user->email }}</p>
            <p><strong>Fecha de emisión:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>S/ {{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>S/ {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Subtotal:</span>
            <span>S/ {{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        <div class="row">
            <span>IVA (15%):</span>
            <span>S/ {{ number_format($invoice->tax, 2) }}</span>
        </div>
        <div class="row total-row">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->isCancelled())
        <div class="cancellation-info">
            <h3>INFORMACIÓN DE CANCELACIÓN</h3>
            <p><strong>Fecha de cancelación:</strong> {{ $invoice->cancelled_at->format('d/m/Y H:i') }}</p>
            <p><strong>Cancelada por:</strong> {{ $invoice->cancelledBy?->name ?? 'Usuario no disponible' }}</p>
            <p><strong>Motivo:</strong> {{ $invoice->cancellation_reason }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Este documento fue generado automáticamente por el Sistema de Facturación.</p>
        <p>{{ config('app.name') }} - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
