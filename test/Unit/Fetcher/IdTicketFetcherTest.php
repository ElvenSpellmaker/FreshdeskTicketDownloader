<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use Mockery;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\IdTicketFetcher;

class IdTicketFetcherTest extends TestCase
{
	/**
	 * @var Api
	 */
	private $api;

	/**
	 * @var TestHandler
	 */
	private $handler;

	/**
	 * @var IdTicketFetcher
	 */
	private $idTicketFetcher;

	public function setUp() : void
	{
		/**
		 * @var Api
		 */
		$this->api = Mockery::mock(Api::class);

		$this->handler = new TestHandler;

		$logger = new Logger('test');
		$logger->pushHandler($this->handler);

		$this->idTicketFetcher = new IdTicketFetcher($this->api, $logger);
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testFetchingIds()
	{
		$this->api
			->shouldReceive('getTicket')
			->with('10')
			->andReturn('{"foo":"bar"}')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getTicket')
			->with('11')
			->andReturn('{"foo":"baz"}')
			->once()
			->ordered();

		$data = $this->idTicketFetcher->fetchTicketDataForIds([
			'10',
			'11',
		]);

		$this->assertSame(
			[
				[
					'foo' => 'bar',
				],
				[
					'foo' => 'baz',
				],
			],
			$data,
		);

		$this->assertSame(
			[
				'Fetching results from Freshdesk (10): 1/2',
				'Fetching results from Freshdesk (11): 2/2',
			],
			array_column($this->handler->getRecords(), 'message'),
		);
	}
}
