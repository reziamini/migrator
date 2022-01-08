<?php

return [
    // Path to access migrator page
    'route'      => 'migrator',

    // Middleware to authorize the admin user
    'middleware' => 'auth',

    /*
    |--------------------------------------------------------------------------
    | Only on local
    |--------------------------------------------------------------------------
    |
    | Flag that preventing showing commands if environment is on production
    |
    */
    'local'      => true,

    /*
    |--------------------------------------------------------------------------
    | Paginate show data per page
    |--------------------------------------------------------------------------
    |
    | Showing data with default paginate per page 10
    |
    */
    'per_page'   => 10,
];
