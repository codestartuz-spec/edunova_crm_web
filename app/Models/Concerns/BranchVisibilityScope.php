<?php

namespace App\Models\Concerns;

use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * v20: filiallar ro'yxatining o'zi ham cheklanadi - ba'zi filiallarga ruxsat berilgan direktor boshqa filiallarni
 * ko'rmaydi (ro'yxat, tanlagich, `branches/{branch}` sahifasi - hammasi shu orqali).
 */
class BranchVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $ids = BranchContext::restrictedIds();

        if ($ids === null) {
            return;
        }

        $ids === [] ? $builder->whereRaw('1 = 0') : $builder->whereIn($model->qualifyColumn('id'), $ids);
    }
}
