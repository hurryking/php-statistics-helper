<?php namespace LW\Statistics;

use Carbon\Carbon;

class DayRange extends DateRange {

	/**
	 * @param \Carbon\Carbon $day
	 */
	public function __construct(Carbon $day)
	{
		$start = clone $day;
		$end = clone $day;

		$start->startOfDay();
		$end->endOfDay();

		parent::__construct($start, $end);
	}

}