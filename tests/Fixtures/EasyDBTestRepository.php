<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use DaftFramework\RelaxedObjectRepository\AppendableObjectRepository;
use ParagonIE\EasyDB\EasyDB;
use PDO;
use SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository;
use SignpostMarv\DaftTypedObject\AppendableTypedObjectRepository;
use SignpostMarv\DaftTypedObject\DaftTypedObjectForRepository;
use SignpostMarv\DaftTypedObject\PatchableObjectRepository;

/**
 * @template T1 as MutableForRepository
 * @template T2 as array{id:int}
 * @template T3 as array{id:int, name:string}
 * @template T4 as array{name:string}
 * @template T5 as array{
 *	type:class-string<MutableForRepository>,
 *	ParagonIE\EasyDB\EasyDB:EasyDB,
 *	table:string
 * }
 *
 * @template-extends AbstractDaftTypedObjectEasyDBRepository<T1, T2, T3, T5>
 *
 * @template-implements AppendableTypedObjectRepository<T1, T2, T3, T5>
 * @template-implements AppendableObjectRepository<T1, T2, T3, T5>
 * @template-implements PatchableObjectRepository<T1, T2, T4, T5>
 */
class EasyDBTestRepository extends AbstractDaftTypedObjectEasyDBRepository implements
		AppendableTypedObjectRepository,
		AppendableObjectRepository,
		PatchableObjectRepository
{
	/**
	 * @param T5 $options
	 */
	public function __construct(
		array $options
	) {
		parent::__construct(
			$options
		);

		$connection = $options[EasyDB::class];
		$table = $options['table'];

		$query =
			'CREATE TABLE IF NOT EXISTS ' .
			$connection->escapeIdentifier($table) .
			' ( ' .
			$connection->escapeIdentifier('id') .
			(
				'sqlite' === $connection->getDriver()
					? ' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, '
					: ' INTEGER NOT NULL AUTO_INCREMENT, '
			) .
			$connection->escapeIdentifier('name') .
			' VARCHAR(255) NOT NULL' .
			(
				'sqlite' === $connection->getDriver()
					? ''
					: (
						', PRIMARY KEY(' .
						$connection->escapeIdentifier('id') .
						')'
					)
			) .
			');';

		$connection->query($query);

		if ('sqlite' !== $connection->getDriver()) {
			$connection->query(
				'TRUNCATE TABLE ' .
				$connection->escapeIdentifier($table)
			);
		}
	}

	/**
	 * @param T1 $object
	 *
	 * @return T1
	 */
	public function AppendTypedObject(
		DaftTypedObjectForRepository $object
	) : DaftTypedObjectForRepository {
		$this->connection->insert($this->table, [
			'name' => $object->name,
		]);

		/**
		 * @var T2
		 */
		$id = ['id' => (int) $this->connection->lastInsertId()];

		/**
		 * @var T1
		 */
		return $this->RecallTypedObject($id);
	}

	/**
	 * @param T1 $object
	 *
	 * @return T1
	 */
	public function AppendObject(object $object) : object
	{
		return $this->AppendTypedObject($object);
	}

	/**
	 * @param T3 $data
	 *
	 * @return T1
	 */
	public function AppendObjectFromArray(array $data) : object
	{
		/** @var T1 */
		$object = $this->ConvertSimpleArrayToObject($data);

		return $this->AppendObject($object);
	}

	/**
	 * @param T3 $data
	 *
	 * @return T1
	 */
	public function AppendTypedObjectFromArray(
		array $data
	) : DaftTypedObjectForRepository {
		return $this->AppendObjectFromArray($data);
	}

	public function PatchTypedObjectData(array $id, array $data) : void
	{
		$this->PatchObjectData($id, $data);
	}

	/**
	 * @param T3 $array
	 */
	public function ConvertSimpleArrayToObject(array $array) : object
	{
		/** @var T1 */
		return MutableForRepository::__fromArray($array);
	}

	/**
	 * @param T1 $object
	 *
	 * @return T3
	 */
	public function ConvertObjectToSimpleArray(object $object) : array
	{
		$data = $object->__toArray();

		$data['id'] = (int) $data['id'];

		/** @var T3 */
		return $data;
	}
}
