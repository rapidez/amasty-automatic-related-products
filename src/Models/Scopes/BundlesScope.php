<?php

namespace Rapidez\AmastyMostViewed\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;


class BundlesScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(bundle.pack_id, "null__"), JSON_OBJECT(
                    "block_title", bundle.block_title,
                    "product_ids", bundle.product_ids,
                    "discount_type", bundle.discount_type,
                    "discount_amount", bundle.discount_amount
                    )), "$.null__") AS amasty_bundles')
            ->leftJoin('amasty_mostviewed_pack_product as productBundle', 'productBundle.product_id', $model->getTable(). '.entity_id')
            ->join('amasty_mostviewed_pack as bundle', function($join) {
                $join->on('bundle.pack_id', 'productBundle.pack_id');
            });

    }
}

