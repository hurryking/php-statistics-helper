<?php namespace LW\Statistics;

use Carbon\Carbon;

class MonthRange extends DateRange {

	/**
	 * @param \Carbon\Carbon $month
	 */
	public function __construct(Carbon $month)
	{
		$start = clone $month;
		$end = clone $month;

		$start->startOfMonth();
		$end->endOfMonth();

		parent::__construct($start, $end);
	}

}