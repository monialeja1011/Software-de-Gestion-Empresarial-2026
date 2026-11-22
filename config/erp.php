<?php

return [
    'low_stock_threshold' => (int) env('ERP_LOW_STOCK', 5),
    'daily_summary_at' => env('ERP_DAILY_SUMMARY_AT', '19:00'),
];
