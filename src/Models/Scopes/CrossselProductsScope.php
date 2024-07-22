<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CrossselProductsScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->selectRaw('GROUP_CONCAT(DISTINCT related.entity_id) as amasty_related_ids')
            ->leftJoin('amasty_mostviewed_product_index as related', function ($join) {
                $join->on('related.rule_id', '=', 'amasty_mostviewed_product_index.rule_id')
                    ->where('related.relation', 'what_show')
                    ->where('related.position', 'cart_into_crosssel')
                    ->where('related.store_id', config('rapidez.store'));
            })
            ->where('amasty_mostviewed_product_index.relation', 'where_show')
            ->where('amasty_mostviewed_product_index.position', 'cart_into_crosssel')
            ->where('amasty_mostviewed_product_index.store_id', config('rapidez.store'));
    }
}
