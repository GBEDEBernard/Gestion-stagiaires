<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Support\NotificationGreeting;

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

    // Décoder le mot de passe avant de l'envoyer à la vue
    $decodedPassword = html_entity_decode($this->temporaryPassword, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return (new MailMessage)
        ->subject("TECHNOLOGY FOREVER GROUP - Vos identifiants d'accès à la plateforme de présence et de suivi d'activité")
        ->view('emails.account_provisioned_html', [
            'fullName'  => $notifiable->name,
            'civility'  => NotificationGreeting::civilityForRecipient($notifiable),
            'email'     => $notifiable->email,
            'password'  => $decodedPassword,
            'resetUrl'  => $resetUrl,
        ]);
}
}