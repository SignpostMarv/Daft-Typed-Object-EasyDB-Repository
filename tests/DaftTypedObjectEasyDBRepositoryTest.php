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

		return $out;
	}

	/**
	* @template K as key-of<S>
	*
	* @dataProvider dataProviderAppendTypedObject
	*
	* @param class-string<AppendableTypedObjectRepository> $repo_type
	* @param array{type:class-string<T1>} $repo_args
	* @param array<int, S> $append_these
	* @param array<int, S2> $expect_these
	*/
	public function testAppendTypedObject(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::testAppendTypedObject(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);

		if (Fixtures\MutableForRepository::class === $repo_args['type']) {
			$repo = new $repo_type(
				$repo_args
			);

			$object_type = $repo_args['type'];

			/**
			* @var array<int, T1>
			*/
			$testing = [];

			foreach ($append_these as $i => $data) {
				/**
				* @var T1
				*/
				$object = $object_type::__fromArray($data);

				$testing[$i] = $repo->AppendTypedObject($object);

				$data['name'] = strrev($testing[$i]->name);
				$data['id'] = $testing[$i]->id;

				$repo->ForgetTypedObject($object->ObtainId());

				$replacing_with = $object_type::__fromArray($data);

				$fresh = $repo->RecallTypedObject($replacing_with->ObtainId());

				$this->assertSame($object->name, $fresh->name);

				$repo->UpdateTypedObject($replacing_with);

				$fresh = $repo->RecallTypedObject($replacing_with->ObtainId());

				$this->assertNotSame($object->name, $fresh->name);
			}
		}
	}
}
