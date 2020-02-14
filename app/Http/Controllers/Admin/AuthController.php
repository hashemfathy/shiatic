<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JsValidator;
use App\Models\User;

class AuthController extends Controller
{
    protected $validationRules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function index()
    {
        if (auth()->user()) {
            return redirect()->route('admin.web.index')->withErrors('Already Logged In');
        }
        $validator = JsValidator::make($this->validationRules);
        return view('backend.pages.auth.index', compact('validator'));
    }
    public function login(Request $request)
    {
        if (auth()->user()) {
            return redirect()->route('admin.web.index')->withErrors('Already Logged In');
        }
        $v = validator($request->all(), $this->validationRules);
        if ($v->fails()) {
            return back();
        }
        $user = auth()->attempt(['email' => $request->email, 'password' => $request->password]);
        if ($user) {
            return redirect()->route('admin.web.index');
        } else {
            return back();
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('auth.index');
    }
}
