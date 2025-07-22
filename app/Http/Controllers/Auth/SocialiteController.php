<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SocialAccount;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return $this->redirectToProvider('google');
    }

    public function handleGoogleCallback()
    {
        return $this->handleProviderCallback('google');
    }

    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function redirectToFacebook()
    {
        return $this->redirectToProvider('facebook');
    }

    public function handleFacebookCallback()
    {
        return $this->handleProviderCallback('facebook');
    }


    public function handleProviderCallback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => bcrypt(str()->random(24)),
            ]
        );

        SocialAccount::firstOrCreate([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ], [
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        return redirect()->route('profile')->with('logged_in', true);
    }
}
