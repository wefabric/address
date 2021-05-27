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
        'api_key' => env('GOOGLE_API_KEY')
    ]
];
