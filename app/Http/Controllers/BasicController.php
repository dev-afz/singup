<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class BasicController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }
    public function githubCallback(Request $request)
    {
        try {
            $user = Socialite::driver('github')->user();
            $finduser = User::where('github_id', $user->id)->first();
            if ($finduser) {
                Auth::login($user);

                return redirect()->intended('dashboard');
            } else {
                $newUser = User::updateOrCreate([
                    'name' => $user->name ?? $user->nickname,
                    'github_id' => $user->id,
                    'email' => $user->email,
                    'password' => encrypt('1234'),
                ]);

                Auth::login($newUser);

                return redirect()->intended('dashboard');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
