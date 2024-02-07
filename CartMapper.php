<?php

namespace App\Modules\Cart;

use App\Infrastructure\Makeable;
use App\Modules\Cart\Entity\Cart;
use App\Modules\Cart\Entity\CartProduct;

class CartMapper
{
    use Makeable;

    public function map(Cart $cart): array
    {
        return [
            'items'          => $cart->getProducts()->map(fn(CartProduct $cartProduct) => [
                'cost'     => $cartProduct->calculateCost(),
                'quantity' => $cartProduct->getQuantity(),
                'product'  => [
                    'id'        => $cartProduct->getProduct()->getId(),
                    'name'      => $cartProduct->getProduct()->getName(),
                    'price'     => $cartProduct->getProduct()->getPrice(),
                    'cover_url' => $cartProduct->getProduct()->getCover()->getSmall(),
                ],
            ])->toArray(),
            'total_quantity' => $cart->totalQuantity(),
            'pay_sum'        => $cart->calculateTotalCost(),
        ];
    }
}
