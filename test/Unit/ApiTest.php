<?php

namespace ElvenSpellmaker\Freshdesk;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
	/**
	 * @var Api
	 */
	private $api;

	/**
	 * @var array
	 */
	private $container;

	/**
	 * @var MockHandler
	 */
	private $handler;

	public function setUp() : void
	{
		$this->container = [];
		$history = Middleware::history($this->container);

		$this->handler = new MockHandler();

		$hs = HandlerStack::create($this->handler);
		$hs->push($history);

		$client = new Client(['handler' => $hs]);

		$this->api = new Api('mysupercompany', 'myspecialkey', $client);
	}

	public function tearDown() : void
	{
		$this->handler->reset();
		$this->container = [];
	}

	public function testSearchTickets() : void
	{
		$this->handler->append(
			new Response(200, [], 'I\'m a teapot, short and stout.'),
			new Response(200, [], 'Full steam ahead!'),
		);

		$this->assertSame(
			'I\'m a teapot, short and stout.',
			$this->api->searchTickets('2020-04-22', '2020-04-23', 1),
		);

		$this->assertSame(
			'Full steam ahead!',
			$this->api->searchTickets('2020-04-22', '2020-04-23', 2),
		);

		$this->assertCount(2, $this->container);

		$page = 1;

		/**
		 * @var Request
		 */
		foreach ($this->container as ['request' => $request])
		{
			$headers = $request->getHeaders();

			// Guzzle UA can change.
			unset($headers['User-Agent']);

			$this->assertSame(
				[
					'Authorization' => [
						'Basic ' . base64_encode('myspecialkey:'),
					],
					'Host' => [
						'mysupercompany.freshdesk.com',
					],
					'Content-Type' => [
						'application/json',
					]
				],
				$headers,
			);

			$query = rawurlencode('"(status:2 OR status:3 OR status:6 OR status:7 OR status:8 OR status:9 OR status:10) AND (created_at:>\'2020-04-22\' AND created_at:<\'2020-04-23\')"');

			$this->assertSame(
				'https://mysupercompany.freshdesk.com/api/v2/search/tickets?query=' . $query . '&page=' . $page++,
				(string)$request->getUri(),
			);
		}
	}

	public function testGetConversations() : void
	{
		$this->handler->append(
			new Response(200, [], 'I\'m a teapot, short and stout.'),
			new Response(200, [], 'Full steam ahead!'),
		);

		$this->assertSame(
			'I\'m a teapot, short and stout.',
			$this->api->getConverstions('25', 1),
		);

		$this->assertSame(
			'Full steam ahead!',
			$this->api->getConverstions('25', 2),
		);

		$this->assertCount(2, $this->container);

		$page = 1;

		/**
		 * @var Request
		 */
		foreach ($this->container as ['request' => $request])
		{
			$headers = $request->getHeaders();

			// Guzzle UA can change.
			unset($headers['User-Agent']);

			$this->assertSame(
				[
					'Authorization' => [
						'Basic ' . base64_encode('myspecialkey:'),
					],
					'Host' => [
						'mysupercompany.freshdesk.com',
					],
					'Content-Type' => [
						'application/json',
					]
				],
				$headers,
			);

			$this->assertSame(
				'https://mysupercompany.freshdesk.com/api/v2/tickets/25/conversations?page=' . $page++,
				(string)$request->getUri(),
			);
		}
	}

	public function testGetTicketTest() : void
	{
		$this->handler->append(new Response(200, [], 'I\'m a teapot, short and stout.'));

		$this->assertSame(
			'I\'m a teapot, short and stout.',
			$this->api->getTicket('25'),
		);

		$this->assertCount(1, $this->container);

		/**
		 * @var Request
		 */
		$request = $this->container[0]['request'];

		$headers = $request->getHeaders();

		// Guzzle UA can change.
		unset($headers['User-Agent']);

		$this->assertSame(
			[
				'Authorization' => [
					'Basic ' . base64_encode('myspecialkey:'),
				],
				'Host' => [
					'mysupercompany.freshdesk.com',
				],
				'Content-Type' => [
					'application/json',
				]
			],
			$headers,
		);

		$this->assertSame(
			'https://mysupercompany.freshdesk.com/api/v2/tickets/25',
			(string)$request->getUri(),
		);
	}
}
