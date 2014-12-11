<?php namespace LW\Statistics;

use Carbon\Carbon;

class LastWeekRange extends WeekRange {

	public function __construct()
	{
		parent::__construct(Carbon::now()->subWeek());
	}

}