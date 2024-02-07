<?php

namespace App\Modules\Cart;

use App\Infrastructure\Auth\CustomerAuthenticator;
use App\Infrastructure\CustomerToken\UnAuthCustomerTokenStorage;
use App\Modules\Cart\Entity\Cart;
use App\Modules\Cart\Entity\CartRepository;

readonly class CartService
{
    public function __construct(
        private UnAuthCustomerTokenStorage $tokenStorage,
        private CustomerAuthenticator $customerAuthenticator,
        private CartRepository $cartRepository,
    ) {}

    public function makeIdentityParam(): CartPriorityParam
    {
        return new CartPriorityParam(
            customerId: $this->customerAuthenticator->id(),
            token     : $this->tokenStorage->get()
        );
    }

    public function findActiveCart(): ?Cart
    {
        return $this->cartRepository->loadProductsWithMedia()->findActiveByPriorityParam($this->makeIdentityParam());
    }

    public function findActiveOrInitEmptyCart(): Cart
    {
        return $this->findActiveCart() ?? $this->initEmptyCart();
    }

    public function initEmptyCart(): Cart
    {
        return new Cart(
            token   : $this->tokenStorage->get(),
            customer: $this->customerAuthenticator->customer()
        );
    }

    public function removeProductById(Cart $cart, int $productId): void
    {
        $cart->removeProductByProductId($productId);
        $this->cartRepository->save($cart);
    }


}
