<?php

namespace WCPaymentLink\Model;

use WCPaymentLink\Infrastructure\Model;
use WCPaymentLink\Repository\ProductRepository;
use WC_Coupon;
use WCPaymentLink\Exceptions\InvalidTokenException;

class LinkModel extends Model
{
	private ?int $id = null;
    /**
     * @var ProductModel[] $products
     */
    private array $products = [];
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private ProductRepository $productRepository;

	public function __construct(
        private string $name = '',
        private string $token = '',
        private ?\DateTime $expireAt = null,
        private string $coupon = ''
    )
	{
        $this->productRepository = new ProductRepository();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $token);
        if ($isUuid) {
            $this->token = $token;
        } else {
            throw new InvalidTokenException();
        }
    }

    public function getExpireAt(): ?\DateTime
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTime $expireAt): void
    {
        if (!$expireAt) {
            $expireAt = new \DateTime();
        }
        $this->expireAt = $expireAt;
    }

    private function setProducts(bool $force = false): void
    {
        if (empty($this->products) || $force) {
            $this->products = $this->productRepository->getLinkProducts($this->id ?? 0);
        }
    }

    public function getProducts(): array
    {
        $this->setProducts();
        $products = [];

        foreach($this->products as $product) {
            $products[] = [
                'product' => $product->getProductId(),
                'quantity' => $product->getQuantity()
            ];
        }

        return $products;
    }

    public function addProduct(int $productId, int $quantity): void
    {
        $productsModel = new ProductModel($productId, $quantity);
        $this->products[] = $productsModel;
    }

    public function saveProducts(array $products)
    {
        $removed = $this->productRepository->removeLinkProducts($this->id);

        if ($removed) {
            $this->products = [];

            foreach($products as $product) {
                $product = is_array($product) ? (object) $product : $product;
                $object = new ProductModel(
                    $product->product,
                    $product->quantity,
                    $this->id
                );

                $this->productRepository->save($object);

                $this->addProduct(
                    $product->product,
                    $product->quantity
                );
            }
        }
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): void
    {
        $this->coupon = $coupon;
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCartTotal(): float
    {
        $total = 0.0;

        if (!empty($this->products)) {
            foreach($this->products as $product) {
                $wc_product = wc_get_product($product->getProductId());
                $total += ($wc_product->get_price() * $product->getQuantity());
            }
        }

        if ($this->coupon) {
            $coupon = new WC_Coupon($this->coupon);
            if ($coupon) {
                $type = $coupon->get_discount_type();
                $amount = $coupon->get_amount();

                if ($type === 'fixed_cart') {
                    $total -= (float) $amount;
                } else {
                    $total -= ($total / 100) * (int) $amount;
                }
            }
        }

        return $total > 0 ? $total : 0;
    }

    public function getLinkUrl(): string
    {
        return site_url( "/pay/{$this->token}", 'https' );
    }

    public function getData(): array
    {
        return [
            'link_id'      => $this->id,
            'name'         => $this->name,
            'token'        => $this->token,
            'coupon'       => $this->coupon,
            'cart_total'   => $this->getCartTotal(),
            'products'     => $this->getProducts(),
            'expire_at'    => $this->getExpireAt()->format('Y-m-d'),
            'expire_hour'  => $this->getExpireAt()->format('H'),
            'link_url'     => $this->getLinkUrl()
        ];
    }

}


