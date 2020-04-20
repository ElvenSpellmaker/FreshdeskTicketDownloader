<?php

require __DIR__ . '/vendor/autoload.php';

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\DateTicketFetcher;

$func = function(Api $api, array $config) : array
{
	$fetcher = new DateTicketFetcher($api);
	return $fetcher->fetchTicketDataInPeriod(
		$config['start_date'],
		$config['end_date'],
	);
};

require __DIR__ . '/include/run.php';
