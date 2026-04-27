<?php

namespace App\Mail;

use App\Models\PermissionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PermissionRequestReviewMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public PermissionRequest $permissionRequest,
        public User $recipient,
        public string $pdfPath,
        public string $recipientRoleLabel
    ) {
    }

    public function build(): self
    {
        $mail = $this
            ->subject('Nouvelle demande de permission - ' . $this->permissionRequest->requester->name)
            ->view('emails.permission-request-review');

        if (Storage::disk('local')->exists($this->pdfPath)) {
            $mail->attach(Storage::disk('local')->path($this->pdfPath), [
                'as' => 'demande-permission-' . $this->permissionRequest->id . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
