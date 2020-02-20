<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;

/**
 * @template S as array<string, scalar|null>
 * @template S2 as array<string, scalar|null>
 * @template T as array<string, scalar|array|object|null>
 * @template T1 as DaftTypedObjectForRepository
 *
 * @template-extends DaftTypedObjectRepositoryTest<S, S2, T, T1>
 */
class DaftTypedObjectEasyDBRepositoryTest extends DaftTypedObjectRepositoryTest
{
	public function dataProviderAppendTypedObject() : array
	{
		$out = [
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

		if ('true' === getenv('TRAVIS')) {
			$out[] = [
				Fixtures\EasyDBTestRepository::class,
				[
					'type' => Fixtures\MutableForRepository::class,
					EasyDB::class => Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
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
			];
		}

		/**
		 * @var list<
		 *	array{
		 *		0:class-string<AppendableTypedObjectRepository>,
		 *		1:array{type:class-string<T1>},
		 *		2:list<S>,
		 *		3:list<S2>
		 *	}
		 * >
		 */
		return $out;
	}

	/**
	 * @template K as key-of<S>
	 *
	 * @dataProvider dataProviderAppendTypedObject
	 *
	 * @param class-string<AppendableTypedObjectRepository> $repo_type
	 * @param array{type:class-string<T1>} $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_append_typed_object(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_append_typed_object(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);

		if (Fixtures\MutableForRepository::class === $repo_args['type']) {
			/**
			 * @var Fixtures\EasyDBTestRepository|AppendableTypedObjectRepository
			 */
			$repo = new $repo_type(
				$repo_args
			);

			$object_type = $repo_args['type'];

			/**
			 * @var list<Fixtures\MutableForRepository>
			 */
			$testing = [];

			foreach ($append_these as $i => $data) {
				/**
				 * @var Fixtures\MutableForRepository
				 */
				$object = $object_type::__fromArray($data);

				/**
				 * @var Fixtures\MutableForRepository
				 */
				$testing[$i] = $repo->AppendTypedObject($object);

				$data['name'] = strrev($testing[$i]->name);
				$data['id'] = $testing[$i]->id;

				$repo->ForgetTypedObject($object->ObtainId());

				/**
				 * @var Fixtures\MutableForRepository
				 */
				$replacing_with = $object_type::__fromArray($data);

				/**
				 * @var Fixtures\MutableForRepository
				 */
				$fresh = $repo->RecallTypedObject($replacing_with->ObtainId());

				static::assertSame($object->name, $fresh->name);

				$repo->UpdateTypedObject($replacing_with);

				/**
				 * @var Fixtures\MutableForRepository
				 */
				$fresh = $repo->RecallTypedObject($replacing_with->ObtainId());

				static::assertNotSame($object->name, $fresh->name);
			}
		}
	}

	/**
	 * @return list<
		array{
			0:class-string<AppendableTypedObjectRepository&PatchableObjectRepository>,
			1:array{type:class-string<T1>},
			2:array<string, scalar|null>,
			3:array<string, scalar|null>,
			4:array<string, scalar|null>
		}
	>
	 */
	public function dataProviderPatchObject() : array
	{
		$out = [
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
					'id' => '1',
					'name' => 'bar',
				],
			],
		];

		if ('true' === getenv('TRAVIS')) {
			$out[] = [
				Fixtures\EasyDBTestRepository::class,
				[
					'type' => Fixtures\MutableForRepository::class,
					EasyDB::class => Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
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
					'id' => '1',
					'name' => 'bar',
				],
			];
		}

		/**
		 * @var list<
			array{
				0:class-string<AppendableTypedObjectRepository&PatchableObjectRepository>,
				1:array{type:class-string<T1>},
				2:array<string, scalar|null>,
				3:array<string, scalar|null>,
				4:array<string, scalar|null>
			}
		>
		 */
		return $out;
	}
}
