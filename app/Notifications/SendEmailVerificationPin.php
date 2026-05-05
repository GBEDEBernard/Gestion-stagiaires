<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailVerificationPin extends Notification
{
    public $pin;

    /**
     * Create a new notification instance.
     */
    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    /**
     * Get the notification's delivery channels.
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
        return (new MailMessage)
            ->subject('Vérification de votre email - Code PIN')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Merci de créer un compte sur notre plateforme Gestion Stagiaires.')
            ->line('Voici votre code de vérification à 4 chiffres:')
            ->line('')
            ->line('**Code : ' . $this->pin . '**')
            ->line('')
            ->line('Ce code expire dans 30 minutes.')
            ->line('Ne partagez ce code avec personne.')
            ->line('Si vous n\'avez pas créé de compte, vous pouvez ignorer cet email.')
            ->salutation('Cordialement, L\'équipe Gestion Stagiaires');
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
