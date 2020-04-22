<?php

namespace ElvenSpellmaker\Freshdesk;

use GuzzleHttp\Client;

/**
 * Provides an interface to the Freshdesk API.
 */
class Api
{
	const BASE_URL = 'https://%s.freshdesk.com';

	const API_ROOT = '/api/v2';

	const SEARCH_URL = self::API_ROOT . '/search/tickets';
	const CONVERSATION_URL = self::API_ROOT . '/tickets/%s/conversations';
	const TICKET_URL = self::API_ROOT . '/tickets/%s';

	const ID_KEY = 'id';
	const CUSTOM_FIELD_KEY = 'custom_fields';
	const ATTACHMENTS_KEY = 'attachments';

	// From https://developer.freshdesk.com/api/#pagination
	// const MAX_PAGE_SIZE = 30; // Says we can up to 100 but it doesn't work either way.

	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * @var string
	 */
	private $baseUrl;

	/**
	 * @var Client
	 */
	private $guzzleClient;

	/**
	 * @param string $companyName
	 * @param string $apiKey
	 */
	public function __construct(string $companyName, string $apiKey, Client $client)
	{
		$this->apiKey = $apiKey;

		$this->baseUrl = sprintf(self::BASE_URL, $companyName);

		$this->guzzleClient = $client;
	}

	/**
	 * Searches tickets from a start sate until an end date and retrieves a
	 * given page.
	 *
	 * Note: Only returns open tickets, resolved (status code 4) and closed (5)
	 * are not searched.
	 *
	 * @param string $startDate
	 * @param string $endDate
	 * @param int    $page
	 *
	 * @return string
	 */
	public function searchTickets(string $startDate, string $endDate, int $page = 1) : string
	{
		return $this->performRequest(
			self::SEARCH_URL,
			[
				'query' => '"(status:2 OR status:3 OR status:6 OR status:7 OR status:8 OR status:9 OR status:10) AND (created_at:>\'' . $startDate . '\' AND created_at:<\'' . $endDate . '\')"',
				'page' => $page,
			],
		);
	}

	/**
	 * Gets a page of conversations for a ticket ID.
	 *
	 * @param string $id
	 * @param int    $page
	 *
	 * @return string
	 */
	public function getConverstions(string $id, int $page = 1) : string
	{
		return $this->performRequest(
			sprintf(self::CONVERSATION_URL, $id),
			[
				'page' => $page,
			],
		);
	}

	/**
	 * Gets a ticket with a specific ID.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public function getTicket(string $id) : string
	{
		return $this->performRequest(
			sprintf(self::TICKET_URL, $id),
		);
	}

	/**
	 * Performs an API request to Freshdesk.
	 *
	 * @param string $url
	 * @param array  $query
	 *
	 * @return string
	 */
	private function performRequest(string $url, array $query = []) : string
	{
		$response = $this->guzzleClient->get($url, [
			'base_uri' => $this->baseUrl,
			'debug' => false,
			'auth' => [
				$this->apiKey,
				'', // No password needed, API key is enough.
			],
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'query' => $query,
		]);

		return $response->getBody()->getContents();
	}
}
