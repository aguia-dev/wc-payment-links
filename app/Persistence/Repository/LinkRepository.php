<?php

namespace WCPaymentLink\Repository;

use WCPaymentLink\Model\LinkModel;
use WCPaymentLink\Infrastructure\Model;
use WCPaymentLink\Infrastructure\Repository;

class LinkRepository extends Repository
{
	public function __construct()
	{
		parent::__construct('wc_payment_links');
	}

	protected function fill(\stdClass $row): LinkModel
	{
		$entity = new LinkModel(
            $row->name,
            $row->token,
            new \DateTime($row->expire_at),
            $row->coupon ?? ''
        );

		$entity->setId($row->id);
		$entity->setUpdatedAt(new \DateTime($row->updated_at));
		$entity->setCreatedAt(new \DateTime($row->created_at));

		return $entity;
	}

	public function remove(Model|LinkModel $entity): bool
	{
		if (!$entity->getId()) {
			return false;
		}

		$query = $this->db->delete(
			$this->table,
			['id' => $entity->getId()]
		);
		
		if (is_bool($query)) {
			return false;
		}

		return true;
	}

	protected function getEntityData(Model|LinkModel $entity): array
	{
		return [
			'name'  => $entity->getName(),
            'token' => $entity->getToken(),
            'expire_at' => $entity->getExpireAt()?->format('Y-m-d H:i:s'),
            'products' => serialize($entity->getProducts()),
            'coupon' => $entity->getCoupon(),
		];
	}

	public function up(): void
	{
		$this->create([
			'id'             => ['INT AUTO_INCREMENT primary key NOT NULL' ],
            'name'           => ['VARCHAR(100) NOT NULL'],
            'token'          => ['VARCHAR(100) NOT NULL'],
            'expire_at'      => ['DATETIME NOT NULL'],
            'products'       => ['TEXT'],
            'coupon'         => ['VARCHAR(100)'],
			'created_at'     => ['DATETIME DEFAULT CURRENT_TIMESTAMP' ],
			'updated_at'     => ['DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' ]
		]);
	}

}
