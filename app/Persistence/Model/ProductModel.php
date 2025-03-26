<?php

namespace WCPaymentLink\Model;

use WCPaymentLink\Infrastructure\Model;

class ProductModel extends Model
{
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        private int $productId,
        private int $quantity,
        private ?int $linkId = null 
    ){}


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

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getLinkId(): int
    {
        return $this->linkId;
    }

    public function setLinkId(int $linkId): void
    {
        $this->linkId = $linkId;
    }
}
