<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('users.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'role' => ['required', 'in:'.implode(',', array_keys(User::availableRoles()))],
            'custom_permissions' => ['nullable', 'array'],
            'custom_permissions.*' => [
                'string',
                'in:'.implode(',', array_keys(User::getAllPermissions())),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $allowedPermissions = User::getAllowedCustomPermissionsForRole((string) $this->input('role'));

                    if ($allowedPermissions === []) {
                        $fail('Custom permissions are only available for Post Editor and Page Editor roles.');

                        return;
                    }

                    if (!in_array($value, $allowedPermissions, true)) {
                        $fail('One or more selected permissions are not allowed for the chosen role.');
                    }
                },
            ],
        ];

        // Password is required for creation, optional for updates
        if ($this->isMethod('POST')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
            ];
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
