<?php

namespace App\Mail;

use App\Models\Stage;
use App\Models\User;
use App\Models\Attestation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttestationSignerNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $signer;
    public $stage;
    public $attestation;
    public $signatureUrl;

    public function __construct(User $signer, Stage $stage, Attestation $attestation)
    {
        $this->signer = $signer;
        $this->stage = $stage;
        $this->attestation = $attestation;
        $this->signatureUrl = route('attestation.sign', [
            'attestation' => $attestation->id,
            'signer' => $signer->id,
            'token' => $this->generateSignatureToken($signer, $attestation)
        ]);
    }

    protected function generateSignatureToken($signer, $attestation)
    {
        return hash_hmac('sha256', $signer->id . $attestation->id, config('app.key'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✍️ Demande de signature - Attestation de stage - {$this->stage->etudiant->personnel->nom}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attestation_signer_notification',
        );
    }
}