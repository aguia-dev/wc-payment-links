<?php

declare(strict_types=1);

namespace WCPaymentLink\Persistence\Repositories\Abstractions;

use WCPaymentLink\Exceptions\DatabaseNotConnectedException;
use WCPaymentLink\Exceptions\EntityNotFoundException;
use WCPaymentLink\Exceptions\EntityNotRemovedException;
use WCPaymentLink\Exceptions\EntityNotSavedException;
use WCPaymentLink\Persistence\Models\Abstractions\AbstractModel;
use stdClass;

abstract class AbstractRepository
{
	protected string $prefix;
	protected string $table;
    protected object $db;

	public function __construct(string $table)
	{
		$this->setTable($table);
	}

	private function setTable(string $table): void
    {
        global $wpdb;

        if (!$wpdb) {
            throw new DatabaseNotConnectedException;
        }

        $this->db     = $wpdb;
        $this->prefix = $this->db->prefix;
        $this->table  = "{$this->prefix}{$table}";

        // $this->db->hide_errors();
    }

	protected function create($fields = []): void
    {
        if (empty($fields)) {
            return;
        }

        $rows = [];

        foreach ($fields as $key => $value) {
            $row = $key ? "`$key` " . implode(" ", $value) : implode(" ", $value);
            array_push($rows, $row);
        }

        $fields = implode(",", $rows);

        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->table} ( {$fields} );");
    }

	public function down(): void
	{
		$this->drop();
	}

	protected function drop(): void
    {
        $this->db->query("DROP TABLE {$this->table};");
    }

    protected function query($query)
    {
        return $this->db->get_results($query);
    }

	protected function update($fields, $where): int
	{
		$result = $this->db->update($this->table, $fields, $where);

        if ($result === false) {
            throw new EntityNotSavedException(get_called_class());
        }

        return $result;
	}

	protected function insert($fields): int
	{
		$result = $this->db->insert($this->table, $fields);

        if ($result !== false) {
            return $this->db->insert_id ?? $result;
        }

        throw new EntityNotSavedException(get_called_class());
	}

    public function save(AbstractModel $entity): int
    {
        if (method_exists($entity, 'getId') && $entity->getId()) {
            return $this->update(
                $this->getEntityData($entity),
                ['id' => $entity->getId()]
            );
        }

        return $this->insert($this->getEntityData($entity));
    }


    public function remove(array $where): int
    {
        $result = $this->db->delete($this->table, $where);

        if ($result) {
            return $result;
        }

        throw new EntityNotRemovedException(get_called_class());
    }

    public function removeById(int $id): int
    {
        return $this->remove(['id' => $id]);
    }

	public function findById(int $id, $fill = true): AbstractModel|stdClass
	{
		$result = $this->query("SELECT * FROM {$this->table} WHERE id = {$id};");
        $row = array_shift($result);

        if (!($row instanceof \stdClass)) {
            throw new EntityNotFoundException(get_called_class());
        }

        if ($fill) {
            return $this->fill($row);
        }

        return $row;
	}

	public function findBy(string $be, mixed $like, $fill = true): array
	{
		$query = "SELECT * FROM {$this->table} WHERE `{$be}` = '{$like}';";
		$rows = [];
        $result = $this->query($query);

        if (!$result) {
            throw new EntityNotFoundException(get_called_class());
        }

        if ($fill) {
            foreach($result as $item) {
                $rows[] = $this->fill($item);
            }
            return $rows;
        }

        return $result;
	}

	public function findAll(string $orderBy = '', int $limit = 10, int $page = 1, string $order = 'ASC', bool $fill = false, string $search = ''): array
	{
		$result = [];
		$query = "";

		if ($search) {
			$query .= " WHERE `name` like '%{$search}%'";
		}

		if ($orderBy) {
            $order = $order === 'DESC' ? 'DESC' : 'ASC';
            $query .= " ORDER BY `$orderBy` $order";
		}

		if ($limit > 0) {
			$result['pagination'] = $this->getPagination($query, $limit, $page);
			$offset = $result['pagination']['offset'];

			$query .= " LIMIT {$offset},{$limit}";
		}

		$query = "SELECT * FROM {$this->table}{$query};";

        if ($fill) {
            $rows = [];
            foreach ($this->query($query) as $item) {
                $rows[] = $this->fill($item);
            }

            $result['rows'] = $rows;
        } else {
            $result['rows'] = $this->query($query) ?? [];
        }

		return $result ;
	}

	public function getPagination(string $query, int $limit, int $page): array
	{
		$result = $this->query("SELECT COUNT(*) AS 'rows' FROM {$this->table}{$query};");
		if ($result) {
			$count = array_shift($result);

			$pages   = (int) ceil($count->rows / $limit);
			$current = min($page, $pages);
			$previous = ($current - 1) > 0 ? ($current - 1) : $current;
			$next    = min(($current + 1), $pages);

			return [
				'pages'    => $pages,
				'current'  => $current,
				'previous' => $previous,
				'next'     => $next,
				'offset'   => ($page - 1) * $limit,
				'rows'     => (int) $count->rows
			];
		}

		return [];
	}

	abstract protected function getEntityData(AbstractModel $entity): array;

	abstract protected function fill(\stdClass $row): AbstractModel;

	abstract public function up(): void;
}
