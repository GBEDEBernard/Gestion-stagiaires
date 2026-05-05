<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmation;
use Illuminate\Http\RedirectResponse;
use App\Models\Contact;

class ContactController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'subject.required' => 'Le sujet est requis.',
            'message.required' => 'Le message est requis.',
        ]);

        // Enregistrer le message dans la base de données
        $contact = Contact::create($validated);

        // Envoyer l'email de confirmation à l'utilisateur
        Mail::to($validated['email'])->send(new ContactConfirmation($validated));

        return redirect()->back()->with('status', 'contact-sent');
    }

    // Affichage pour l'admin
    public function afficher()
    {
        $contacts = Contact::latest()->get(); // tri du plus récent au plus ancien
        return view('admin.contacts.index', compact('contacts'));
    }
}