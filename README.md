Freshdesk Ticket Downloader [![Build Status](https://travis-ci.com/ElvenSpellmaker/FreshdeskTicketDownloader.svg?branch=master)](https://travis-ci.com/ElvenSpellmaker/FreshdeskTicketDownloader)
===========================
This is a small project which can download Freshdesk tickets and conversations
on Tickets based on a date or a list of IDs.

Requirements:
  - PHP 7.3+
  - `composer`

or

  - Docker, tested on:
    - `php:7.3.17-zts-alpine3.11`
	- `php:7.4.5-zts-alpine3.11`

Usage
-----
Copy `config.php` to `config.local.php` and fill in the required values.

Run `composer install` to fetch the dependencies.

Then call either `php dates.php` or `php ids.php` depending on whether you want
to use dates or ids as the search method.

Docker Usage
------------

If you don't have PHP installed you can run this in a Docker container. Follow
the above to set up the config and get dependencies, and then run:

For dates:
`docker run -v "$(pwd)":/app --entrypoint 'php' -it php:7.3.17-zts-alpine3.11 app/dates.php`

For IDs:
`docker run -v "$(pwd)":/app --entrypoint 'php' -it php:7.3.17-zts-alpine3.11 app/ids.php`
