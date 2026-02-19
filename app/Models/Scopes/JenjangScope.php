<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class JenjangScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $activeJenjangId = Session::get('active_jenjang_id');

        if ($activeJenjangId) {
            // Check if this jenjang is 'UMUM' (General/All)
            $jenjang = \App\Models\JenjangPendidikan::find($activeJenjangId);
            if ($jenjang && (strtoupper($jenjang->nama) === 'UMUM' || strtoupper($jenjang->type) === 'UMUM')) {
                return;
            }

            // Check if model has a specific scope defined.
            // Note: Elquent allows defining scopeByJenjang($query, $value).
            // We can call it here on the builder instance.
            if (method_exists($model, 'scopeByJenjang')) {
                $model->scopeByJenjang($builder, $activeJenjangId);
            } else {
                // Default handling: check if the column exists in the table? 
                // Or assume standard column name.
                // To avoid errors, checks could be added, but for now we assume standard Column.
                $builder->where($model->getTable() . '.id_jenjang_pendidikan', $activeJenjangId);
            }
        }
    }
}
