<?php

namespace App\Modules\Cart\Entity;

use App\Modules\Cart\CartService;

readonly class ActiveCartManager
{
    private Cart $cart;

    public function __construct(private CartService $cartService) {}

    public function findActiveOrInit(): Cart
    {
        if (!isset($this->cart)) {
            $this->cart = $this->cartService->findActiveOrInitEmptyCart();
        }

        return $this->cart;
    }
}
