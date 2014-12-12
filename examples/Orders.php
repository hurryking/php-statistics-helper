<?php

use Illuminate\Database\Eloquent\Model;

// Orders model.
class Orders extends Model {

	/**
	 * @var string
	 */
	protected $table = 'orders';

}

// Orders statistics provider.
class OrdersStatisticsProvider extends \LW\Statistics\AbstractEloquentStatisticsProvider {

	/**
	 * @param \Orders $model
	 */
	public function __construct(\Orders $model)
	{
		parent::__construct($model);
	}

}


// Example, in verbose form, a lot can be done by DI.
$providers = new LW\Statistics\ProvidersHelper;
$ranges    = new LW\Statistics\RangeHelper;

$providers->add('orders', 'OrdersStatisticsProvider');

$statistics = new LW\Statistics\StatisticsHelper($providers, $ranges);

$statistics->interval('daily');
$statistics->period('last-month');

$statistics->get(['orders' => 'count']);