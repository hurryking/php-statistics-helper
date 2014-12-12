<?php namespace LW\Statistics;

use Illuminate\Support\Collection;

class StatisticsHelper {

	/**
	 * @var \LW\Statistics\ProvidersHelper
	 */
	protected $providersHelper;

	/**
	 * @var \LW\Statistics\RangeHelper
	 */
	protected $rangeHelper;

	/**
	 * @var \LW\Statistics\DateRange
	 */
	protected $period;

	/**
	 * @var \LW\Statistics\Interval
	 */
	protected $interval;

	/**
	 * @param \LW\Statistics\ProvidersHelper $providersHelper
	 * @param \LW\Statistics\RangeHelper $rangeHelper
	 */
	public function __construct(ProvidersHelper $providersHelper, RangeHelper $rangeHelper)
	{
		$this->providersHelper = $providersHelper;
		$this->rangeHelper = $rangeHelper;
	}

	/**
	 * @return \LW\Statistics\ProvidersHelper
	 */
	public function providers()
	{
		return $this->providersHelper;
	}

	/**
	 * @param string $period
	 */
	public function period($period)
	{
		$this->period = $this->rangeHelper->parse($period);
	}

	/**
	 * @param array $interval
	 */
	public function interval($interval)
	{
		$this->interval = new Interval($interval);
	}

	/**
	 * @param array $providers
	 * @return \Illuminate\Support\Collection
	 */
	public function get(array $providers)
	{
		$return = new Collection;
		$providerCollection = $this->getProviderCollection(array_keys($providers));

		foreach ($providers as $provider => $scopes)
		{
			$return[$provider] = $this->results($providerCollection->get($provider), $scopes);
		}


		if ($this->interval)
		{
			$return['_totals'] = $this->totals($return);
		}
		
		return $return;
	}

	/**
	 * @param \Illuminate\Support\Collection $results
	 * @return array
	 */
	protected function totals($results)
	{
		$return = new Collection;

		$results->map(function($results, $provider) use ($return)
		{
			$totals = new Collection;

			foreach ($results as $scope => $resultSet)
			{
				$totals[$scope] = array_sum(array_values($resultSet));
			}


			$totals['_total'] = array_sum($totals->toArray());
			$return[$provider] = $totals;
		});

		return $return;
	}

	/**
	 * @param \LW\Statistics\StatisticsProviderInterface $provider
	 * @param array $scopes
	 * @return \Illuminate\Support\Collection
	 */
	protected function results(StatisticsProviderInterface $provider, $scopes)
	{
		if (is_string($scopes))
		{
			$scopes = explode(',', $scopes);
		}

		$results = new Collection;

		foreach ($scopes as $scope)
		{
			list($method, $args) = $this->parseScope($scope);

			$results[$scope] = $this->call($provider, $method, $args);
		}

		return $results;
	}

	/**
	 * @param \LW\Statistics\StatisticsProviderInterface $provider
	 * @param string $method
	 * @param array $args
	 * @return string|int|array
	 */
	protected function call(StatisticsProviderInterface $provider, $method, $args)
	{
		if ($this->period)
		{
			$this->period->apply($provider);
		}

		if ($this->interval)
		{
			return $this->callInterval($provider, $method, $args);
		}

		return call_user_func_array([$provider, $method], $args);
	}

	/**
	 * @param \LW\Statistics\StatisticsProviderInterface $provider
	 * @param string $method
	 * @param array $args
	 * @return array
	 */
	protected function callInterval(StatisticsProviderInterface $provider, $method, $args)
	{
		$provider->setInterval($this->interval);

		$result = call_user_func_array([$provider, $method], $args);

		return $result;
	}

	/**
	 * @param string $scope
	 * @return array
	 */
	protected function parseScope($scope)
	{
		if (count($parts = explode(':', $scope, 2)) === 2)
		{
			$parts[1] = explode(':', $parts[1]);
		}
		else
		{
			$parts[1] = [];
		}

		return $parts;
	}

	/**
	 * @param array $scopes
	 * @return \LW\Statistics\StatisticsProviderCollection
	 */
	protected function getProviderCollection(array $scopes)
	{
		return $this->providersHelper->providers($scopes);
	}

}