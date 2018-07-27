<?php

require __DIR__.'/../vendor/autoload.php';

use RoliChung\GoogleAnalytics\GoogleAnalytics;

$keyPath = __DIR__ . '/../key.json';
$startDate = (new \Carbon\Carbon('first day of last month'))->startOfDay();
$endDate = (new \Carbon\Carbon('last day of last month'))->endOfDay();

$analytics = new GoogleAnalytics($keyPath, 'ga:178864965', $startDate, $endDate);
$report = $analytics->report("ga:channelGrouping");

print_r($report);
