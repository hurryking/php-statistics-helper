<?php namespace LW\Statistics;

use Carbon\Carbon;

class YearRange extends DateRange {

	/**
	 * @param \Carbon\Carbon $year
	 */
	public function __construct(Carbon $year)
	{
		$start = clone $year;
		$end = clone $year;

		$start->startOfYear();
		$end->endOfYear();

		parent::__construct($start, $end);
	}

}