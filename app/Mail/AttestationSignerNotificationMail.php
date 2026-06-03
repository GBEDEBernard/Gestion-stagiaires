<?php

namespace App\Mail;

use App\Models\Attestation;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttestationSignerNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $signataire;
    public Stage $stage;
    public Attestation $attestation;

    public function __construct(User $signataire, Stage $stage, Attestation $attestation)
    {
        $this->signataire = $signataire;
        $this->stage = $stage;
        $this->attestation = $attestation;
    }

    public function build()
    {
        return $this->subject('Nouvelle attestation de stage à signer')
            ->view('emails.attestation_signer_notification');
    }
}
