<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactConfirmation extends Mailable
{
    public $contact;

    /**
     * Crée une nouvelle instance de message.
     *
     * @param array $contact
     */
    public function __construct(array $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Construit le message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('gbedebernard60@gmail.com', 'Gestion des Stagiaires')
                    ->subject('Confirmation de votre message')
                    ->markdown('emails.contact-confirmation')
                    ->with([
                        'name' => $this->contact['name'],
                        'message' => $this->contact['message'],
                    ]);
    }
}