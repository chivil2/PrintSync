<?php

use App\Models\Quote;

return [
    /*
    |--------------------------------------------------------------------------
    | Sales Report Configuration
    |--------------------------------------------------------------------------
    | Defines data sources, metric calculations, and date grouping
    | for monthly and yearly sales reports.
    |
    */

    'source' => [
        'model' => Quote::class,
        'table' => 'quotes',
    ],

    'status_filter' => ['accepted'],

    'metrics' => [
        'revenue' => [
            'label' => 'Revenue',
            'description' => 'Total revenue from accepted quotes',
            'column' => 'total',
            'aggregate' => 'sum',
            'format' => 'currency',
        ],

        'orders' => [
            'label' => 'Orders',
            'description' => 'Number of accepted quotes',
            'column' => 'id',
            'aggregate' => 'count',
            'format' => 'number',
        ],

        'paid' => [
            'label' => 'Paid',
            'description' => 'Revenue from fully paid quotes',
            'column' => 'total',
            'aggregate' => 'sum',
            'format' => 'currency',
            'conditions' => ['payment_status' => 'paid'],
        ],

        'downpayment' => [
            'label' => 'Down Payment',
            'description' => 'Revenue from partially paid quotes',
            'column' => 'total',
            'aggregate' => 'sum',
            'format' => 'currency',
            'conditions' => ['payment_status' => 'partially_paid'],
        ],

        'unpaid' => [
            'label' => 'Unpaid',
            'description' => 'Revenue from unpaid quotes',
            'column' => 'total',
            'aggregate' => 'sum',
            'format' => 'currency',
            'conditions' => ['payment_status' => 'unpaid'],
        ],
    ],

    'date_column' => 'created_at',

    'currency' => [
        'symbol' => '₱',
        'decimal_places' => 2,
        'locale' => 'en_PH',
    ],
];
