<?php

require 'vendor/autoload.php';

use ElvenSpellmaker\Freshdesk\Api;
use ElvenSpellmaker\Freshdesk\Fetcher\IdTicketFetcher;

$func = function(Api $api, array $config) : array
{
	$fetcher = new IdTicketFetcher($api);
	return $fetcher->fetchTicketDataForIds($config['fetch_ids']);
};

require 'include/run.php';
