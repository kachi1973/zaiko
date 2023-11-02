<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use App\Models\System;
use App\Models\User;
use App\Models\Jigyosyo;
use App\Models\Bumon;
use App\Models\Zaiko;
use App\Models\Kkamoku;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'id';
    }

    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $id = $credentials[$this->username()];
        $user = User::where('id', $id)->first();
        if(isset($user)){
            $credentials[$this->username()] = $user->id;
        }
        $user = User::where('email', $id)->orderBy('enable_at', 'desc')->first();
        if(isset($user)){
            $credentials[$this->username()] = $user->id;
        }
        $user = User::where('email', $id . config('app.user_id_suffix'))->orderBy('enable_at', 'desc')->first();
        if(isset($user)){
            $credentials[$this->username()] = $user->id;
        }
        $credentials['kengen06'] = true;
        return $credentials;
    }

    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        System::add_login_cnt();
        session([
            'jigyosyos' => Jigyosyo::all2()->all(),
            'bumons' => Bumon::where('enable1', true)->orderBy('id')->get()->all(),
            'tanas' => Zaiko::get_tanas(),
            'kkamokus' => Kkamoku::orderBy('id')->get()->all(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
