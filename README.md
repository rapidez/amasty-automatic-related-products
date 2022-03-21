# Amasty Automatic Related Products

This plugin requires the [Amasty Automatic Related Products](https://amasty.com/automatic-related-products-for-magento-2.html) and the `amasty/customers-also-viewed-graphql` module installed and configured within your Magento 2 installation.

## Installation

```
composer require rapidez/amasty-automatic-related-products
```

If you haven't published the Rapidez views yet, you can publish them with:
```
php artisan vendor:publish --provider="Rapidez\Core\RapidezServiceProvider" --tag=views
```

You can publish the views of this module with:
```
php artisan vendor:publish --provider="Rapidez\AmastyAutomaticRelatedProducts\AmastyAutomaticRelatedProductsServiceProvider" --tag=views
```

Add this Vue component to your `app.js`.
```
Vue.component('amastybundles', require('Vendor/rapidez/amasty-automatic-related-products/resources/js/components/amastybundles.vue').default)
```

## Usage

This module has 2 Blade components for each related product rules and product bundles. You can include them in `resources/views/vendor/rapidez/product/overview.blade.php`

### Related products
```
<x-amastyrelatedproducts::relatedproducts :related_ids="$product->amasty_related_ids" />
```

### Bundles
```
<x-amastyrelatedproducts::productbundles :product="$product->id" />
```

## TODO

- Notifications
- Bundles with configurable products
- Use the `apply_condition` option when it's available through GraphQL

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
