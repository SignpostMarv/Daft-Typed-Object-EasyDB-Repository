<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use ParagonIE\EasyDB\EasyDB;
use PDO;

/**
* @template T1 as DaftTypedObjectForRepository
* @template T2 as array<string, scalar>
*
* @template-extends AbstractDaftTypedObjectRepository<T1, T2>
*/
abstract class AbstractDaftTypedObjectEasyDBRepository extends AbstractDaftTypedObjectRepository
{
	protected EasyDB $connection;

	protected string $table;

	/**
	* @param array{
	*	type:class-string<T1>,
	*	EasyDB::class:EasyDB,
	*	table:string
	* } $options
	*/
	public function __construct(
		array $options
	) {
		parent::__construct([
			'type' => $options['type'],
		]);

		/**
		* @var EasyDB
		*/
		$this->connection = $options[EasyDB::class];
		$this->table = $options['table'];
	}

	/**
	* @param T1 $object
	*/
	public function UpdateTypedObject(
		DaftTypedObjectForRepository $object
	) : void {
		$id = $object->ObtainId();

		/**
		* @var array<int, string>
		*/
		$properties = $object::TYPED_PROPERTIES;

		if ('sqlite' === $this->connection->getDriver()) {
			$sth = $this->connection->prepare(
				'REPLACE INTO ' .
				$this->connection->escapeIdentifier($this->table) .
				' (' .
				implode(', ', array_map(
					[$this->connection, 'escapeIdentifier'],
					$properties
				)) .
				') VALUES (' .
				implode(
					', ',
					array_fill(0, count($properties), '?')
				) .
				')'
			);

			$sth->execute(array_values($object->__toArray()));
		} else {
			$this->connection->insertOnDuplicateKeyUpdate(
				$this->table,
				$object->__toArray(),
				array_filter(
					$properties,
					function (string $maybe) use ($id) : bool {
						return ! array_key_exists($maybe, $id);
					}
				)
			);
		}

		parent::UpdateTypedObject($object);
	}

	/**
	* @param T2 $id
	*/
	public function RemoveTypedObject(array $id) : void
	{
		$this->connection->delete($this->table, $id);

		$hash = static::DaftTypedObjectHash($id);

		unset($this->memory[$hash]);
	}

	/**
	* @param T2 $id
	*
	* @return T1|null
	*/
	public function MaybeRecallTypedObject(
		array $id
	) : ? DaftTypedObjectForRepository {
		$maybe = parent::MaybeRecallTypedObject($id);

		if (is_null($maybe)) {
			$type = $this->type;
			/**
			* @var array<int, string>
			*/
			$properties = $type::TYPED_PROPERTIES;

			/**
			* @var array<int, string>
			*/
			$id_fields = array_keys($id);

			$sth = $this->connection->prepare(
				'SELECT ' .
				implode(', ', array_map(
					[$this->connection, 'escapeIdentifier'],
					$properties
				)) .
				' FROM ' .
				$this->connection->escapeIdentifier($this->table) .
				' WHERE ' .
				implode(
					' AND ',
					array_map(
						function (string $field) : string {
							return
								$this->connection->escapeIdentifier($field) .
								' = ?';
						},
						$id_fields
					)
				) .
				' LIMIT 1'
			);

			$sth->execute(array_values($id));

			/**
			* @var array<string, scalar|null>|null
			*/
			$row = $sth->fetch(PDO::FETCH_ASSOC);

			if (is_array($row)) {
				/**
				* @var T1
				*/
				$maybe = $type::__fromArray($row);

				parent::UpdateTypedObject($maybe);
			}
		}

		return $maybe;
	}
}
