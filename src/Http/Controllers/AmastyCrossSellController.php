<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Http\Controllers;

use Illuminate\Http\Request;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Related;

class AmastyCrossSellController
{
    public function __invoke(Request $request): ?array
    {
        $rules = Related::with('rule')
            ->where('relation', 'where_show')
            ->where('store_id', config('rapidez.store'))
            ->whereIn('entity_id', $request->ids)
            ->get();

        if (!$rules) {
            return null;
        }

        return $rules
            ->flatMap(fn (Related $related) => $related->rule->crosssells->pluck('entity_id'))
            ->toArray();
    }
}
