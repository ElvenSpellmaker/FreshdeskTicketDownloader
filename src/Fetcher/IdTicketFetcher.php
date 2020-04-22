<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use DatePeriod;
use DateTime;
use ElvenSpellmaker\Freshdesk\Fetcher\AbstractFetcher;

/**
 * Given a Freshdesk API this will fetch all tickets for a given set of IDs.
 */
class IdTicketFetcher extends AbstractFetcher
{
	/**
	 * Fetches all Freshdesk Ticket data for a the given IDs.
	 *
	 * @param array $ids
	 *
	 * @return array
	 */
	public function fetchTicketDataForIds(array $ids) : array
	{
		$results = [];

		$runningTotal = 1;
		$total = count($ids);
		foreach ($ids as $id)
		{
			$results[] = $this->getTicketDataFromFreshdesk($id);

			$this->logger->info('Fetching results from Freshdesk (' . $id . '): ' . $runningTotal++ . '/' . $total);
		}

		return $results;
	}

	/**
	 * Given a an ID fetches the Ticket.
	 *
	 * Returns the JSON decoded data as an array.
	 *
	 * @param string $id
	 *
	 * @return array
	 */
	private function getTicketDataFromFreshdesk(string $id) : array
	{
		return $this->getDataWrapper(function() use ($id) {
			return $this->api->getTicket($id);
		});
	}
}
