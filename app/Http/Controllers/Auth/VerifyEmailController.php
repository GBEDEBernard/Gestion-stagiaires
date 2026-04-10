<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if (!$request->user()->hasVerifiedEmail() && $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if ($request->user()->requiresPasswordChange()) {
            return redirect()->route('password.first.edit')->with('success', 'Email verifie avec succes.');
        }

        return redirect()->route($request->user()->homeRouteName())->with('success', 'Email verifie avec succes.');
    }
}
