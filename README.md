# Statistics Builder

Easy to use basic statistics building.

### Intervals

The result set can be split into intervals. These intervals are listed below.

- `hourly`
- `daily`
- `monthly`
- `yearly`

Setting the interval for the result set.
```
$statistics = new LW\Statistics\StatisticsHelper;
$statistics->interval('hourly');
```

### Periods

- `yesterday` - Filter yesterday.
- `d:YYYY-MM-DD` - Filter to a specific day, 00:00:01 to 23:59:59.
- `m:YYYY-MM` - Filter to a specific month from start to end.
- `y:YYYY` - Filter to a specific year from start to end.
- `YYYY-MM-DD` - Filter to a manual date.
- `YYYY-MM-DD:YYYY-MM-DD` - Filter to a manual start and end date.

Setting the period filter for the result set.
```
$statistics = new LW\Statistics\StatisticsHelper;
$statistics->period('yesterday');
```