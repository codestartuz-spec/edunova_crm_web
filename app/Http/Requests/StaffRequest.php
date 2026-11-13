<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Policies\UserPolicy;
use App\Rules\UniquePhonePerRole;
use App\Rules\UzPhone;
use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $target = $this->route('user');
        $creating = $target === null;

        $branchId = $target?->branch_id
            ?? ($this->user()->isGlobal()
                ? (int) $this->input('branch_id')
                : $this->user()->branch_id);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z0-9._@+-]+$/', Rule::unique('users', 'username')->ignore($target?->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target?->id)],
            'phone' => ['required', 'string', new UzPhone, new UniquePhonePerRole(
                $branchId,
                // v13: lavozim o'zgarayotgan bo'lsa, telefon yangi lavozimda takrorlanmasligi tekshiriladi
                $creating
                    ? (Role::tryFrom((string) $this->input('role')) ?? Role::Manager)
                    : (Role::tryFrom((string) $this->input('role')) ?? $target->role),
                $target?->id,
            )],
            'birthday' => ['nullable', 'date', 'before:today'],
            'hired_at' => ['nullable', 'date', 'before_or_equal:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'password' => [$creating ? 'required' : 'nullable', 'confirmed', Password::min(8)],
        ];

        // v13: operatorga qo'shimcha filiallar - faqat sAdmin belgilaydi
        if ($this->user()->isGlobal()) {
            $rules['extra_branches'] = ['nullable', 'array'];
            $rules['extra_branches.*'] = ['integer', \App\Support\BranchContext::branchExists()];
            $rules['extra_branches_form'] = ['nullable'];
        } else {
            $rules['extra_branches'] = ['prohibited'];
        }

        if (! $creating) {
            // v13: lavozimni o'zgartirish (ixtiyoriy). Hozirgi lavozim har doim ruxsat etiladi (o'zgarmaydi).
            $changeable = array_map(fn (Role $r) => $r->value, UserPolicy::assignableRoles($this->user(), $target));
            $rules['role'] = ['nullable', Rule::in([...$changeable, $target->role->value])];
        }

        if ($creating) {
            $allowedRoles = collect(UserPolicy::manageableRoles($this->user()))
                ->filter(fn (Role $r) => in_array($r, [Role::Admin, Role::Manager, Role::Teacher, Role::Operator], true))
                ->map->value->all();

            $rules['role'] = ['required', Rule::in($allowedRoles)];

            $rules['branch_id'] = $this->user()->isGlobal()
                ? ['required', \App\Support\BranchContext::branchExists()]
                : ['prohibited'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'F.I.O',
            'username' => 'Login',
            'email' => 'Email',
            'phone' => 'Telefon',
            'birthday' => "Tug'ilgan sana",
            'address' => 'Manzil',
            'status' => 'Holat',
            'password' => 'Parol',
            'role' => 'Lavozim',
            'branch_id' => 'Filial',
            'extra_branches' => "Qo'shimcha filiallar",
        ];
    }
}
