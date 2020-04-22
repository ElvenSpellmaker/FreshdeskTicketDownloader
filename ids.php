<?php

require 'vendor/autoload.php';

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\IdTicketFetcher;
use Psr\Log\LoggerInterface;

$func = function(Api $api, array $config, LoggerInterface $logger) : array
{
	$fetcher = new IdTicketFetcher($api, $logger);
	return $fetcher->fetchTicketDataForIds($config['fetch_ids']);
};

require 'include/run.php';
