<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,pending,inactive,suspended'],
            'role' => ['required', 'in:superadmin,admin,manager,staff'],
            'reset_password' => ['nullable', 'boolean'],
        ];
    }
}
