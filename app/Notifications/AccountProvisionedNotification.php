<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountProvisionedNotification extends Notification
{
    protected $token;
    protected ?string $email;
    protected ?string $temporaryPassword;

    public function __construct($token, ?string $email = null, ?string $temporaryPassword = null)
    {
        $this->token = $token;
        $this->email = $email;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email ?? $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject("TECHNOLOGY FOREVER GROUP - Vos identifiants d'accès à la plateforme de présence et de suivi d'activité")
            ->markdown('emails.account_provisioned', [
                'fullName'  => $notifiable->name,
                'email'     => $notifiable->email,
                'password'  => $this->temporaryPassword,
                'resetUrl'  => $resetUrl,
            ]);
    }
}