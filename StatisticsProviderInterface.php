<?php namespace LW\Statistics;

use Carbon\Carbon;

interface StatisticsProviderInterface {

	/**
	 * @param \LW\Statistics\Interval $interval
	 */
	public function setInterval(Interval $interval);

	/**
	 * @param \Carbon\Carbon $start
	 * @param \Carbon\Carbon $end
	 * @return void
	 */
	public function setTimePeriod(Carbon $start, Carbon $end);

	/**
	 * @param string $column
	 * @return int
	 */
	public function count($column = '*');

	/**
	 * @param string $column
	 * @return int
	 */
	public function sum($column);

	/**
	 * @param string $column
	 * @return int
	 */
	public function max($column);

	/**
	 * @param string $column
	 * @return int
	 */
	public function min($column);

	/**
	 * @param string $column
	 * @return double
	 */
	public function avg($column);


}