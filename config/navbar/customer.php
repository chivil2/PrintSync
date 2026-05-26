<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Navbar Configuration
    |--------------------------------------------------------------------------
    | Navigation items for customer-facing navbar
    |
    */

    'logo' => [
        'route' => 'customer.store',
        'icon' => 'fa-solid fa-file-lines',
    ],

    'links' => [
        [
            'label' => 'Dashboard',
            'route' => 'customer.dashboard',
            'icon' => 'fa-solid fa-house',
            'permission' => 'place_orders',
        ],
        [
            'label' => 'Store',
            'route' => 'customer.store',
            'icon' => 'fa-solid fa-bag-shopping',
            'permission' => 'place_orders',
        ],
        [
            'label' => 'View Orders',
            'route' => 'customer.orders',
            'icon' => 'fa-solid fa-clipboard-list',
            'permission' => 'place_orders',
        ],
    ],

    'cart' => [
        'enabled' => true,
        'icon' => 'shopping-cart',
    ],

    'user_dropdown' => [
        'items' => [
            [
                'label' => 'Profile Settings',
                'route' => 'customer.profile',
                'permission' => 'view_own_profile',
            ],
        ],
    ],
];
