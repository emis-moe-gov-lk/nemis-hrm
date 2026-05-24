<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactForm extends Component
{
    public string $name    = '';
    public string $email   = '';
    public string $message = '';

    public bool $sent    = false;
    public bool $hasError = false;

    protected function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'    => 'Please enter your full name.',
            'email.required'   => 'Please enter your work email.',
            'email.email'      => 'Please enter a valid email address.',
            'message.required' => 'Please write your message.',
            'message.min'      => 'Your message must be at least 10 characters.',
        ];
    }

    public function sendMessage(): void
    {
        $this->validate();

        $recipient = config('mail.contact_address', config('mail.from.address'));

        try {
            Mail::to($recipient)->send(
                new ContactFormMail(
                    senderName:    $this->name,
                    senderEmail:   $this->email,
                    userMessage:   $this->message,
                )
            );

            $this->reset(['name', 'email', 'message']);
            $this->sent     = true;
            $this->hasError = false;

        } catch (\Throwable $e) {
            $this->hasError = true;
            report($e);
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
