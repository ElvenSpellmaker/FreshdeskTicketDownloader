<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\ConversationFetcher;
use Mockery;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;

class ConversationFetcherTest extends TestCase
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
	 * @var ConversationFetcher
	 */
	private $conversationFetcher;

	public function setUp() : void
	{
		/**
		 * @var Api
		 */
		$this->api = Mockery::mock(Api::class);

		$this->handler = new TestHandler;

		$logger = new Logger('test');
		$logger->pushHandler($this->handler);

		$this->conversationFetcher = new ConversationFetcher($this->api, $logger);
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testConversationFetcher()
	{
		$this->setupApiExpectations();

		$results = $this->conversationFetcher->fetchConversationsForTickets([
			'123',
			'234',
			'345',
		]);

		$this->assertSame(
			[
				[
					'foo' => 'foo',
					'id' => '123',
				],
				[
					'foo' => 'bar',
					'id' => '123',
				],
				[
					'foo' => 'foo',
					'id' => '234',
				],
				[
					'foo' => 'bar',
					'id' => '234',
				],
				[
					'foo' => 'baz',
					'id' => '234',
				],
				[
					'foo' => 'braz',
					'id' => '234',
				],
				[
					'foo' => 'sticks',
					'id' => '234',
				],
				[
					'foo' => 'twigs',
					'id' => '234',
				],
				[
					'foo' => 'funny',
					'id' => '234',
				],
				[
					'foo' => 'money',
					'id' => '234',
				],
				[
					'foo' => 'cooking',
					'id' => '234',
				],
				[
					'foo' => 'by',
					'id' => '234',
				],
				[
					'foo' => 'the',
					'id' => '345',
				],
				[
					'foo' => 'book',
					'id' => '345',
				],
			],
			$results
		);

		$messages = array_column($this->handler->getRecords(), 'message');

		$this->assertSame(
			[
				'Fetching results from Freshdesk (Ticket ID: 123): New: 2, Total: 2',
				'Fetching results from Freshdesk (Ticket ID: 123): New: 0, Total: 2',
				'Fetching results from Freshdesk: Total: 2',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 2, Total: 2',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 2, Total: 4',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 2, Total: 6',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 2, Total: 8',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 2, Total: 10',
				'Fetching results from Freshdesk (Ticket ID: 234): New: 0, Total: 10',
				'Fetching results from Freshdesk: Total: 12',
				'Fetching results from Freshdesk (Ticket ID: 345): New: 2, Total: 2',
				'Fetching results from Freshdesk (Ticket ID: 345): New: 0, Total: 2',
				'Fetching results from Freshdesk: Total: 14',
			],
			$messages,
		);
	}

	private function setupApiExpectations()
	{
		// One page, two calls
		$this->api
			->shouldReceive('getConverstions')
			->with('123', 1)
			->andReturn('[{"foo": "foo", "id": "123"}, {"foo": "bar", "id": "123"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('123', 2)
			->andReturn('[]')
			->once()
			->ordered();

		// Five pages, six calls
		$this->api
			->shouldReceive('getConverstions')
			->with('234', 1)
			->andReturn('[{"foo": "foo", "id": "234"}, {"foo": "bar", "id": "234"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('234', 2)
			->andReturn('[{"foo": "baz", "id": "234"}, {"foo": "braz", "id": "234"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('234', 3)
			->andReturn('[{"foo": "sticks", "id": "234"}, {"foo": "twigs", "id": "234"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('234', 4)
			->andReturn('[{"foo": "funny", "id": "234"}, {"foo": "money", "id": "234"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('234', 5)
			->andReturn('[{"foo": "cooking", "id": "234"}, {"foo": "by", "id": "234"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('234', 6)
			->andReturn('[]')
			->once()
			->ordered();

		// One page, two calls
		$this->api
			->shouldReceive('getConverstions')
			->with('345', 1)
			->andReturn('[{"foo": "the", "id": "345"}, {"foo": "book", "id": "345"}]')
			->once()
			->ordered();

		$this->api
			->shouldReceive('getConverstions')
			->with('345', 2)
			->andReturn('[]')
			->once()
			->ordered();
	}
}
