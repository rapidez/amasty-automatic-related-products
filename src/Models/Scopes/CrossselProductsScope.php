<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CrossselProductsScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->selectRaw('GROUP_CONCAT(DISTINCT related.entity_id) as amasty_crosssell_ids')
            ->leftJoin('quote_item as item', function ($join) {
                $join->on('item.quote_id', '=', 'quote.entity_id')->whereNull('item.parent_item_id');
            })
             ->leftJoin('amasty_mostviewed_product_index as mainrule', function ($join) {
                 $join->on('mainrule.entity_id', '=', 'item.product_id')
                    ->where('mainrule.relation', 'where_show')
                    ->where('mainrule.position', 'cart_into_crosssel')
                    ->where('mainrule.store_id', config('rapidez.store'));
             })
            ->leftJoin('amasty_mostviewed_product_index as related', function ($join) {
                $join->on('related.rule_id', '=', 'mainrule.rule_id')
                    ->where('related.relation', 'what_show')
                    ->where('related.position', 'cart_into_crosssel')
                    ->where('related.store_id', config('rapidez.store'));
            });
    }
}
