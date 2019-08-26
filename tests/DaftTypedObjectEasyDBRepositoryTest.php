<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;

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
						'id' => 1,
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
						'id' => 1,
						'name' => 'foo',
					],
				],
			];
		}

		return $out;
	}
}
