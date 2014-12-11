<?php namespace LW\Statistics;

use Carbon\Carbon;

class LastMonthRange extends MonthRange {

	public function __construct()
	{
		parent::__construct(Carbon::now()->subMonth());
	}

}