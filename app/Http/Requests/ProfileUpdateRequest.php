<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $fullName = $this->input('full_name');
        $name = $this->input('name');
        $currentUser = $this->user();

        $this->merge([
            'full_name' => $fullName ?: $name,
            'name' => $name ?: $fullName,
            'email' => $currentUser?->email ?? $this->input('email'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUser = $this->user();
        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
        ];

        if (
            $currentUser !== null
            && strcasecmp((string) $this->input('email'), (string) $currentUser->email) !== 0
        ) {
            $emailRules[] = Rule::unique(User::class)->ignore($currentUser);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'bio' => ['nullable', 'string', 'max:1000'],
            'region' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:3072'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
