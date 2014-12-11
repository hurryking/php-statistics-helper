<?php namespace LW\Statistics;

use Illuminate\Support\Collection;

class StatisticsProviderCollection {

	/**
	 * @param \Illuminate\Support\Collection
	 */
	protected $providers;

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$this->providers = new Collection;
	}

	/**
	 * @param $name
	 * @param \LW\Statistics\StatisticsProviderInterface $provider
	 * @return static
	 */
	public function add($name, StatisticsProviderInterface $provider)
	{
		$this->providers[$name] = $provider;

		return $this;
	}

	/**
	 * Execute a callback over each item.
	 *
	 * @param  \Closure  $callback
	 * @return $this
	 */
	public function each(\Closure $callback)
	{
		$this->providers->each($callback);

		return $this;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function get($name)
	{
		return $this->providers[$name];
	}

	// Magic methods.

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->providers[$name];
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed Returns provided $value
	 * @throws \InvalidArgumentException
	 */
	public function __set($name, $value)
	{
		if ($value instanceof StatisticsProviderInterface)
		{
			$this->add($name, $value);

			return $value;
		}

		throw new \InvalidArgumentException;
	}

}