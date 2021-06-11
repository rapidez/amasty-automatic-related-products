<?php

namespace Rapidez\AmastyAutomaticRelatedProducts;

use Illuminate\Support\ServiceProvider;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes\RelatedProductsScope;
use TorMorten\Eventy\Facades\Eventy;
use Rapidez\Core\Casts\CommaSeparatedToArray;

class AmastyAutomaticRelatedProductsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->addScopes()
            ->registerViews();
    }

    public function addScopes() : self
    {
        Eventy::addFilter('product.scopes', fn ($scopes) => array_merge($scopes ?: [], [RelatedProductsScope::class]));
        Eventy::addFilter('product.casts', fn($casts) => array_merge($casts ?: [], ['amasty_bundles' => 'object']));

        return $this;
    }

    public function registerViews() : self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'amastyrelatedproducts');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/rapidez/amastyrelatedproducts'),
        ], 'views');

        return $this;
    }
}
