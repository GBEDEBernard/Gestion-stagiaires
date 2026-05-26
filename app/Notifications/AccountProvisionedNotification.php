<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountProvisionedNotification extends Notification
{
    use Queueable;

    protected $token;
    protected ?string $email;

    public function __construct($token, ?string $email = null)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email ?? $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Activation de votre compte - Gestion Stagiaires')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Un compte est disponible pour vous sur la plateforme.')
            ->line('Votre email de connexion est : ' . $notifiable->email)
            ->line('Pour définir votre mot de passe, cliquez sur le bouton ci-dessous :')
            ->action('Configurer mon mot de passe', $url)
            ->line('Ce lien expire dans 60 minutes.')
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce message.')
            ->salutation('Cordialement, L\'équipe Gestion Stagiaires');
    }
}
