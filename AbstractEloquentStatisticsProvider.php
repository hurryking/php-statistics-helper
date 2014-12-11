<?php namespace LW\Statistics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentStatisticsProvider implements StatisticsProviderInterface {

	/**
	 * @var \Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * @var \Illuminate\Database\ConnectionInterface
	 */
	protected $db;

	/**
	 * @var \LW\Statistics\Interval
	 */
	protected $interval;

	/**
	 * @var \Carbon\Carbon
	 */
	protected $start;

	/**
	 * @var \Carbon\Carbon
	 */
	protected $end;

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
		$this->db = $model->getConnection();
	}

	/**
	 * @param \LW\Statistics\Interval $interval
	 */
	public function setInterval(Interval $interval)
	{
		$this->interval = $interval;
	}

	/**
	 * @param \Carbon\Carbon $start
	 * @param \Carbon\Carbon $end
	 * @return void
	 */
	public function setTimePeriod(Carbon $start, Carbon $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	/**
	 * @param string $column
	 * @return int
	 */
	public function count($column = '*')
	{
		return $this->aggregate('count', $column);
	}

	/**
	 * @param string $column
	 * @return int
	 */
	public function sum($column)
	{
		return $this->aggregate('sum', $column);
	}

	/**
	 * @param string $column
	 * @return int
	 */
	public function max($column)
	{
		return $this->aggregate('max', $column);
	}

	/**
	 * @param string $column
	 * @return int
	 */
	public function min($column)
	{
		return $this->aggregate('min', $column);
	}

	/**
	 * @param string $column
	 * @return double
	 */
	public function avg($column)
	{
		return $this->aggregate('avg', $column);
	}

	/**
	 * @param string $func
	 * @param string $column
	 * @param \anlutro\LaravelRepository\CriteriaInterface[] $criteria
	 * @return int|array
	 */
	protected function aggregate($func, $column, array $criteria = [])
	{
		if (! $this->interval)
		{
			$query = $this->newQuery();

			array_map(function ($criteria) use ($query)
			{
				$criteria->apply($query);
			}, $criteria);

			return $query->{$func}($column);
		}

		return $this->aggregateInterval($func, $column, $criteria);
	}

	/**
	 * @param string $func
	 * @param string $column
	 * @param \anlutro\LaravelRepository\CriteriaInterface[] $criteria
	 * @return array
	 */
	protected function aggregateInterval($func, $column, array $criteria = [])
	{
		$query = $this->db->table($this->model->getTable());
		$this->interval->applyQuery($query);

		array_map(function ($criteria) use ($query)
		{
			$criteria->apply($query);
		}, $criteria);

		$expression = $this->db->raw("{$func}({$column}) as `__aggregate__`");
		$query->addSelect($expression);

		return $this->interval->parse($query->get(), $this->start, $this->end);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
	 */
	protected function newQuery()
	{
		$query = $this->model->newQuery();

		$this->setupQuery($query);

		return $query;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
	 */
	protected function setupQuery($query)
	{
		if ($this->start)
		{
			$query->where('created_at', '>=', $this->start->toDateTimeString());
		}

		if ($this->end)
		{
			$query->where('created_at', '<=', $this->end->toDateTimeString());
		}
	}

}