<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Model;

class Rule extends Model
{
    protected $table = 'amasty_mostviewed_group';
    protected $primaryKey = 'group_id';

    public function indexed(): HasMany
    {
        // TODO:
        // - Listen to `sorting` attribute
        // - Listen to `show_out_of_stock` attribute
        return $this->hasMany(Related::class, 'rule_id', 'group_id')
            ->limit($this->max_products);
    }

    public function related(): HasMany
    {
        return $this->indexed()
            ->where('relation', 'what_show')
            ->where('position', 'product_into_related');
    }

    public function upsells(): HasMany
    {
        return $this->indexed()
            ->where('relation', 'what_show')
            ->where('position', 'product_into_upsell');
    }

    public function combined(): HasMany
    {
        return $this->indexed()
            ->where('relation', 'what_show')
            ->where(fn($query) =>
                $query->where('position', 'product_into_upsell')
                    ->orWhere('position', 'product_into_related')
            );
    }

    public function crosssells(): HasMany
    {
        return $this->indexed()
            ->where('relation', 'what_show')
            ->where('position', 'product_into_crossel');
    }
}
