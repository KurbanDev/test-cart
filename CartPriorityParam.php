<?php

namespace App\Modules\Cart;

readonly class CartPriorityParam
{
    public function __construct(
        public ?int $customerId = null,
        public ?string $token = null
    ) {}
}
