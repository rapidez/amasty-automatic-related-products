<?php

namespace Rapidez\AmastyAutomaticRelatedProducts\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Rapidez\AmastyAutomaticRelatedProducts\Models\Scopes\CrossselProductsScope;
use Rapidez\Core\Models\Model;

#[ScopedBy([CrossselProductsScope::class])]
class AmastyCrossSells extends Model
{
    protected $table = 'amasty_mostviewed_product_index';
}
