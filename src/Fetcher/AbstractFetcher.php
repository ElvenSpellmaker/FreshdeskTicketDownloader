<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use Closure;
use DateInterval;
use ElvenSpellmaker\Freshdesk\Api;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Houses Base logic for a Freshdesk Fetcher.
 */
abstract class AbstractFetcher
{
	/**
	 * @var Api
	 */
	protected $api;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var DateInterval
	 */
	protected $dateInterval;

	/**
	 * @param Api $api
	 */
	public function __construct(Api $api, LoggerInterface $logger)
	{
		$this->api = $api;
		$this->logger = $logger;
		$this->dateInterval = new DateInterval('P1D');
	}

	/**
	 * Wraps a Freshdesk API call to catch the exception and print the message
	 * and exit on error.
	 *
	 * @param Closure $fetchFunction
	 *
	 * @return array
	 *
	 * @throws RequestException
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	protected function getDataWrapper(Closure $fetchFunction) : array
	{
		try
		{
			$result = $fetchFunction();
		}
		catch (RequestException $e)
		{
			$this->logger->info($e->getResponse()->getBody());

			throw $e;
		}

		return json_decode($result, true);
	}
}
