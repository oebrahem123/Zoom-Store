<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected function authenticated($request, $user)
    {
        \Log::info('[CHECKPOINT-4] LoginController@authenticated()', [
            'user_id' => $user->id,
            'redirect_to_input' => $request->input('redirect_to'),
            'redirect_to_filled' => $request->filled('redirect_to'),
            'session_guest_cart_ids' => session('guest_cart_ids', []),
            'intended_url' => session('url.intended'),
        ]);

        $guestCartIds = session('guest_cart_ids', []);
        if (! empty($guestCartIds)) {
            Cart::whereIn('id', $guestCartIds)->update(['user_id' => $user->id]);
            session()->forget('guest_cart_ids');
        }

        if ($request->filled('redirect_to')) {
            $destination = $request->redirect_to;
            \Log::info('[CHECKPOINT-5] Destination URL after login (redirect_to branch)', ['url' => $destination]);
            return redirect($destination);
        }

        $fallback = '/';
        \Log::info('[CHECKPOINT-5] Destination URL after login (fallback branch)', ['url' => $fallback, 'redirectPath' => $this->redirectPath()]);
        return redirect($fallback);
    }

    public function redirectPath()
    {
        return '/';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
