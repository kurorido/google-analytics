<?php

namespace RoliChung\GoogleAnalytics;

use Carbon\Carbon;
use Google_Client;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_SegmentDimensionFilter;

class GoogleAnalytics
{
    protected $client;
    protected $service;
    protected $analytics;
    protected $startDate;
    protected $endDate;

    public function __construct($key_path, $VIEW_ID, Carbon $startDate, Carbon $endDate)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($key_path);
        $this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->analytics = new Google_Service_AnalyticsReporting($this->client);
        $this->VIEW_ID = $VIEW_ID;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function report($dimensions)
    {
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($this->startDate->toDateString());
        $dateRange->setEndDate($this->endDate->toDateString());

        // Create the Metrics object.
        $sessions = new Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:sessions");
        $sessions->setAlias("sessions");

        //Create the browser dimension.
        $requestDimensions = [];

        if (is_array($dimensions)) {
            foreach ($dimensions as $dimension) {
                $ga_dimension = new Google_Service_AnalyticsReporting_Dimension();
                $ga_dimension->setName($dimension);
                $requestDimensions[] = $ga_dimension;
            }
        } else {
            $ga_dimension = new Google_Service_AnalyticsReporting_Dimension();
            $ga_dimension->setName($dimensions);
            $requestDimensions[] = $ga_dimension;
        }

        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setDimensions($requestDimensions);
        $request->setMetrics(array($sessions));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        return $this->analytics->reports->batchGet( $body );
    }
}