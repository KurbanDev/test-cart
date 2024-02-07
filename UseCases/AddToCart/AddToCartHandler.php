<?php

namespace App\Modules\Cart\UseCases\AddToCart;

use App\Exceptions\BusinessException;
use App\Modules\Cart\Entity\Cart;
use App\Modules\Cart\Entity\ActiveCartManager;
use App\Modules\Cart\Entity\CartRepository;
use App\Modules\Product\ProductRepository;

readonly class AddToCartHandler
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepository $productRepository,
        private ActiveCartManager $cartManager
    ) {}

    public function handle(AddToCartCmd $cmd): Cart
    {
        $cart    = $this->cartManager->findActiveOrInit();
        $product = $this->productRepository->findById($cmd->productId);

        if (!$product) {
            throw new BusinessException('Товар не найден');
        }

        $cart->addProduct($product, $cmd->quantity);
        $this->cartRepository->save($cart);

        return $cart;
    }
}
