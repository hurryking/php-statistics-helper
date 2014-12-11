<?php namespace LW\Statistics;

use Carbon\Carbon;

class WeekRange extends DateRange {

	/**
	 * @param \Carbon\Carbon $week
	 */
	public function __construct(Carbon $week)
	{
		$start = clone $week;
		$end = clone $week;

		$start->startOfWeek();
		$end->endOfWeek();

		parent::__construct($start, $end);
	}

}