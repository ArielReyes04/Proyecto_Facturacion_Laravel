<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailConfig extends Command
{
    protected $signature = 'test:email-config {email}';
    protected $description = 'Test basic email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email configuration...");
        $this->info("To: {$email}");
        
        // Mostrar configuración actual
        $this->info("Mail Driver: " . config('mail.default'));
        $this->info("Mail Host: " . config('mail.mailers.smtp.host'));
        $this->info("Mail Port: " . config('mail.mailers.smtp.port'));
        $this->info("Mail Username: " . config('mail.mailers.smtp.username'));
        $this->info("Mail From: " . config('mail.from.address'));
        
        try {
            Mail::raw('Este es un email de prueba desde Laravel.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Prueba de configuración de email - ' . config('app.name'));
            });
            
            $this->info("✅ Email enviado exitosamente!");
            $this->info("Revisa tu bandeja de entrada en: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ Error enviando email: " . $e->getMessage());
            
            // Mostrar información adicional para debugging
            if (str_contains($e->getMessage(), 'authentication')) {
                $this->warn("💡 Sugerencia: Verifica que estés usando una contraseña de aplicación de Gmail, no tu contraseña normal.");
            }
            
            if (str_contains($e->getMessage(), 'Connection timed out')) {
                $this->warn("💡 Sugerencia: Verifica tu conexión a internet y que el puerto 587 no esté bloqueado.");
            }
            
            return 1;
        }
        
        return 0;
    }
}
