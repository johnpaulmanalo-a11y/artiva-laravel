<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        // Naka-save pa rin sa database bilang backup, tapos i-email din.
        $contactMessage = ContactMessage::create($data);

        try {
            Mail::to(config('mail.contact_to'))->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $e) {
            // Hindi mawawala ang message kahit pumalya ang email; nasa DB pa rin.
            Log::error('Contact email failed: '.$e->getMessage());
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you, '.$data['name'].'. Your message is with the Artiva team and we will reply within two business days.');
    }
}
