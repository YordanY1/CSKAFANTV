<?php

namespace App\Livewire\Pages;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Contact extends Component
{
    public $name, $email, $message;

    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Контакти | CSKA FAN TV',
            'description' => 'Свържи се с екипа на CSKA FAN TV. Пиши ни при въпроси, предложения или за съдействие относно съдържанието.',
            'robots' => 'index, follow',
            'canonical' => url('/contact'),
            'og_title' => 'Контакт с нас | CSKA FAN TV',
            'og_description' => 'Имаш въпрос или предложение? Свържи се с нас чрез контактната форма на CSKA FAN TV.',
            'og_image' => asset('images/og-cska.jpg'),
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

    public function submit(): void
    {
        $this->validate();
        $this->sendContactEmail();
        $this->resetForm();
        session()->flash('success', 'Съобщението беше изпратено успешно!');
    }

    protected function sendContactEmail(): void
    {
        Mail::to(config('mail.admin_address'))
            ->send(new ContactMessage($this->name, $this->email, $this->message));
    }

    protected function resetForm(): void
    {
        $this->reset(['name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.pages.contact')
            ->layout('layouts.app', $this->layoutData);
    }
}
