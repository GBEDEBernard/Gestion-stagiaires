<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Get the reset password notification mail message for the given token.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notification de réinitialisation de mot de passe')
            ->greeting('Bonjour!')
            ->line('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser le mot de passe', url(config('app.url') . route('password.reset', [$this->token], false)))
            ->line('Ce lien de réinitialisation de mot de passe expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n\'est requise.')
            ->salutation('Cordialement,')
            ->markdown('notifications.reset-password');
    }
}
