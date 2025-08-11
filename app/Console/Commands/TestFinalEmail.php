<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Invoice;
use App\Mail\InvoiceMail;

class TestFinalEmail extends Command
{
    protected $signature = 'test:final-email';
    protected $description = 'Test final email sending to client';

    public function handle()
    {
        $this->info("🚀 Prueba final del sistema de envío de emails con PDF");

        try {
            // Obtener una factura con todas sus relaciones
            /** @var Invoice|null $invoice */
            $invoice = Invoice::with(['client', 'user', 'items.product'])->first();

            if (!$invoice) {
                $this->error("No hay facturas en la base de datos!");
                return 1;
            }
            /** @var \App\Models\Client $client */
            $client = $invoice->client;


            $this->info("📄 Factura: {$invoice->invoice_number}");
            $this->info("👤 Cliente: {$client->name}");
            $this->info("📧 Email del cliente: {$client->email}");
            $this->info("💰 Total: $" . number_format((float)$invoice->total, 2));


            // Enviar email al cliente real
            $this->info("📤 Enviando email al cliente...");

            Mail::to($client->email)->send(new InvoiceMail($invoice));

            $this->info("✅ ¡Email enviado exitosamente al cliente!");
            $this->info("📬 El cliente recibirá el email con el PDF adjunto en: {$client->email}");

            // También enviar una copia al administrador para verificación
            $this->info("📤 Enviando copia al administrador...");
            Mail::to('luisorlo1997@gmail.com')->send(new InvoiceMail($invoice));

            $this->info("✅ ¡Copia enviada al administrador!");

            $this->line("");
            $this->info("🎉 ¡Prueba completada exitosamente!");
            $this->info("📋 Resumen:");
            $this->info("  - Email enviado al cliente: {$client->email}");
            $this->info("  - Copia enviada al admin: luisorlo1997@gmail.com");
            $this->info("  - PDF adjunto: factura-{$invoice->invoice_number}.pdf");
        } catch (\Exception $e) {
            $this->error("❌ Error enviando email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
