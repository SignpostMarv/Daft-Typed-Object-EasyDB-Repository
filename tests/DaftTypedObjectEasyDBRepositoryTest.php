<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use ParagonIE\EasyDB\Factory;

class DaftTypedObjectEasyDBRepositoryTest extends DaftTypedObjectRepositoryTest
{
	public function dataProviderAppendTypedObject() : array
	{
		$out = [
			[
				Fixtures\MutableForRepository::class,
				Fixtures\EasyDBTestRepository::class,
				[
					Factory::create('sqlite::memory:'),
					'foo',
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
				Fixtures\MutableForRepository::class,
				Fixtures\EasyDBTestRepository::class,
				[
					Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
					'foo',
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
