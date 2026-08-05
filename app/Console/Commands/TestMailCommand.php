<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Adresse email du destinataire (défaut : MAIL_FROM_ADDRESS)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un email de test pour vérifier la configuration SMTP (synchrone, sans file d\'attente)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $to = $this->argument('email') ?: config('mail.from.address');

        $this->info('=== Configuration SMTP actuelle ===');
        $this->line('Mailer      : ' . config('mail.default'));
        $this->line('Host        : ' . config('mail.mailers.smtp.host'));
        $this->line('Port        : ' . config('mail.mailers.smtp.port'));
        $this->line('Username    : ' . config('mail.mailers.smtp.username'));
        $this->line('From address: ' . config('mail.from.address'));
        $this->line('From name   : ' . config('mail.from.name'));
        $this->line('Queue       : ' . config('queue.default'));
        $this->newLine();

        if (!$to) {
            $this->error('Aucune adresse email configurée. Utilisez : php artisan mail:test "destinataire@exemple.com"');
            return self::FAILURE;
        }

        $this->info("Envoi d'un email de test vers : {$to} ...");

        try {
            Mail::to($to)->send(new TestMail());
            $this->info('✅ Email envoyé avec succès !');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Échec de l\'envoi de l\'email.');
            $this->error('Erreur : ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
