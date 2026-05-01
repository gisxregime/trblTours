<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isPasswordChangeRequested =
            $this->filled('current_password')
            || $this->filled('new_password')
            || $this->filled('new_password_confirmation');

        $emailUniqueRule = Rule::unique(User::class, 'email');

        if ($user !== null) {
            $emailUniqueRule = $emailUniqueRule->ignore($user->id);
        }

        return [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', $emailUniqueRule],
            'current_password' => ['nullable', Rule::requiredIf($isPasswordChangeRequested), 'current_password'],
            'new_password' => [
                'nullable',
                Rule::requiredIf($isPasswordChangeRequested),
                'string',
                Password::min(8)->letters()->mixedCase()->numbers(),
                'confirmed',
            ],
            'new_password_confirmation' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.current_password' => 'The current password is incorrect.',
        ];
    }
}
