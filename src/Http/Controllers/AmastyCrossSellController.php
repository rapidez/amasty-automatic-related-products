<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Http\Controllers;


use Illuminate\Http\Request;
use Rapidez\AmastyAutomaticRelatedProducts\Models\AmastyCrossSells;

class AmastyCrossSellController
{
    public function __invoke(Request $request): array|null
    {
        $crossSellIds = AmastyCrossSells::whereIn('amasty_mostviewed_product_index.entity_id', $request->ids)
            ->pluck('amasty_related_ids')
            ->first();

        return explode(',', $crossSellIds) ?? null;
    }
}
