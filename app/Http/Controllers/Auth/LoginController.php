<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "/";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $remember = $request->has('remember');
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if (!$user->status) {
                auth()->logout();
                return back()->with('error', 'Tu cuenta está desactivada. Por favor, contacta al administrador.');
            }
            PersonalAccessToken::where('tokenable_id', $user->id)->delete();
            $token = $user->createToken('NombreDeTuToken')->accessToken;
            $cookie = cookie('authorization_token', $token, 60); 
            $request->session()->regenerate();
 
            return redirect()->intended()->withCookie($cookie);
        }
 
        return back()->withErrors([
            'email' => [trans('auth.failed')],
        ])->onlyInput('email');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->status) {
            auth()->logout();
            return back()->with('error', 'Tu cuenta está desactivada. Por favor, contacta al administrador.');
        }

        return redirect()->intended();
    }
}
