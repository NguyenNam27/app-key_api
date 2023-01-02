<?php

namespace App\Http\Requests\Auth;

use Dotenv\Exception\ValidationException;
use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
    public function throttleKey(){
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
    public function authenticateToken() : string
    {
        if (! $token = Auth::attempt($this->only('email','password'))){
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
               'email'=>__('auth.failed'),
            ]);

        }
        RateLimiter::clear($this->throttleKey());
        return $token;
    }

}
