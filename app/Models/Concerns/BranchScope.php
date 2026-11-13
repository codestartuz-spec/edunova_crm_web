<?php

namespace App\Models\Concerns;

use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $branchId = BranchContext::id();

        if ($branchId === null) {
            // v20: "barcha filiallar" rejimida ham cheklangan direktor faqat o'z filiallarini ko'radi
            $allowed = BranchContext::restrictedIds();

            if ($allowed !== null) {
                $allowed === [] ? $builder->whereRaw('1 = 0') : $builder->whereIn($model->qualifyColumn('branch_id'), $allowed);
            }

            return;
        }

        // 0 - filialga biriktirilmagan foydalanuvchi: hech narsa ko'rmaydi
        if ($branchId === 0) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where($model->qualifyColumn('branch_id'), $branchId);
    }
}
