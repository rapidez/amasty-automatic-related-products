<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RelatedProductsScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->selectRaw('GROUP_CONCAT(related.entity_id) as amasty_related_ids')
            ->leftJoin('amasty_mostviewed_product_index as mainrule', function ($join) use ($builder) {
                $join->on('mainrule.entity_id', '=', $builder->getQuery()->from.'.entity_id')
                    ->where('mainrule.relation', 'where_show')
                    ->whereIn('mainrule.position', ['product_into_related', 'product_into_upsell'])
                    ->where('mainrule.store_id', config('rapidez.store'));
            })
            ->leftJoin('amasty_mostviewed_product_index as related', function ($join) {
                $join->on('related.rule_id', '=', 'mainrule.rule_id')
                    ->where('related.relation', 'what_show')
                    ->whereIn('related.position', ['product_into_related', 'product_into_upsell'])
                    ->where('related.store_id', config('rapidez.store'));
            });
    }
}
