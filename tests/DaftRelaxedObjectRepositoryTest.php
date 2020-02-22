<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use DaftFramework\RelaxedObjectRepository\ObjectRepositoryTest;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;

/**
 * @template S as array{id:int, name:string}
 * @template S2 as array{id:int|string, name:string}
 * @template S3 as array{name:string}
 * @template T1 as Fixtures\MutableForRepository
 * @template T2 as array<string, scalar|array|object|null>
 * @template T3 as Fixtures\EasyDBTestRepository
 * @template T4 as Fixtures\EasyDBTestRepository
 *
 * @template-extends ObjectRepositoryTest<S, S2, S3, T1, T2, T3, T4>
 */
class DaftRelaxedObjectRepositoryTest extends ObjectRepositoryTest
{
	/**
	 * @return list<
	 *	array{
	 *		0:class-string<T3>,
	 *		1:T2,
	 *		2:list<S>,
	 *		3:list<S2>
	 *	}
	 * >
	 */
	public function dataProviderAppendObject() : array
	{
		/**
		 * @var list<
		 *	array{
		 *		0:class-string<T3>,
		 *		1:T2,
		 *		2:list<S>,
		 *		3:list<S2>
		 *	}
		 * >
		 */
		return [
			[
				Fixtures\EasyDBTestRepository::class,
				[
					'type' => Fixtures\MutableForRepository::class,
					EasyDB::class => Factory::create('sqlite::memory:'),
					'table' => 'foo',
				],
				[
					[
						'id' => 0,
						'name' => 'foo',
					],
				],
				[
					[
						'id' => '1',
						'name' => 'foo',
					],
				],
			],
		];
	}

	/**
	 * @return list<
	 *	array{
	 *		0:class-string<T4>,
	 *		1:T2,
	 *		2:S,
	 *		3:S3,
	 *		4:S2
	 *	}
	 * >
	 */
	public function dataProviderPatchObject() : array
	{
		/**
		 * @var list<
		 *	array{
		 *		0:class-string<T4>,
		 *		1:T2,
		 *		2:S,
		 *		3:S3,
		 *		4:S2
		 *	}
		 * >
		 */
		return [
			[
				Fixtures\EasyDBTestRepository::class,
				[
					'type' => Fixtures\MutableForRepository::class,
					EasyDB::class => Factory::create('sqlite::memory:'),
					'table' => 'foo',
				],
				[
					'id' => 0,
					'name' => 'foo',
				],
				[
					'name' => 'bar',
				],
				[
					'id' => 1,
					'name' => 'bar',
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::ObtainIdFromObject()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::RecallTypedObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::AppendObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::AppendTypedObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertObjectToSimpleArray()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertSimpleArrayToObject()
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_append_object(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_append_object(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::ObtainIdFromObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertSimpleArrayToObject()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_default_failure(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_default_failure(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::ObtainIdFromObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertSimpleArrayToObject()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_custom_failure(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_custom_failure(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderPatchObject
	 *
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::ObtainIdFromObject()
	 * @covers \SignpostMarv\DaftTypedObject\AbstractDaftTypedObjectEasyDBRepository::RecallTypedObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::__construct()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::AppendObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::AppendTypedObject()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertObjectToSimpleArray()
	 * @covers \SignpostMarv\DaftTypedObject\Fixtures\EasyDBTestRepository::ConvertSimpleArrayToObject()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T4> $repo_type
	 * @param T2 $repo_args
	 * @param S $append_this
	 * @param S3 $patch_this
	 * @param S2 $expect_this
	 */
	public function test_patch_object(
		string $repo_type,
		array $repo_args,
		array $append_this,
		array $patch_this,
		array $expect_this
	) : void {
		$repo = new $repo_type(
			$repo_args
		);

		/** @var T1 */
		$object = $repo->ConvertSimpleArrayToObject($append_this);

		$fresh = $repo->AppendObject($object);

		$id = $repo->ObtainIdFromObject($fresh);

		$repo->PatchObjectData($id, $patch_this);

		/** @var T1 */
		$fresh2 = $repo->RecallObject($id);

		static::assertSame(
			$expect_this,
			$repo->ConvertObjectToSimpleArray($fresh2)
		);
	}
}
