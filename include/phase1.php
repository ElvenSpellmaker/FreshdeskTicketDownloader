<?php

use ElvenSpellmaker\Freshdesk\Api;

if (! isset($func) || ! $func instanceof Closure)
{
	"`\$func` needs to be defined\n!";
}

echo "Phase 1: Power up the Bass Cannon (https://www.youtube.com/watch?v=-u0t8ZIlwuQ)...\n";
$api = new Api($companyName, $freshdeskApiKey);
$results = $func($api, $config);
echo 'End of Phase 1: Tickets found: ', count($results), "\n";
