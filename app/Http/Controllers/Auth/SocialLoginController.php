<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    private array $allowedProviders = ['google', 'facebook'];

    public function redirectToProvider(string $provider, Request $request)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        session(['social_redirect_to' => $request->input('redirect_to', '/')]);

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider, Request $request)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        $redirectTo = session('social_redirect_to', '/');
        $request->session()->forget('social_redirect_to');

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['social' => 'تعذر تسجيل الدخول باستخدام ' . ($provider === 'google' ? 'Google' : 'Facebook') . '. يرجى المحاولة مرة أخرى.']);
        }

        $email = $socialUser->getEmail();
        if (!$email) {
            $msg = $provider === 'facebook'
                ? 'تعذر الحصول على البريد الإلكتروني من Facebook. يرجى استخدام تسجيل الدخول العادي أو Google.'
                : 'تعذر الحصول على البريد الإلكتروني.';
            return redirect()->route('login')
                ->withErrors(['social' => $msg]);
        }

        $user = $this->findOrCreateUser($socialUser, $provider);

        Auth::login($user);

        return redirect()->intended($redirectTo);
    }

    private function findOrCreateUser($socialUser, string $provider): User
    {
        $field = $provider . '_id';
        $id = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName();
        $avatar = $socialUser->getAvatar();

        $existingById = User::where($field, $id)->first();
        if ($existingById) {
            return $existingById;
        }

        $existingByEmail = User::where('email', $email)->first();
        if ($existingByEmail) {
            $updateData = [$field => $id];
            if ($existingByEmail->avatar === null) {
                $updateData['avatar'] = $avatar;
            }
            $existingByEmail->update($updateData);
            return $existingByEmail;
        }

        $customerRoleId = \App\Models\Role::where('name', 'customer')->value('id');

        return User::create([
            'name' => $name ?? explode('@', $email)[0],
            'email' => $email,
            'password' => Hash::make(Str::random(24)),
            $field => $id,
            'avatar' => $avatar,
            'role_id' => $customerRoleId,
        ]);
    }
}
