<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return theme_view('contact');
    }

    public function store(Request $request)
    {
        // Honeypot: bot mengisi field tersembunyi ini — pura-pura sukses.
        if (filled($request->input('website'))) {
            return redirect()
                ->route('contact.show')
                ->with('contact_status', 'Pesan Anda telah terkirim. Terima kasih!');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ], [], [
            'name' => 'nama',
            'email' => 'email',
            'phone' => 'telepon',
            'subject' => 'subjek',
            'message' => 'pesan',
        ]);

        ContactMessage::create([...$data, 'is_read' => false]);

        return redirect()
            ->route('contact.show')
            ->with('contact_status', 'Pesan Anda telah terkirim. Terima kasih!');
    }
}
