<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class TestPdfEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pdf-email {invoice_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PDF generation and email sending for invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->argument('invoice_id');

        if ($invoiceId) {
            /** @var Invoice|null $invoice */
            $invoice = Invoice::with(['client', 'user', 'items.product'])->find($invoiceId);
            if (!$invoice) {
                $this->error("Invoice with ID {$invoiceId} not found.");
                return;
            }
        } else {
            /** @var Invoice|null $invoice */
            $invoice = Invoice::with(['client', 'user', 'items.product'])->first();
            if (!$invoice) {
                $this->error("No invoices found in the database.");
                return;
            }
        }
        
        /** @var \App\Models\Client $client */
        $client = $invoice->client;


        $this->info("Testing with Invoice: {$invoice->invoice_number}");
        $this->info("Client: {$client->name}");
        $this->info("Email: {$client->email}");

        // Test PDF generation
        $this->info("Testing PDF generation...");
        try {
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
            $this->info("✅ PDF generated successfully!");
        } catch (\Exception $e) {
            $this->error("❌ PDF generation failed: " . $e->getMessage());
            return;
        }

        // Test email sending
        $this->info("Testing email sending...");
        try {
            Mail::to($client->email)->send(new InvoiceMail($invoice));
            $this->info("✅ Email sent successfully!");
            $this->info("Check your logs at storage/logs/laravel.log for email details.");
        } catch (\Exception $e) {
            $this->error("❌ Email sending failed: " . $e->getMessage());
            return;
        }

        $this->info("🎉 All tests passed!");
    }
}
