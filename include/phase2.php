<?php

use ElvenSpellmaker\Freshdesk\CsvHelper;
use ElvenSpellmaker\Freshdesk\Parser;

echo 'Phase 2: Put on those shades and wave to yesterday, the sunlight hurts my eyes (https://www.youtube.com/watch?v=9gW30MVPS9I)...', "\n";
$csvHelper = new CsvHelper;
$parser = new Parser;
$csv = $csvHelper->getCsvHandle();
$ids = $parser->transformIntoCsv($csv, $results);
file_put_contents($ticketOutputFile, $csv);
echo 'End of Phase 2: Tickets processed: ', count($ids), "\n";
