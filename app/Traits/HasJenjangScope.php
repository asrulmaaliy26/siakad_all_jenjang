<?php

namespace App\Traits;

use App\Models\Scopes\JenjangScope;
use Illuminate\Database\Eloquent\Builder;

trait HasJenjangScope
{
    /**
     * The "booted" method of the model.
     */
    protected static function bootHasJenjangScope(): void
    {
        static::addGlobalScope(new JenjangScope);
    }

    /**
     * Local scope to allow manual overriding or checks if needed
     */
    public function scopeWithAllJenjang(Builder $query): Builder
    {
        return $query->withoutGlobalScope(JenjangScope::class);
    }
}
