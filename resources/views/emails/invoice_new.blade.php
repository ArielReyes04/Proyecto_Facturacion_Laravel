<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .invoice-details {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #667eea;
            margin-bottom: 25px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .invoice-info strong {
            color: #2d3748;
        }
        .amount {
            font-size: 24px;
            font-weight: 700;
            color: #38a169;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f0fff4;
            border-radius: 6px;
            border: 2px solid #68d391;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            color: #718096;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📧 Nueva Factura</h1>
            <p>Factura {{ $invoice->invoice_number }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Estimado/a {{ $invoice->client->name }},
            </div>

            <p>Esperamos que se encuentre bien. Le adjuntamos su factura correspondiente con los siguientes detalles:</p>

            <div class="invoice-details">
                <div class="invoice-info">
                    <span><strong>Número de Factura:</strong></span>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                <div class="invoice-info">
                    <span><strong>Fecha de Emisión:</strong></span>
                    <span>{{ $invoice->issue_date->format('d/m/Y') }}</span>
                </div>
                <div class="invoice-info">
                    <span><strong>Fecha de Vencimiento:</strong></span>
                    <span>{{ $invoice->due_date->format('d/m/Y') }}</span>
                </div>
                <div class="invoice-info">
                    <span><strong>Estado:</strong></span>
                    <span>
                        @if($invoice->status === 'active')
                            Activa ✅
                        @elseif($invoice->status === 'cancelled')
                            Cancelada ❌
                        @else
                            {{ $invoice->status }} (Debug)
                        @endif
                    </span>
                </div>
            </div>

            <div class="amount">
                💰 Total: ${{ number_format($invoice->total, 2) }}
            </div>

            <p>En el archivo PDF adjunto encontrará todos los detalles de los productos/servicios facturados.</p>

            @if($invoice->status === 'active')
                <p><strong>Información de pago:</strong> Por favor, proceda con el pago antes de la fecha de vencimiento para evitar cargos adicionales.</p>
            @endif

            <p>Si tiene alguna pregunta sobre esta factura, no dude en contactarnos.</p>

            <p>Atentamente,<br>
            <strong>{{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>Este es un email automático, por favor no responda a esta dirección.</p>
            <p>{{ config('app.name') }} - Sistema de Facturación</p>
            <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
