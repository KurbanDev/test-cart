<?php

namespace App\Modules\Cart\UseCases\AddToCart;

readonly  class AddToCartCmd
{

    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}
}
