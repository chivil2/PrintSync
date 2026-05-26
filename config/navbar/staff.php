<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Staff Navbar Configuration
    |--------------------------------------------------------------------------
    | Navigation items for staff/employee/owner navbar
    |
    */

    'logo' => [
        'route' => 'staff.dashboard',
        'icon' => 'fa-solid fa-file-lines',
    ],

    'links' => [
        [
            'label' => 'Dashboard',
            'route' => 'staff.dashboard',
            'icon' => 'fa-solid fa-house',
            'permission' => 'view_assigned_service_jobs',
        ],
        [
            'label' => 'Quotes',
            'route' => 'owner.quotes', // Will be dynamically resolved based on role
            'icon' => 'fa-solid fa-file-lines',
            'permission' => 'view_assigned_service_jobs',
            'role_based_route' => [
                'owner' => 'owner.quotes',
                'employee' => 'employee.quotes',
            ],
        ],
        [
            'label' => 'Jobs',
            'route' => 'owner.jobs', // Will be dynamically resolved based on role
            'icon' => 'fa-solid fa-briefcase',
            'permission' => 'view_assigned_service_jobs',
            'role_based_route' => [
                'owner' => 'owner.jobs',
                'employee' => 'employee.jobs',
            ],
        ],
        [
            'label' => 'Employees',
            'route' => 'owner.employees',
            'icon' => 'fa-solid fa-users',
            'permission' => 'manage_users',
        ],
    ],

    'cart' => [
        'enabled' => false,
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
