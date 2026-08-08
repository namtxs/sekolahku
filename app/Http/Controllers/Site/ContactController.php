<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\NewContactMessageAdminMail;
use App\Models\ContactMessage;
use App\Support\SiteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $message = ContactMessage::create([...$data, 'is_read' => false]);

        // Beri tahu admin via antrean; skip bila email sekolah belum diisi.
        if ($adminEmail = SiteMail::adminEmail()) {
            Mail::to($adminEmail)->queue(new NewContactMessageAdminMail($message));
        }

        return redirect()
            ->route('contact.show')
            ->with('contact_status', 'Pesan Anda telah terkirim. Terima kasih!');
    }
}
