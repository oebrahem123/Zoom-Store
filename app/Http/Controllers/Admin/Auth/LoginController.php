<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function redirectPath()
    {
        return route('admin.index');
    }

    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['is_active' => true]
        );
    }

    public function logout(Request $request)
    {
        $wasLoggedIn = Auth::guard('admin')->check();

        Auth::guard('admin')->logout();

        if ($wasLoggedIn) {
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login');
    }
}
