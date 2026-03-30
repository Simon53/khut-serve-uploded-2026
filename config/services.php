<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    
 

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
   'khut' => [
        'secret' => env('KHUT_API_SECRET'),
        'catalog_url' => env('KHUT_CATALOG_URL', 'https://khut.bdsoft.us/api/get_all_items.php'),
        'sale_update_url' => env('KHUT_SALE_UPDATE_URL', 'https://khut.bdsoft.us/api/sale_update.php'),
        'sale_update_api_key' => env('KHUT_API_KEY', env('KHUT_SALE_UPDATE_API_KEY')),
        'cancel_order_url' => env('KHUT_CANCEL_ORDER_URL', 'https://khut.bdsoft.us/api/cancel_order.php'),
        'cancel_order_api_key' => env('KHUT_API_KEY', env('KHUT_CANCEL_ORDER_API_KEY', env('KHUT_SALE_UPDATE_API_KEY'))),
    ],
    

];
