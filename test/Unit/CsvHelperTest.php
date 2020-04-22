<?php

namespace ElvenSpellmaker\Freshdesk;

use League\Csv\Writer;
use Mockery;
use PHPUnit\Framework\TestCase;
use SplTempFileObject;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @group longrun
 */
class CsvHelperTest extends TestCase
{
	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testCsvReturn()
	{
		$externalMock = Mockery::mock('alias:' . Writer::class);
		$externalMock->shouldReceive('createFromFileObject')
			->with(Mockery::type('SplTempFileObject'))
			->andReturn(new Writer)
			->once()
			->ordered();

		$csv = (new CsvHelper)->getCsvHandle();

		$this->assertInstanceOf(Writer::class, $csv);
	}
}
