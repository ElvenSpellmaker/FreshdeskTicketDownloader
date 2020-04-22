<?php

use ElvenSpellmaker\Freshdesk\Api;
use GuzzleHttp\Client;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (! isset($func) || ! $func instanceof Closure)
{
	"`\$func` needs to be defined\n!";
}

echo "Phase 1: Power up the Bass Cannon (https://www.youtube.com/watch?v=-u0t8ZIlwuQ)...\n";
$api = new Api($companyName, $freshdeskApiKey, new Client);

$formatter = new LineFormatter("%message%\n");

$handler = new StreamHandler('php://output');
$handler->setFormatter($formatter);

$logger = new Logger('terminal');
$logger->pushHandler($handler);

try
{
	$results = $func($api, $config, $logger);
}
catch(Exception $e)
{
	exit;
}

echo 'End of Phase 1: Tickets found: ', count($results), "\n";
