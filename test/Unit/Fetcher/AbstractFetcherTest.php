<?php

namespace ElvenSpellmaker\Freshdesk;

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\AbstractFetcher;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase as TestCase;

class AbstractFetcherTest extends TestCase
{
	public function tearDown() : void
	{
		Mockery::close();
	}

	public function testApiThrowingException()
	{
		$this->expectException(RequestException::class);
		$this->expectExceptionMessage('Exception!');

		$api = Mockery::mock(Api::class);

		$handler = new TestHandler;

		$logger = new Logger('test');
		$logger->pushHandler($handler);

		$abstractFetcher = Mockery::mock(AbstractFetcher::class, [
			$api,
			$logger,
		]);

		try
		{
			$abstractFetcher->getDataWrapper(function() {
				throw new RequestException(
					'Exception!',
					new Request('l', ''),
					new Response(200, [], 'Network Error!')
				);
			});
		}
		catch (RequestException $e)
		{
			// Intercept the Logging and test the message is correct.
			$messages = array_column($handler->getRecords(), 'message');
			$this->assertSame(
				[
					'Network Error!',
				],
				$messages,
			);

			// Rethrow the exception to pass the test!
			throw $e;
		}
	}
}
