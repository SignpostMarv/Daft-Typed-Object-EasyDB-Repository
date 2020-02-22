<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository;
use ParagonIE\EasyDB\EasyDB;
use Throwable;

/**
 * @template T1 as DaftTypedObjectForRepository
 * @template T2 as array<string, scalar>
 * @template T3 as array<string, scalar|null>
 * @template T4 as array{
 *	type:class-string<DaftTypedObjectForRepository>,
 *	ParagonIE\EasyDB\EasyDB:EasyDB,
 *	table:string
 * }
 *
 * @template-extends ObjectEasyDBRepository<T1, T2, T3, T4>
 *
 * @template-implements DaftTypedObjectRepository<T1, T2, T4>
 */
abstract class AbstractDaftTypedObjectEasyDBRepository extends ObjectEasyDBRepository implements DaftTypedObjectRepository
{
	/** @var class-string<T1> */
	protected string $type;

	/**
	 * @param T4 $options
	 */
	public function __construct(
		array $options
	) {
		parent::__construct($options);

		/** @var class-string<T1> */
		$this->type = $options['type'];
	}

	/**
	 * @param T1 $object
	 */
	public function UpdateTypedObject(
		DaftTypedObjectForRepository $object
	) : void {
		$this->UpdateObject($object);
	}

	/**
	 * @param T2 $id
	 */
	public function ForgetTypedObject(array $id) : void
	{
		$this->ForgetObject($id);
	}

	/**
	 * @param T2 $id
	 */
	public function RemoveTypedObject(array $id) : void
	{
		$this->RemoveObject($id);
		$this->ForgetTypedObject($id);
		$this->PurgeObjectDataCache($id);
	}

	/**
	 * @param T2 $id
	 *
	 * @return T1
	 */
	public function RecallTypedObject(
		array $id,
		Throwable $not_found = null
	) : DaftTypedObjectForRepository {
		return $this->RecallObject($id, $not_found);
	}

	/**
	 * @param T2 $id
	 *
	 * @return T1|null
	 */
	public function MaybeRecallTypedObject(
		array $id
	) : ? DaftTypedObjectForRepository {
		/** @var T1|null */
		return $this->MaybeRecallObject($id);
	}

	/**
	 * @param T1 $object
	 *
	 * @return T2
	 */
	public function ObtainIdFromObject(object $object) : array
	{
		return $object->ObtainId();
	}
}
