<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\DateTicketFetcher;
use Mockery;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;

class DateTicketFetcherTest extends TestCase
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
	 * @var DateTicketFetcher
	 */
	private $dateTicketFetcher;

	public function setUp() : void
	{
		/**
		 * @var Api
		 */
		$this->api = Mockery::mock(Api::class);

		$this->handler = new TestHandler;

		$logger = new Logger('test');
		$logger->pushHandler($this->handler);

		$this->dateTicketFetcher = new DateTicketFetcher($this->api, $logger);
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testDateTicketFetcher()
	{
		$startDate = '2020-04-19';
		$endDate = '2020-04-21';

		$this->setupApiExpectations();

		$results = $this->dateTicketFetcher->fetchTicketDataInPeriod($startDate, $endDate);

		$this->assertSame(
			[
				[
					'foo' => 'foo',
				],
				[
					'foo' => 'bar',
				],
				[
					'foo' => 'cooking',
				],
				[
					'foo' => 'by',
				],
				[
					'foo' => 'the',
				],
				[
					'foo' => 'book',
				],
				[
					'foo' => 'waffle',
				],
				[
					'foo' => 'pancake',
				],
				[
					'foo' => 'cat',
				],
				[
					'foo' => 'duck',
				],
				[
					'foo' => 'mouse',
				],
				[
					'foo' => 'bunny',
				],
				[
					'foo' => 'sticks',
				],
			],
			$results
		);

		$messages = array_column($this->handler->getRecords(), 'message');

		$this->assertSame(
			[
				'Fetching results from Freshdesk (2020-04-19): 2/2',
				'Fetching results from Freshdesk (2020-04-20): 2/10',
				'Fetching results from Freshdesk (2020-04-20): 4/10',
				'Fetching results from Freshdesk (2020-04-20): 6/10',
				'Fetching results from Freshdesk (2020-04-20): 8/10',
				'Fetching results from Freshdesk (2020-04-20): 10/10',
				'Fetching results from Freshdesk (2020-04-21): 1/1',
			],
			$messages,
		);
	}

	private function setupApiExpectations()
	{
		// One page
		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-19', '2020-04-20', 1)
			->andReturn('{"results": [{"foo": "foo"}, {"foo": "bar"}], "total": 2}')
			->once()
			->ordered();

		// 5 pages
		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-20', '2020-04-21', 1)
			->andReturn('{"results": [{"foo": "cooking"}, {"foo": "by"}], "total": 10}')
			->once()
			->ordered();

		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-20', '2020-04-21', 2)
			->andReturn('{"results": [{"foo": "the"}, {"foo": "book"}], "total": 10}')
			->once()
			->ordered();

		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-20', '2020-04-21', 3)
			->andReturn('{"results": [{"foo": "waffle"}, {"foo": "pancake"}], "total": 10}')
			->once()
			->ordered();

		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-20', '2020-04-21', 4)
			->andReturn('{"results": [{"foo": "cat"}, {"foo": "duck"}], "total": 10}')
			->once()
			->ordered();

		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-20', '2020-04-21', 5)
			->andReturn('{"results": [{"foo": "mouse"}, {"foo": "bunny"}], "total": 10}')
			->once()
			->ordered();

		// One page, one result
		$this->api
			->shouldReceive('searchTickets')
			->with('2020-04-21', '2020-04-22', 1)
			->andReturn('{"results": [{"foo": "sticks"}], "total": 1}')
			->once()
			->ordered();
	}
}
