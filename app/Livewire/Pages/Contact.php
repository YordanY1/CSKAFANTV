<?php

namespace App\Livewire\Pages;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;


class Contact extends Component
{
    public $name, $email, $message;

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'message' => ['required', 'min:10'],
        ];
    }


    public function submit(): void
    {
        $this->validate();
        $this->sendContactEmail();
        $this->resetForm();
        session()->flash('success', 'Съобщението беше изпратено успешно!');
    }



    protected function sendContactEmail(): void
    {
        Log::info('Опит за изпращане на имейл до: ' . config('mail.admin_address'));

        Mail::to(config('mail.admin_address'))
            ->send(new ContactMessage($this->name, $this->email, $this->message));
    }

    protected function resetForm(): void
    {
        $this->reset(['name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.pages.contact')->layout('layouts.app');
    }
}
