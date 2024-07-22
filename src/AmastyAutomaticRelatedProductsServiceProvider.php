<?php

namespace Rapidez\AmastyAutomaticRelatedProducts;

use Illuminate\Support\ServiceProvider;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes\RelatedProductsScope;
use Rapidez\Core\Casts\CommaSeparatedToIntegerArray;
use TorMorten\Eventy\Facades\Eventy;

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
        Eventy::addFilter('productpage.scopes', fn ($scopes) => array_merge($scopes ?: [], [RelatedProductsScope::class]));
        Eventy::addFilter('product.casts', fn ($casts) => array_merge($casts ?: [], [
            'amasty_related_ids' => CommaSeparatedToIntegerArray::class,
        ]));

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
