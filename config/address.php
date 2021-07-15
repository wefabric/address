<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model to use, this makes it possible to implement a custom Address model
    |--------------------------------------------------------------------------
    */
    'model_class' => \Wefabric\Address\Models\Address::class,

    /*
    |--------------------------------------------------------------------------
    | For retrieving Streetview images we use the Google API
    |--------------------------------------------------------------------------
    */
    'google' => [
        'street_view_active' => env('GOOGLE_STREETVIEW_ACTIVE', false),
        'street_view_cache_path' => 'streetview/tmp',
        'api_key' => env('GOOGLE_API_KEY')
    ]
    
    /*
    |--------------------------------------------------------------------------
    | The Postcode API Provider to use with https://github.com/nickurt/laravel-postcodeapi
    |--------------------------------------------------------------------------
    */
    'postcode_api_provider' => env('POSTCODE_API_PROVIDER', 'Pro6PP_NL')
];
