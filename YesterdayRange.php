<?php namespace LW\Statistics;

use Carbon\Carbon;

class YesterdayRange extends DayRange {

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		parent::__construct(Carbon::yesterday());
	}

}