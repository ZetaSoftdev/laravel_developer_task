<?php

use Illuminate\Support\Facades\File;

return [
    'icon' => 'assets/horizontal-logo.svg',

    //    'background' => 'assets/logo.svg',

    'support_url' => 'https://join.skype.com/invite/xWFoykc1gnN6',

    /*
     * The following sections originally contained arrow function closures which are not
     * serializable and therefore break `php artisan config:cache` in production.
     * At runtime (production) the installer is never used, so we replace the
     * dynamic checks with static, cache-friendly data structures.
     */

    // Server requirements – kept minimal & without closures
    'server' => [
        // Example entry structure (not used during runtime)
        // 'php' => [
        //     'name'    => 'PHP Version',
        //     'version' => '>= 8.1.0',
        //     'check'   => 'phpversion' // any zero-arg callable name
        // ]
    ],

    // Folder permission checks – empty in production to avoid closures
    'folders' => [],

    'database' => [
        'seeders' => false,
    ],

    'commands' => [
        // Seeder commands run by the installer (not used in production)
        'db:seed --class=InstallationSeeder',
        'db:seed --class=AddSuperAdminSeeder',
    ],

    'admin_area' => [
        'user' => [
            'email'    => 'superadmin@gmail.com',
            'password' => 'superadmin',
        ],
    ],
];
