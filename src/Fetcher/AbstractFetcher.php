<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use Closure;
use DateInterval;
use ElvenSpellmaker\Freshdesk\Api;
use GuzzleHttp\Exception\RequestException;

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
	 * @var DateInterval
	 */
	protected $dateInterval;

	/**
	 * @param Api $api
	 */
	public function __construct(Api $api)
	{
		$this->api = $api;
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
			echo $e->getResponse()->getBody();
			exit;
		}

		return json_decode($result, true);
	}
}
