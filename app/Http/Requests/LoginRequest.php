<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required',
            'password' => 'required'
        ];
    }

    public function getCredentials()
    {
        $email = $this->get('email');

        if($this->isEmail($email))
        {
            return [
                'email' => $email,
                'password'=> $this->get('password')
            ];
        }

        return $this->only('email', 'password');
    }

    public function isEmail($value): bool
    {
        if(filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }

        return false;
    }
}
