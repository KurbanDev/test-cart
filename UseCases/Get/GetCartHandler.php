<?php

namespace App\Modules\Cart\UseCases\Get;

use App\Modules\Cart\CartService;
use App\Modules\Cart\Entity\ActiveCartManager;
use App\Modules\Cart\Entity\Cart;


readonly class GetCartHandler
{
    public function __construct(private ActiveCartManager $cartManager) {}

    public function handle(): Cart
    {
        return $this->cartManager->findActiveOrInit();
    }
}

