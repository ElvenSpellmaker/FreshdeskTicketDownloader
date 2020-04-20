<?php

use ElvenSpellmaker\Freshdesk\Fetcher\ConversationFetcher;

echo "Phase 3: Bacteria we begin with only one (https://www.youtube.com/watch?v=i3Rx1zJ3BZ0)...\n";
$csv = $csvHelper->getCsvHandle();
$fetcher = new ConversationFetcher($api);
$conversations = $fetcher->fetchConversationsForTickets($ids);
$parser->transformIntoCsv($csv, $conversations);
file_put_contents($conversationOutputFile, $csv);
echo 'End of Phase 3: Conversations Found: ', count($conversations), "\n";
