<?php namespace LW\Statistics;

use Carbon\Carbon;

class RangeHelper {

	/**
	 * @var array
	 */
	protected $words = [
		'yesterday' => 'LW\Statistics\YesterdayRange',
		'today' => 'LW\Statistics\TodayRange',
		'last-week' => 'LW\Statistics\LastWeekRange',
		'last-month' => 'LW\Statistics\LastMonthRange',
		'last-year' => 'LW\Statistics\LastYearRange',
	];

	/**
	 * @param string $period
	 * @return \LW\Statistics\DateRange
	 */
	public function parse($period)
	{
		if (array_key_exists($period, $this->words))
		{
			return $this->build($period);
		}

		if ($range = $this->makeDayMonthOrYearRange($period))
		{
			return $range;
		}

		return $this->makeFallbackRange($period);
	}

	/**
	 * @param string $word
	 * @return mixed
	 */
	protected function build($word)
	{
		$class = array_get($this->words, $word);
		return new $class;
	}

	/**
	 * If the string has the second char as ':' then it will see if
	 * the first character is either 'd', 'm' or 'y'
	 *
	 * @param string $period
	 * @return \LW\Statistics\DateRange|null
	 */
	protected function makeDayMonthOrYearRange($period)
	{
		if (':' !== $period{1})
		{
			return null;
		}

		if ('d' === $period{0})
		{
			return $this->makeDayRange(substr($period, 2));
		}

		if ('m' === $period{0})
		{
			return $this->makeMonthRange(substr($period, 2));
		}

		if ('y' === $period{0})
		{
			return $this->makeYearRange(substr($period, 2));
		}

		return null;
	}

	/**
	 * @param string $period
	 * @return \LW\Statistics\DayRange
	 */
	protected function makeDayRange($period)
	{
		return new DayRange(Carbon::createFromFormat('Y-m-d', $period));
	}

	/**
	 * @param string $period
	 * @return \LW\Statistics\MonthRange
	 */
	protected function makeMonthRange($period)
	{
		return new MonthRange(Carbon::createFromFormat('Y-m', $period));
	}

	/**
	 * @param string $period
	 * @return \LW\Statistics\YearRange
	 */
	protected function makeYearRange($period)
	{
		return new YearRange(Carbon::createFromFormat('Y', $period));
	}

	/**
	 * @param string $start
	 * @param string $end
	 * @return \LW\Statistics\DateRange
	 */
	protected function makeDateRange($start, $end)
	{
		$cStart = Carbon::createFromFormat('Y-m-d', $start);
		$cEnd   = Carbon::createFromFormat('Y-m-d', $end);

		return new DateRange($cStart, $cEnd);
	}

	/**
	 * @param string $period
	 * @return \LW\Statistics\DateRange
	 */
	protected function makeFallbackRange($period)
	{
		if (preg_match('/^(\d{4}\-\d{2}\-\d{2})\:(\d{4}\-\d{2}\-\d{2})$/', $period, $matches))
		{
			return $this->makeDateRange($matches[1], $matches[2]);
		}

		if (preg_match('/^(\d{4}\-\d{2}\-\d{2})$/', $period, $matches))
		{
			return $this->makeDateRange($matches[1], date('Y-m-d'));
		}

		throw new \InvalidArgumentException('Unable to parse period.');
	}

}