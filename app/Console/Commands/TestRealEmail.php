<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Invoice;
use App\Models\Client;
use App\Mail\InvoiceMail;

class TestRealEmail extends Command
{
    protected $signature = 'test:real-email {email} {--invoice-id=}';
    protected $description = 'Test sending a real email with invoice PDF attachment';

    public function handle()
    {
        $email = $this->argument('email');
        $invoiceId = $this->option('invoice-id');

        $this->info("Testing real email sending to: {$email}");

        try {
            // Verificar configuración de email
            $this->info("Checking email configuration...");
            $this->info("Mail Driver: " . config('mail.default'));
            $this->info("Mail Host: " . config('mail.mailers.smtp.host'));
            $this->info("Mail Port: " . config('mail.mailers.smtp.port'));
            $this->info("Mail Username: " . config('mail.mailers.smtp.username'));
            $this->info("Mail From: " . config('mail.from.address'));

            // Obtener o crear una factura de prueba
            if ($invoiceId) {
                $invoice = Invoice::with(['client', 'user', 'items.product'])->find($invoiceId);
                if (!$invoice) {
                    $this->error("Invoice with ID {$invoiceId} not found!");
                    return 1;
                }
            } else {
                $invoice = Invoice::with(['client', 'user', 'items.product'])->first();
                if (!$invoice) {
                    $this->error("No invoices found in the database!");
                    return 1;
                }
            }

            /** @var \App\Models\Client $client */
            $client = $invoice->client;


            $this->info("Using invoice: {$invoice->invoice_number}");
            $this->info("Original client email: {$client->email}");

            // Enviar email
            $this->info("Sending email...");

            Mail::to($email)->send(new InvoiceMail($invoice));

            $this->info("✅ Email sent successfully!");
            $this->info("Check your inbox at: {$email}");
        } catch (\Exception $e) {
            $this->error("❌ Error sending email: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
