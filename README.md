# Amasty Automatic Related Products

This plugin requires the [Amasty Automatic Related Products](https://amasty.com/automatic-related-products-for-magento-2.html) module installed and configured within your Magento2 installation.

## Installation

```
composer require rapidez/amasty-most-viewed
```

If you haven't published the Rapidez views yet, you can publish them with:
```
php artisan vendor:publish --provider="Rapidez\Core\RapidezServiceProvider" --tag=views
```

You can publish the views of this module with:
```
php artisan vendor:publish --provider="Rapidez\AmastyMostViewed\AmastyAutomaticRelatedProductsServiceProvider" --tag=views
```

Add this Vue component to your `app.js`.
```
Vue.component('bundles', require('Vendor/rapidez/amasty-automatic-related-products/resources/js/components/amastybundles.vue').default)
```

## Usage

This module has 2 blade components for each related product rules and product bundles. You can include them like this in `resources/views/vendor/rapidez/product/overview.blade.php`
```
<x-amastyrelatedproducts::relatedproducts :related_ids="$product->amasty_related_ids" />
```

```
<x-amastyrelatedproducts::productbundles :product="$product" />
```

