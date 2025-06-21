<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvatarUpload extends Component
{
    use WithFileUploads;

    public $avatar;

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $filename = uniqid() . '.' . $this->avatar->getClientOriginalExtension();
        $this->avatar->storeAs('avatars', $filename, 'public');

        $user->avatar = $filename;
        $user->save();

        $this->redirect(request()->header('Referer'), navigate: true);
    }


    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $user->avatar = null;
        $user->save();

        session()->flash('success', 'Аватарът беше изтрит успешно!');
    }


    public function render()
    {
        return view('livewire.profile.avatar-upload', [
            'avatarUrl' => Auth::user()?->avatar
                ? asset('storage/avatars/' . Auth::user()->avatar)
                : asset('images/logo/logo.jpg'),
        ]);
    }
}
