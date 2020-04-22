<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use ElvenSpellmaker\Freshdesk\Fetcher\AbstractFetcher;

/**
 * Given a Freshdesk API this will fetch all Pages of Data for conversations.
 */
class ConversationFetcher extends AbstractFetcher
{
	/**
	 * Fetches all the conversations for the given list of Ticket IDs.
	 *
	 * @param array $ticketIds
	 *
	 * @return array
	 */
	public function fetchConversationsForTickets(array $ticketIds) : array
	{
		$results = [];
		$total = 0;

		foreach ($ticketIds as $ticketId)
		{
			$result = $this->fetchConversationsForTicket($ticketId);

			$results = array_merge($results, $result);

			$total = count($results);

			$this->logger->info('Fetching results from Freshdesk: Total: ' . $total);
		}

		return $results;
	}

	/**
	 * Fetches all conversations for a given ticket ID.
	 *
	 * @param string $ticketId
	 *
	 * @return array
	 */
	private function fetchConversationsForTicket(string $ticketId) : array
	{
		$results = [];
		$page = 1;
		$total = 0;

		do
		{
			$result = $this->getConversationDataFromFreshdesk($ticketId, $page++);

			$results = array_merge($results, $result);

			$total += count($result);

			$this->logger->info('Fetching results from Freshdesk (Ticket ID: ' . $ticketId . '): New: ' . count($result) . ', Total: ' . $total);
		}
		while (count($result));

		return $results;
	}

	/**
	 * Given an ID, fetches a page of Freshdesk data.
	 *
	 * Returns the JSON decoded data as an array.
	 *
	 * @param string $id
	 * @param int    $page
	 *
	 * @return array
	 */
	private function getConversationDataFromFreshdesk(string $id, int $page) : array
	{
		return $this->getDataWrapper(function() use ($id, $page) {
			return $this->api->getConverstions($id, $page);
		});
	}
}
