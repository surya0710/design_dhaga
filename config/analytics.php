<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (Data API — free tier)
    |--------------------------------------------------------------------------
    |
    | Measurement ID (G-XXXX) is used in the frontend gtag snippet.
    | Property ID is the numeric ID from GA4 Admin → Property Settings.
    | Credentials: path to a service account JSON with Viewer access on the property.
    |
    */

    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID', 'G-PLEQEJBY8K'),

    'ga4_property_id' => env('GA4_PROPERTY_ID'),

    /*
    | Relative paths are resolved from the project root (e.g. storage/app/file.json).
    */
    'ga4_credentials' => env('GA4_CREDENTIALS')
        ? (preg_match('#^([A-Za-z]:[\\\\/]|/)#', env('GA4_CREDENTIALS'))
            ? env('GA4_CREDENTIALS')
            : base_path(env('GA4_CREDENTIALS')))
        : storage_path('app/google-analytics-credentials.json'),

    'ga4_cache_minutes' => (int) env('GA4_CACHE_MINUTES', 30),

];
