<?php

namespace App\Modules\Cart\Entity;

use App\Exceptions\BusinessException;
use App\Modules\HasDates;
use App\Modules\Product\Product;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity]
class CartProduct
{
    use HasDates;

    const TABLE = 'cart_products';

    #[Column('primary')]
    protected int $id;

    #[Column(type: 'integer', nullable: true)]
    private ?int $orderPrice = null;

    #[BelongsTo(target: Cart::class)]
    private readonly Cart $cart;

    public function __construct(
        #[BelongsTo(target: Product::class)]
        private Product $product,
        #[Column('integer')]
        private int $quantity = 0
    )
    {
        $this->setQuantity($this->quantity);
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new BusinessException('Указано некорректное количество');
        }
        $this->quantity = $quantity;
    }

    public function addQuantity(int $quantity): void
    {
        $this->setQuantity($quantity + $this->quantity);
    }

    public function getProductId(): int
    {
        return $this->product->getId();
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function calculateCost(): int
    {
        return $this->quantity * $this->product->getPrice();
    }

    public function getOrderPriceOrProductPrice(): int
    {
        return $this->hasOrderPrice() ? $this->orderPrice : $this->product->getPrice();
    }

    public function calculateCostByOrderPrice(): int
    {
        return $this->quantity * $this->getOrderPriceOrProductPrice();
    }

    public function hasOrderPrice(): bool
    {
        return !is_null($this->orderPrice);
    }

    public function initOrderPrice(): void
    {
        $this->orderPrice = $this->hasOrderPrice() ? $this->orderPrice : $this->product->getPrice();
    }

    public function getOrderPrice(): ?int
    {
        return $this->orderPrice;
    }
}
