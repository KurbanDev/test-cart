<?php

namespace App\Modules\Cart\Events;

class CartCreated
{
    public function __construct(public string $token) {}
}
