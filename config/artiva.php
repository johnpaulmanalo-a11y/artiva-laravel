<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Every price in Artiva is rendered through the money() helper, which reads
    | these values. Change them in .env to switch the storefront currency.
    |
    */

    'currency' => env('APP_CURRENCY', 'USD'),

    'currency_symbol' => env('APP_CURRENCY_SYMBOL', '$'),

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    |
    | Artiva ships with a clearly labelled simulated payment gateway so the
    | project can be demonstrated without live payment credentials. A flat tax
    | rate of 0 keeps totals easy to explain during a demo.
    |
    */

    'tax_rate' => (float) env('ARTIVA_TAX_RATE', 0),

    'payment_gateway' => env('ARTIVA_PAYMENT_GATEWAY', 'simulated'),

];
