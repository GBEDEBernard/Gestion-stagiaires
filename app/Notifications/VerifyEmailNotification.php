<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channel.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // jb -> Ce mail est reserve au renvoi du lien de verification
        // apres la creation initiale du compte. Le mail initial d'onboarding
        // est gere a part pour inclure aussi les identifiants temporaires.
        return (new MailMessage)
            ->subject('Activation de votre compte - Gestion Stagiaires')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Merci de créer un compte sur notre plateforme Gestion Stagiaires.')
            ->line('Cliquez sur le bouton ci-dessous pour activer votre compte:')
            ->action('Activer mon compte', $verificationUrl)
            ->line('Ce lien expire dans 60 minutes.')
            ->line('Si vous n\'avez pas créé de compte, vous pouvez ignorer cet email.')
            ->salutation('Cordialement, L\'équipe Gestion Stagiaires');
    }

    /**
     * Get the verification URL for the notifiable.
     */
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
