<?php namespace LW\Statistics;

use Carbon\Carbon;

class DateRange {

	/**
	 * @var \Carbon\Carbon
	 */
	protected $start;

	/**
	 * @var \Carbon\Carbon
	 */
	protected $end;

	/**
	 * @param \Carbon\Carbon $start
	 * @param \Carbon\Carbon $end
	 */
	public function __construct(Carbon $start, Carbon $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	/**
	 * @param \LW\Statistics\StatisticsProviderInterface $provider
	 */
	public function apply(StatisticsProviderInterface $provider)
	{
		$provider->setTimePeriod($this->start, $this->end);
	}


}