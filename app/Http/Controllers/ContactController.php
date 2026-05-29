<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            Mail::to(config('mail.support_email'))->send(new ContactFormMail(
                senderName: $data['name'],
                senderEmail: $data['email'],
                contactSubject: $data['subject'],
                contactMessage: $data['message'],
            ));
        } catch (\Throwable $e) {
            Log::error('Contact form email failed to send', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return back()->with('error', 'Sorry, we could not send your message right now. Please try again later or email us directly.');
        }

        return back()->with('success', 'Thank you for contacting us! We will respond within 24 hours.');
    }
}
