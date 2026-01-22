<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPasswordResetPin extends Notification
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
            ->subject('Code de réinitialisation de mot de passe')
            ->line('Bonjour!')
            ->line('Vous avez demandé à réinitialiser votre mot de passe.')
            ->line('Voici votre code de vérification (valide pendant 15 minutes) :')
            ->line('')
            ->line('**Code : ' . $this->pin . '**')
            ->line('')
            ->line('Ne partagez ce code avec personne. Si vous n\'avez pas demandé de réinitialisation, ignorez cet email.');
    }
}
