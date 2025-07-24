<?php

namespace App\Livewire\Pages;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Contact extends Component
{
    public $name, $email, $message;

    public array $layoutData = [];

    public $recaptchaToken;


    public function mount()
    {
        $this->layoutData = [
            'title' => 'Контакти | CSKA FAN TV',
            'description' => 'Свържи се с екипа на CSKA FAN TV. Пиши ни при въпроси, предложения или за съдействие относно съдържанието.',
            'robots' => 'index, follow',
            'canonical' => url('/contact'),
            'og_title' => 'Контакт с нас | CSKA FAN TV',
            'og_description' => 'Имаш въпрос или предложение? Свържи се с нас чрез контактната форма на CSKA FAN TV.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/contact'),
            'og_type' => 'website',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'message' => ['required', 'min:10'],
        ];
    }

    public function submit()
    {
        $this->validate();

        $captchaToken = $this->recaptchaToken;

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $captchaToken,
            'remoteip' => request()->ip(),
        ]);

        if (!($response->json('success') ?? false)) {
            session()->flash('error', 'Неуспешна проверка на reCAPTCHA. Опитай отново.');
            return;
        }

        Mail::to(config('mail.admin_address'))
            ->send(new ContactMessage($this->name, $this->email, $this->message));

        $this->reset(['name', 'email', 'message', 'recaptchaToken']);
        session()->flash('success', 'Съобщението беше изпратено успешно!');
    }



    public function render()
    {
        return view('livewire.pages.contact')
            ->layout('layouts.app', $this->layoutData);
    }
}
