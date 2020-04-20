<?php

$config = require __DIR__ . '/../config.local.php';

$companyName = $config['company_name'];
$freshdeskApiKey = $config['freshdesk_api_key'];
$startDate = $config['start_date'];
$endDate = $config['end_date'];
$ticketOutputFile = $config['ticket_output_file'];
$conversationOutputFile = $config['conversation_output_file'];
$ids = $config['fetch_ids'];
