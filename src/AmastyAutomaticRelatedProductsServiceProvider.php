<?php

namespace Rapidez\AmastyAutomaticRelatedProducts;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Related;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Rule;
use Rapidez\Core\Models\Product;

class AmastyAutomaticRelatedProductsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->addScopes()
            ->registerViews()
            ->bootRoutes();
    }

    public function bootRoutes(): self
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        return $this;
    }

    public function addScopes(): self
    {
        Product::resolveRelationUsing('relationRules', function (Product $product) {
            return $product
                ->hasManyThrough(Rule::class, Related::class, 'entity_id', 'group_id', null, 'rule_id')
                ->where('relation', 'where_show')
                ->where('store_id', config('rapidez.store'));
        });

        Product::macro('amastyRelatedIds', function (): Collection {
            return $this
                ->relationRules
                ->flatMap(fn (Rule $rule) => $rule->combined->pluck('entity_id'));
        });

        return $this;
    }

    public function registerViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'amastyrelatedproducts');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/amastyrelatedproducts'),
        ], 'views');

        return $this;
    }
}
