<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AccountProvisionedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $resetUrl
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Envoi professionnel : action principale pour configurer le mot de passe
        // et inclusion du lien de vérification d'email.
        return (new MailMessage)
            ->subject('Activation de votre compte - Gestion Stagiaires')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Un compte a été créé pour vous sur la plateforme Gestion Stagiaires.')
            ->line('Votre email de connexion est : ' . $notifiable->email)
            ->line('Pour activer votre compte et définir votre mot de passe, cliquez sur le bouton ci-dessous :')
            ->action('Configurer mon mot de passe', $this->resetUrl)
            ->line('Ensuite, pensez à vérifier votre adresse email en utilisant ce lien (valable 60 minutes) :')
            ->line($verificationUrl)
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce message.')
            ->salutation('Cordialement, L\'équipe Gestion Stagiaires');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
