<?php

namespace App\Modules\Cart\Entity;


use App\Modules\AggregateRoot;
use App\Modules\Customer\Customer;
use App\Modules\HasDates;
use App\Modules\HasEvents;
use App\Modules\Product\Product;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Illuminate\Support\Collection;

#[Entity(repository: CartRepository::class, table: self::TABLE)]
class Cart implements AggregateRoot
{
    use HasDates, HasEvents;

    const TABLE = 'carts';

    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'boolean', typecast: 'bool')]
    private bool $isActive;

    /**
     * @var Collection<CartProduct>
     */
    #[HasMany(target: CartProduct::class)]
    private Collection $products;

    public function __construct(
        #[Column(type: 'text', nullable: true)]
        private readonly ?string $token,
        #[BelongsTo(target: Customer::class, nullable: true)]
        private ?Customer $customer = null
    )
    {
        if (!$this->token && !$this->customer) {
            throw new \DomainException("Корзина должна иметь минимум 1 параметр");
        }

        $this->isActive = true;
        $this->products = new Collection();

    }

    public function addProduct(Product $product, int $quantity): void
    {
        /** @var CartProduct $existProduct */
        $existProduct = $this->products->firstWhere(fn(CartProduct $cp) => $cp->getProductId() === $product->getId());

        if ($existProduct && $quantity <= 0) {
            $this->removeProduct($product);
            return;
        }

        $existProduct
            ? $existProduct->setQuantity($quantity)
            : $this->products->add(new CartProduct($product, $quantity));
    }

    public function sync(Cart $oldCart): void
    {
        foreach ($oldCart->products as $p) {
            $existProduct = $this->products->firstWhere(fn(CartProduct $cp) => $cp->getProductId() === $p->getProductId());
            if ($existProduct) {
                $existProduct->setQuantity($p->getQuantity());
            } else {
                $this->addProduct($p->getProduct(), $p->getQuantity());
            }
        }
    }

    public function isEmpty(): bool
    {
        return $this->products->isEmpty();
    }


    /**
     * @return Collection<CartProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function calculateTotalCost()
    {
        $cost = 0;
        foreach ($this->products as $item) {
            $cost += $item->calculateCost();
        }
        return $cost;
    }

    public function totalQuantity(): int
    {
        $qty = 0;
        foreach ($this->products as $item) {
            $qty += $item->getQuantity();
        }
        return $qty;
    }

    public function removeProduct(Product $product): void
    {
        $this->removeProductByProductId($product->getId());
    }

    public function removeProductByProductId(int $productId): void
    {
        $this->products = $this->products->filter(fn(CartProduct $cp) => $cp->getProductId() !== $productId);
    }

    public function disable(): void
    {
        $this->isActive = false;
    }

    public function toOrder(): void
    {
        $this->disable();
        $this->initProductOrderPrices();
    }

    public function initProductOrderPrices(): void
    {
        foreach ($this->products as $p) {
            $p->initOrderPrice();
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer?->getId();
    }

    public function clean(): void
    {
        $this->products = collect();
    }
}
