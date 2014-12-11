<?php namespace LW\Statistics;

use Illuminate\Container\Container;

class ProvidersHelper {

	/**
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $providerMap = [];

	/**
	 * @param \Illuminate\Container\Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param string $key
	 * @param string $provider
	 * @return static
	 */
	public function add($key, $provider)
	{
		$this->providerMap[$key] = $provider;

		return $this;
	}

	/**
	 * @return array
	 */
	public function available()
	{
		return array_keys($this->providerMap);
	}

	/**
	 * @param array $providers
	 * @return \LW\Statistics\StatisticsProviderCollection
	 */
	public function providers(array $providers)
	{
		foreach ($providers as $key => $provider)
		{
			if (! isset($this->providerMap[$provider]))
			{
				unset($providers[$key]);
			}
		}

		return $this->makeProvidersCollection($providers);
	}

	/**
	 * @param array $keys
	 * @return \LW\Statistics\StatisticsProviderCollection
	 */
	protected function makeProvidersCollection($keys)
	{
		$collection = new StatisticsProviderCollection;

		foreach ($keys as $name)
		{
			$collection->add($name, $this->container->make($this->providerMap[$name]));
		}

		return $collection;
	}

}