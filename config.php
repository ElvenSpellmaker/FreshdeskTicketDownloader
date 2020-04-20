<?php

return [
	// WHICH COMPANY ARE YOU FROM??
	'company_name' => '',
	'freshdesk_api_key' => '',
	// Please give these in a format that PHP's DateTime understands, else it'll say STOP PLEASE
	'start_date' => '2020-01-01',
	'end_date' => 'today',
	// Escenario goes into Estagging like Conversations go into Tickets
	'ticket_output_file' => 'estagging.csv',
	'conversation_output_file' => 'escenario.csv',
	// Combines with `ids.php` to fetch only specific IDs
	'fetch_ids' => [],
];
