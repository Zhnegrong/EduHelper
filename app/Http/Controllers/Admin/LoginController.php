<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * public methods for routes
     *
     */
    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('guest:admin',['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        return $this->sendFailedLoginResponse($request);
    }
    /**
     * protected methods
     *
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email'=> 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::guard('admin')->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    protected function sendLoginResponse(Request $request)
    {
         $request->session()->regenerate();
        //$this->clearLoginAttempts($request);
       
        return $this->authenticated($request, Auth::guard('admin')->user())
            ?: redirect()->intended(route('admin.dashboard'));
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [ 'email' => trans('auth.failed')];
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors($errors);
    }

    protected function authenticated(Request $request, $user)
    {
    }
}
