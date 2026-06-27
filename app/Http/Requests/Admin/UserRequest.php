<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Modules\Identity\Authorization\Roles;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
                // Privilege-escalation guard: only a super-admin may grant super-admin.
                function (string $attribute, mixed $value, Closure $fail): void {
                    $actor = $this->user();

                    if ($value === Roles::SUPER_ADMIN && (! $actor instanceof User || ! $actor->hasRole(Roles::SUPER_ADMIN))) {
                        $fail('You are not permitted to assign the super-admin role.');
                    }
                },
            ],
            'password' => $this->route('user')
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
        ];
    }
}
