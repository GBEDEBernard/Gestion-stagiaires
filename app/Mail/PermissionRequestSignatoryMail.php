<?php

namespace App\Mail;

use App\Models\PermissionRequest;
use App\Models\Signataire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PermissionRequestSignatoryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public PermissionRequest $permissionRequest,
        public Signataire $signataire,
        public string $pdfPath
    ) {
    }

    public function build(): self
    {
        $mail = $this
            ->subject('Demande de permission - ' . $this->permissionRequest->requester->name)
            ->view('emails.permission-request-signatory');

        if (Storage::disk('local')->exists($this->pdfPath)) {
            $mail->attach(Storage::disk('local')->path($this->pdfPath), [
                'as' => 'demande-permission-' . $this->permissionRequest->id . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
