<?php

use Illuminate\Support\Str;

return [
    'merchant_id' => env('PAYBOX_MERCHANT_ID'),

    'secret_key' => env('PAYBOX_SECRET_KEY'),

    'currency' => 'KGS',

    'result_url' => 'paybox/result',

    'success_url' => null,

    'failure_url' => null,

    'url_method' => 'POST',

    'success_url_method' => null,

    'failure_url_method' => null,

    'testing_mode' => ! app()->isProduction(),

    'salt' => Str::random(20)
];
