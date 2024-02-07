<?php

namespace App\Modules\Cart\UseCases\Remove;

use App\Modules\Cart\CartService;
use App\Modules\Cart\Entity\ActiveCartManager;
use App\Modules\Cart\Entity\Cart;

readonly class RemoveProductHandler
{
    public function __construct(private CartService $cartService, private ActiveCartManager $cartManager) {}

    public function handle(int $productId): Cart
    {
        $cart = $this->cartManager->findActiveOrInit();
        $this->cartService->removeProductById($cart, $productId);
        return $cart;
    }
}
