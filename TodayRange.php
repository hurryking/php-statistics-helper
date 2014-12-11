<?php namespace LW\Statistics;

use Carbon\Carbon;

class TodayRange extends DayRange {

	public function __construct()
	{
		parent::__construct(Carbon::now());
	}

}