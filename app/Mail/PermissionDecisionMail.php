<?php

namespace App\Mail;

use App\Models\PermissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PermissionDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermissionRequest $request,
        public string $decision,
    ) {}

    public function build()
    {
        $label = $this->decision === 'approved' ? 'approuvée' : 'refusée';
        return $this
            ->subject("Votre demande de permission a été {$label}")
            ->view('emails.permission_decision');
    }
}
