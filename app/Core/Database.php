<?php

declare(strict_types=1);

namespace WCPaymentLink\Core;

use WCPaymentLink\Persistence\Repositories\LinkRepository;
use WCPaymentLink\Persistence\Repositories\ProductRepository;

final class Database
{
	public array $tables;

	public function __construct()
	{
		$this->tables = [
            LinkRepository::class,
            ProductRepository::class
        ];
	}

	public function initialize(): void
	{
		$this->tables();
	}

	public function uninstall(): void
	{
		foreach ($this->tables as $table) {
			if (class_exists($table)) {
				$t = new $table;
				$t->down();
			}
		}
	}

	private function tables() : void
	{
		foreach ($this->tables as $table) {
			if (class_exists($table)) {
				$t = new $table;
				$t->up();
			}
		}
	}
}
