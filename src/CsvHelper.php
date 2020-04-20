<?php

namespace ElvenSpellmaker\Freshdesk;

use League\Csv\Writer;
use SplTempFileObject;

/**
 * Provides a wrapper around generating a League CSV Writer.
 */
class CsvHelper
{
	/**
	 * Gets a Handle to a CSV Writer Object using a memory file object.
	 *
	 * @return Writer
	 */
	public function getCsvHandle() : Writer
	{
		return Writer::createFromFileObject(new SplTempFileObject);
	}
}
