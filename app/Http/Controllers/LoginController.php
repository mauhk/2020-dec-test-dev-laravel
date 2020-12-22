<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return Auth::user();
        }
        Errors::make("10000");
    }
    /**
     * Handle an registration register.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:500',
            'email' => 'required|string|min:1|max:100',
            'password' => 'required|string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $customer = new User;
        $customer->id = (string) Str::orderedUuid();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = bcrypt($request->password);
        $customer->save();

        return $this->authenticate($request);
    }


}