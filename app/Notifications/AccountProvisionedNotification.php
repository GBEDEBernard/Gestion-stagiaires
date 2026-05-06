<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AccountProvisionedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $temporaryPassword
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // jb -> Un seul mail d'onboarding:
        // il donne les identifiants initiaux et porte aussi le lien
        // de verification pour garder un parcours simple et clair.
        return (new MailMessage)
            ->subject('Activation de votre compte - Gestion Stagiaires')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Un compte vient d\'etre cree pour vous sur la plateforme Gestion Stagiaires.')
            ->line('Votre email de connexion est : ' . $notifiable->email)
            ->line('Votre mot de passe temporaire est : ' . $this->temporaryPassword)
            ->line('Etapes a suivre :')
            ->line('1. Cliquez d\'abord sur le bouton ci-dessous pour verifier votre adresse email.')
            ->line('2. Connectez-vous ensuite avec votre email et votre mot de passe temporaire.')
            ->line('3. Une fois connecte, l\'application vous obligera a choisir votre mot de passe personnel.')
            ->action('Verifier mon email', $verificationUrl)
            ->line('Ce lien de verification expire dans 60 minutes.')
            ->line('Si vous n\'etes pas a l\'origine de cette demande, vous pouvez ignorer ce message.')
            ->salutation('Cordialement, L\'equipe Gestion Stagiaires');
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
