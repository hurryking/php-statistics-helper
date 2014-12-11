<?php namespace LW\Statistics;

use Carbon\Carbon;

class LastYearRange extends YearRange {

	public function __construct()
	{
		parent::__construct(Carbon::now()->subYear());
	}

}