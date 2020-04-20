<?php

namespace ElvenSpellmaker\Freshdesk\Fetcher;

use DatePeriod;
use DateTime;
use ElvenSpellmaker\Freshdesk\Fetcher\AbstractFetcher;

/**
 * Given a Freshdesk API this will fetch all Pages of Data possible.
 */
class DateTicketFetcher extends AbstractFetcher
{
	/**
	 * Fetches all Ticket data for the period of time between the start date
	 * and the end date looping one day at a time from Freshdesk.
	 *
	 * Dates should be in a DateTime parsable format.
	 *
	 * Returns a flat array with all the results from the API.
	 *
	 * @param string $startDate
	 * @param string $endDate
	 *
	 * @return array
	 */
	public function fetchTicketDataInPeriod(string $startDate, string $endDate) : array
	{
		$datePeriod = new DatePeriod(
			new DateTime($startDate),
			$this->dateInterval,
			new DateTime($endDate),
		);

		$results = [];
		foreach ($datePeriod as $date)
		{
			$results = $this->fetchTicketDataForDate($date, $results);
		}

		return $results;
	}

	/**
	 * Fetches all Freshdesk Ticket data for a particular date, and returns a
	 * merged dataset.
	 *
	 * @param DateTime $date
	 * @param array    $results
	 *
	 * @return array
	 */
	private function fetchTicketDataForDate(DateTime $date, array $results) : array
	{
		$startDate = $date->format('Y-m-d');
		$endDate = $date->add($this->dateInterval)->format('Y-m-d');

		$page = 1;
		$runningTotal = 0;
		do
		{
			$result = $this->getTicketDataFromFreshdesk($startDate, $endDate, $page++);

			$runningTotal += count($result['results']);

			$results = array_merge($results, $result['results']);

			echo 'Fetching results from Freshdesk (' . $startDate . '): ', $runningTotal, '/', $result['total'], "\n";
		}
		while ($runningTotal !== $result['total']);

		return $results;
	}

	/**
	 * Given a start and end date, fetches a page of Freshdesk data.
	 *
 * Returns the JSON decoded data as an array.
	 *
	 * @param string $startDate
	 * @param string $endDate
	 * @param int    $page
	 *
	 * @return array
	 */
	private function getTicketDataFromFreshdesk(string $startDate, string $endDate, int $page) : array
	{
		return $this->getDataWrapper(function() use ($startDate, $endDate, $page) {
			return $this->api->searchTickets($startDate, $endDate, $page);
		});
	}
}
