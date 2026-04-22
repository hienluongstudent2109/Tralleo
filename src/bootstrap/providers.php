<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;

return [
    ArtisanServiceProvider::class,
    CacheServiceProvider::class,
    CookieServiceProvider::class,
    DatabaseServiceProvider::class,
    FilesystemServiceProvider::class,
    FormRequestServiceProvider::class,
    PaginationServiceProvider::class,
    RoutingServiceProvider::class,
    SessionServiceProvider::class,
    ValidationServiceProvider::class,
    ViewServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    AppServiceProvider::class,
];
