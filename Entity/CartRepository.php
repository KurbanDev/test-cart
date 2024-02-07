<?php

namespace App\Modules\Cart\Entity;


use App\Infrastructure\Makeable;
use App\Modules\Cart\CartPriorityParam;
use WayOfDev\Cycle\Repository;


class CartRepository extends Repository
{
    use Makeable;

    public function save(Cart $cart): void
    {
        $this->persist($cart);
    }

    public function findActiveByPriorityParam(CartPriorityParam $param): ?Cart
    {
        if ($param->customerId) {
            return $this->findActiveAuthorized($param->customerId);
        }
        if ($param->token) {
            return $this->findActiveUnAuthorized($param->token);
        }
        return null;
    }

    public function findActiveAuthorized(int $customerId): ?Cart
    {
        return $this->findOne(['is_active' => true, 'customer_id' => $customerId]);
    }

    /**
     * Поиск корзины неавторизованного пользователя
     */
    public function findActiveUnAuthorized(string $token): ?Cart
    {
        return $this->findOne(['is_active' => true, 'token' => $token, 'customer_id' => null]);
    }

    public function remove(Cart $cart): void
    {
        $this->entityManager->delete($cart)->run();
    }

    public function findById(int $id): ?Cart
    {
        return $this->findByPK($id);
    }

    public function loadProductsWithMedia(): static
    {
        $repo = clone $this;
        $repo->select->load('products.product.media');
        return $repo;
    }

    public function whereActive(): static
    {
        $repo = clone $this;
        $repo->select->where(['is_active' => true]);
        return $repo;
    }

    public function newest(): static
    {
        $repo = clone $this;
        $repo->select->orderBy('id', 'DESC');
        return $repo;
    }
}
