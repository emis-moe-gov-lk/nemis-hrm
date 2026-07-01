<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function testForm()
    {
        return view('mail-test');
    }

    public function sendMailTest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        try {
            Mail::raw($request->message, function ($mail) use ($request) {
                $mail->to($request->email)
                     ->subject($request->subject);
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->email . '. Check your inbox!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending email: ' . $e->getMessage());
        }
    }
}
