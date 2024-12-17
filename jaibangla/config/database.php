<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql_sp' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '172.20.60.107'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'sneherporosh'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'sslmode' => 'prefer',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'pgsql2' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'bandhu',
            'sslmode' => 'prefer',
        ],

        'pgsql3' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_doc_server_local'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'jb_doc',
            'sslmode' => 'prefer',
        ],

        'pgsql4' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'manabik',
            'sslmode' => 'prefer',
        ],
        'pgsql5' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'pension',
            'sslmode' => 'prefer',
        ],
        'pgsql6' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'fisherman_oap',
            'sslmode' => 'prefer',
        ],
        'pgsql7' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'prachesta',
            'sslmode' => 'prefer',
        ],

        'pgsql8' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'sbi',
            'sslmode' => 'prefer',
        ],
        'pgsql9' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'msme',
            'sslmode' => 'prefer',
        ],
        'pgsql10' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'weavers',
            'sslmode' => 'prefer',
        ],
        'pgsql_mis' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'sslmode' => 'prefer',
        ],

        'pgsql_legacy' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'legacy',
            'sslmode' => 'prefer',
        ],

        'pgsql_ifsc' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'ifsc',
            'sslmode' => 'prefer',
        ],
        'pgsql_report' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'pension',
            'sslmode' => 'prefer',
        ],
        'pgsql11' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'oap_st_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsql12' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'oap_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsql13' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'wp_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsql14' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'lokprasar_retainer',
            'sslmode' => 'prefer',
        ],
        'pgsql15' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'lokprasar_pensioner',
            'sslmode' => 'prefer',
        ],
        'pgsql16' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'farmer',
            'sslmode' => 'prefer',
        ],
        'pgsqlpurohitmonthly' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'purohit_monthly',
            'sslmode' => 'prefer',
        ],
        'pgsqlpurohithousing' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'purohit_housing',
            'sslmode' => 'prefer',
        ],
        'pgsql20' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'lk_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsqllbtemp' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'lb_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsql_ifms' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_local'),
            'username' => env('DB_USERNAME', 'jaibangla'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'ifms',
            'sslmode' => 'prefer',
        ],
        'pgsqlwp_mis' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'jaibangla_local',
            'username' =>  'postgres',
            'password' => '123',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'wp_wcd',
            'sslmode' => 'prefer',
        ],
        'pgsqloap_mis' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'jaibangla',
            'username' =>  'postgres',
            'password' => 'postgres',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'oap_wcd',
            'sslmode' => 'prefer',
        ],

        'pgsqlmanabik_mis' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'jaibangla',
            'username' =>  'postgres',
            'password' => 'postgres',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'manabik',
            'sslmode' => 'prefer',
        ],
        'pgsql_main_mis' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'jaibangla_doc_server_local'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', '123'),
            'charset' => 'utf8',
           
           
            'sslmode' => 'prefer',
        ],
        'pgsql_encwrite' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'jaibangla_doc_server_local',
            'username' =>  'postgres',
            'password' => '123',
            'charset' => 'utf8',
            'prefix' => '',
            
            'sslmode' => 'prefer',
        ],
        'pgsql_paywrite' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'jb_payment',
            'username' =>  'postgres',
            'password' => '123',
            'charset' => 'utf8',
            'prefix' => '',
            
            'sslmode' => 'prefer',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
