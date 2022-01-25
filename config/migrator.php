<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Migrator Route
    | Migrator route address to access the module: 'localhost:8000/migrator'
    |--------------------------------------------------------------------------
    */
    'route' => 'migrator',

    /*
    |--------------------------------------------------------------------------
    | Middlewares for Migrator
    | Middlewares which are used to access Migrator route
    |--------------------------------------------------------------------------
    */
    'middleware' => ['auth'],

    /*
    |--------------------------------------------------------------------------
    | Only on local
    | Flag that preventing showing commands if environment is on production
    |--------------------------------------------------------------------------
    */
    'local' => true,

    /*
    |--------------------------------------------------------------------------
    | Paginate show data per page
    | Showing data with default paginate per page 15
    |--------------------------------------------------------------------------
    */
    'per_page' => 15,
];
