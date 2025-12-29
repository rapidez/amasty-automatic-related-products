<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class Related extends Model
{
    protected $table = 'amasty_mostviewed_product_index';
    protected $primaryKey = 'index_id';

    protected static function booted(): void
    {
        static::addGlobalScope('store', function ($query) {
            $query->where('store_id', config('rapidez.store'));
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product'), 'entity_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }
}
