<?php

namespace ElvenSpellmaker\Freshdesk;

use ElvenSpellmaker\Freshdesk\Parser;
use League\Csv\Writer;
use Mockery;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
	/**
	 *@var Parser
	 */
	private $parser;

	public function setUp() : void
	{
		$this->parser = new Parser;
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testBasicParse()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'foo' => 'bar',
			],
			[
				'id' => '2',
				'foo' => 'baz',
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'foo',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'bar',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'baz',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}

	public function testCustomFieldsParse()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'foo' => 'bar',
				'custom_fields' => [
					'cf_foo' => 'barstool',
					'cf_baz' => 'sticks',
				],
				'param_after_custom' => 'lol',
			],
			[
				'id' => '2',
				'foo' => 'baz',
				'custom_fields' => [
					'cf_foo' => 'waffle',
					'cf_baz' => 'pancake',
				],
				'param_after_custom' => 'kek',
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'foo',
					'cf_foo',
					'cf_baz',
					'param_after_custom',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'bar',
					'barstool',
					'sticks',
					'lol'
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'baz',
					'waffle',
					'pancake',
					'kek',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}

	public function testBooleanField()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'foo' => true,
			],
			[
				'id' => '2',
				'foo' => false,
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'foo',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'true',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'false',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}

	public function testArrayField()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'foo' => [
					'foo',
					'bar',
				]
			],
			[
				'id' => '2',
				'foo' => [
					'baz',
					'sticks',
				],
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'foo',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'foo;bar',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'baz;sticks',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}

	public function testAttachmentField()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'attachments' => [
					'title1' => 'foo',
					'title2' => 'bar',
				]
			],
			[
				'id' => '2',
				'attachments' => [
					'title1' => 'baz',
					'title2' => 'sticks',
				],
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'attachments',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'"foo";"bar"',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'"baz";"sticks"',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}

	public function testComplexExample()
	{
		$freshdeskData = [
			[
				'id' => '1',
				'foo' => [
					'foo',
					'bar',
				],
				'attachments' => [
					'key1' => 'val1',
					'key2' => 'val2',
				],
				'custom_fields' => [
					'key1' => 'val3',
					'key2' => 'val4',
				],
				'tags' => [
					'tag1',
					'tag2',
				],
				'bool' => true,
			],
			[
				'id' => '2',
				'foo' => [
					'baz',
					'sticks',
				],
				'attachments' => [
					'key1' => 'val5',
					'key2' => 'val6',
				],
				'custom_fields' => [
					'key1' => 'val7',
					'key2' => 'val8',
				],
				'tags' => [
					'tag3',
					'tag4',
				],
				'bool' => false,
			],
		];

		$csv = Mockery::mock(Writer::class);

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'id',
					'foo',
					'attachments',
					'key1',
					'key2',
					'tags',
					'bool',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'1',
					'foo;bar',
					'"val1";"val2"',
					'val3',
					'val4',
					'tag1;tag2',
					'true',
				],
			)
			->ordered();

		$csv->shouldReceive('insertOne')
			->once()
			->with(
				[
					'2',
					'baz;sticks',
					'"val5";"val6"',
					'val7',
					'val8',
					'tag3;tag4',
					'false',
				],
			)
			->ordered();

		/**
		 * @var Writer $csv
		 */
		$ids = $this->parser->transformIntoCsv($csv, $freshdeskData);

		$this->assertEquals(['1', '2'], $ids);
	}
}
