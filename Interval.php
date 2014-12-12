<?php namespace LW\Statistics;

use Carbon\Carbon;

class Interval {

	/**
	 * @var string
	 */
	protected $interval;

	/**
	 * @var array
	 */
	protected $map = [
		'hourly' => ['hourly', 'hours', 'hour', 'h'],
		'daily' => ['daily', 'days', 'day', 'd'],
		'monthly' => ['monthly', 'months', 'month', 'm'],
		'yearly' => ['yearly', 'years', 'year', 'y'],
	];

	/**
	 * @var array
	 */
	protected $sqlSelect = [
		'hourly' => 'CONCAT(YEAR(created_at), \'-\', MONTH(created_at), \'-\', DAY(created_at), \'-\', HOUR(created_at))',
		'daily' => 'CONCAT(YEAR(created_at), \'-\', MONTH(created_at), \'-\', DAY(created_at))',
		'monthly' => 'CONCAT(YEAR(created_at), \'-\', MONTH(created_at))',
		'yearly' => 'YEAR(created_at)',
	];

	/**
	 * @var array
	 */
	protected $carbonAddition = [
		'hourly' => 'addHour',
		'daily' => 'addDay',
		'monthly' => 'addMonth',
		'yearly' => 'addYear',
	];

	/**
	 * @var array
	 */
	protected $format = [
		'hourly' => 'Y-m-d H:i',
		'daily' => 'Y-m-d',
		'monthly' => 'Y-m',
		'yearly' => 'Y',
	];

	/**
	 * @param string $interval
	 */
	public function __construct($interval)
	{
		foreach ($this->map as $name => $values)
		{
			if (in_array($interval, $values))
			{
				$this->interval = $name;
				return;
			}
		}

		throw new \InvalidArgumentException('Invalid interval.');
	}

	/**
	 * @return string
	 */
	protected function getSQLSelect()
	{
		return $this->sqlSelect[$this->interval];
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
	 * @return void
	 */
	public function applyQuery($query)
	{
		$query->selectRaw($this->getSQLSelect() . ' as __interval__');
		$query->groupBy('__interval__');
	}

	/**
	 * @param $results
	 * @param \Carbon\Carbon $start
	 * @param \Carbon\Carbon $end
	 * @return array
	 */
	public function parse($results, Carbon $start = null, Carbon $end = null)
	{
		if (! $start)
		{
			// @todo, where do we start from?
			throw new \RuntimeException('Intervals can only be used when a period is set.');
		}

		$results = $this->parseResults($results);
		$return = [];
		$current = clone $start;

		while ($current <= $end)
		{
			$interval = $this->getInterval($current);
			$return[$interval] = (int) array_get($results, $interval);

			$this->addInterval($current);
		}

		return $return;
	}

	/**
	 * @param \Carbon\Carbon $carbon
	 * @return void
	 */
	protected function addInterval($carbon)
	{
		call_user_func([$carbon, array_get($this->carbonAddition, $this->interval)]);
	}

	/**
	 * @param \Carbon\Carbon $carbon
	 * @return string
	 */
	protected function getInterval($carbon)
	{
		return $carbon->format(array_get($this->format, $this->interval));
	}

	/**
	 * @param \stdClass[] $results
	 * @return array
	 */
	protected function parseResults($results)
	{
		$return = [];

		foreach ($results as $result)
		{
			$return[object_get($result, '__interval__')] = object_get($result, '__aggregate__');
		}

		return $return;
	}

}