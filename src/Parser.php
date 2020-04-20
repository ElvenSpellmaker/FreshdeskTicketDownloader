<?php

namespace ElvenSpellmaker\Freshdesk;

use ElvenSpellmaker\Freshdesk\Api;
use League\Csv\Writer;

/**
 * The Parser takes Freshdesk API results and transforms them.
 */
class Parser
{
	/**
	 * Parses Freshdesk API results into a flattened structure and puts them
	 * inside a CSV Writer.
	 * Handles custom fields by pulling them up a level.
	 *
	 * Returns the IDs of all the data parsed.
	 *
	 * @param Writer $csv
	 * @param array  $results
	 *
	 * @return array
	 */
	public function transformIntoCsv(Writer $csv, array $results) : array
	{
		$headers = [];
		$ids = [];

		$headers = $this->extractHeaders($results[0]);
		$csv->insertOne($headers);

		foreach ($results as $result)
		{
			$row = [];

			$ids[] = $result[Api::ID_KEY];

			$row = $this->parseTicket($result);

			$csv->insertOne($row);
		}

		return $ids;
	}

	/**
	 * Extracts headers from the Freshdesk API response, moving custom fields
	 * up one level if they exist.
	 *
	 * @param array $ticket
	 *
	 * @return array
	 */
	private function extractHeaders(array $ticket) : array
	{
		$headers = array_keys($ticket);

		if (array_key_exists(Api::CUSTOM_FIELD_KEY, $ticket))
		{
			$cfHeaders = array_keys($ticket[Api::CUSTOM_FIELD_KEY]);

			$cfPos = array_search(Api::CUSTOM_FIELD_KEY, $headers);

			$beginning = array_slice($headers, 0, $cfPos);
			$end = array_slice($headers, $cfPos + 1);

			$headers = array_merge($beginning, $cfHeaders, $end);
		}

		return $headers;
	}

	/**
	 * Parses a ticket into a row that is compatible for a CSV. Handles custom
	 * fields by moving them up a level into the rows.
	 *
	 * @param array $ticket
	 *
	 * @return array
	 */
	private function parseTicket(array $ticket) : array
	{
		$row = [];

		foreach ($ticket as $key => $field)
		{
			if ($key === Api::CUSTOM_FIELD_KEY)
			{
				foreach ($field as $customField)
				{
					$row[] = $customField;
				}

				continue;
			}

			if (is_array($field))
			{
				if ($key === Api::ATTACHMENTS_KEY)
				{
					foreach ($field as &$attachment)
					{
						$attachment = json_encode($attachment);
					}
				}

				$field = join(';', $field);
			}

			if (is_bool($field))
			{
				$field = $field ? 'true' : 'false';
			}

			$row[] = $field;
		}

		return $row;
	}
}
