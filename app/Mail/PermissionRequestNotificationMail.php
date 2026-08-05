<?php

namespace App\Mail;

use App\Models\PermissionRequest;
use App\Models\Signataire;
use Illuminate\Mail\Mailable;

class PermissionRequestNotificationMail extends Mailable
{
    public function __construct(
        public PermissionRequest $request,
        public Signataire $signataire,
    ) {}

    public function build()
    {
        return $this
            ->subject("Nouvelle demande de permission – {$this->request->type->name}")
            ->view('emails.permission_request_notification');
    }
}
