<?php

namespace WCPaymentLink\Persistence\Repositories;

use WCPaymentLink\Persistence\Repositories\Abstractions\AbstractRepository;
use WCPaymentLink\Persistence\Models\ProductModel;
use WCPaymentLink\Persistence\Models\Abstractions\AbstractModel;

class ProductRepository extends AbstractRepository
{
	public function __construct()
	{
		parent::__construct('wc_payment_links_products');
	}

	/**
	 * @return ProductModel[]
	 */
	public function getLinkProducts(int $linkId): array
	{
		return $this->findBy('link_id', $linkId) ?? [];
	}

	public function removeLinkProducts(int $linkId): bool
	{
		$query = $this->db->delete(
			$this->table,
			['link_id' => $linkId]
		);

		if (is_bool($query)) {
			return false;
		}


		return true;
	}

	protected function fill(\stdClass $row): ProductModel
	{
		$entity = new ProductModel(
            $row->product_id,
            $row->quantity,
        );

		$entity->setLinkId($row->link_id);
		$entity->setUpdatedAt(new \DateTime($row->updated_at));
		$entity->setCreatedAt(new \DateTime($row->created_at));

		return $entity;
	}

	protected function getEntityData(AbstractModel|ProductModel $entity): array
	{
		return [
			'product_id' => $entity->getProductId(),
            'quantity'   => $entity->getQuantity(),
            'link_id'    => $entity->getLinkId()
		];
	}

	public function up(): void
	{
		$this->create([
			'id'         => ['INT AUTO_INCREMENT primary key NOT NULL' ],
            'product_id' => ['INT NOT NULL'],
            'quantity'   => ['INT NOT NULL'],
            'link_id'    => ['INT NOT NULL'],
			'created_at' => ['DATETIME DEFAULT CURRENT_TIMESTAMP' ],
			'updated_at' => ['DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ],
			''           => ["INDEX (product_id)"],
			''           => ["FOREIGN KEY (link_id) REFERENCES {$this->prefix}wc_payment_links(ID) ON DELETE CASCADE"],
		]);
	}

}
