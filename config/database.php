<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Tương đương "accountConnectionString" trong Web.config cũ - DB chính
    | chứa các bảng tài khoản / dữ liệu game (Account_Info, DaiLyKNB, ...).
    |
    */

    'default' => env('DB_CONNECTION', 'sqlsrv'),

    'connections' => [

        // DB "account" - tương đương accountConnectionString / accountConnectionString1
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'account'),
            'username' => env('DB_USERNAME', 'sa'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'encrypt' => env('DB_ENCRYPT', 'no'),
            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'yes'),
        ],

        // DB "jxm_news" - tương đương jxm_newsConnectionString (tin tức)
        'sqlsrv_news' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_NEWS_URL'),
            'host' => env('DB_NEWS_HOST', env('DB_HOST', 'localhost')),
            'port' => env('DB_NEWS_PORT', env('DB_PORT', '1433')),
            'database' => env('DB_NEWS_DATABASE', 'jxm_news'),
            'username' => env('DB_NEWS_USERNAME', env('DB_USERNAME', 'sa')),
            'password' => env('DB_NEWS_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => env('DB_NEWS_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'encrypt' => env('DB_NEWS_ENCRYPT', 'no'),
            'trust_server_certificate' => env('DB_NEWS_TRUST_SERVER_CERTIFICATE', 'yes'),
        ],

        // SQLite được giữ làm fallback cho cache/session/queue mặc định của Laravel
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE_SQLITE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
