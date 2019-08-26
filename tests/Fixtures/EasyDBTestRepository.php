<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use ParagonIE\EasyDB\EasyDB;
use PDO;
use SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository;
use SignpostMarv\DaftTypedObject\DaftTypedObjectForRepository;

/**
* @template T1 as MutableForRepository
* @template T2 as array<string, scalar>
*
* @template-extends AbstractDaftTypedObjectRepository<T1, T2>
*/
class EasyDBTestRepository extends AbstractDaftTypedObjectEasyDBRepository
{
	/**
	* @param array{type:class-string<T1>, EasyDB::class:EasyDB, table:string}
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

		$id = $this->connection->lastInsertId();

		/**
		* @var T1
		*/
		return $this->RecallTypedObject([
			'id' => $id,
		]);
	}
}
