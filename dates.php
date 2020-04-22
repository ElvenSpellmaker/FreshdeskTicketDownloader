<?php

require __DIR__ . '/vendor/autoload.php';

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\DateTicketFetcher;
use Psr\Log\LoggerInterface;

$func = function(Api $api, array $config, LoggerInterface $logger) : array
{
	$fetcher = new DateTicketFetcher($api, $logger);
	return $fetcher->fetchTicketDataInPeriod(
		$config['start_date'],
		$config['end_date'],
	);
};

require __DIR__ . '/include/run.php';
