<?php

use Illuminate\Support\Facades\Facade; // Tambahkan ini jika belum ada di atas (untuk 'aliases')
use Illuminate\Support\ServiceProvider; // Tambahkan ini jika belum ada di atas (untuk 'providers')

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    */
    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    */
    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'), // Biasanya 'null' jika driver adalah 'file'
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Laravel Framework Service Providers...
         */
        // Biasanya sudah ada di defaultProviders(), tapi jika diperlukan secara eksplisit:
        // Illuminate\Auth\AuthServiceProvider::class,
        // Illuminate\Broadcasting\BroadcastServiceProvider::class,
        // Illuminate\Bus\BusServiceProvider::class,
        // Illuminate\Cache\CacheServiceProvider::class,
        // Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        // Illuminate\Cookie\CookieServiceProvider::class,
        // Illuminate\Database\DatabaseServiceProvider::class,
        // Illuminate\Encryption\EncryptionServiceProvider::class,
        // Illuminate\Filesystem\FilesystemServiceProvider::class,
        // Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        // Illuminate\Hashing\HashServiceProvider::class,
        // Illuminate\Mail\MailServiceProvider::class,
        // Illuminate\Notifications\NotificationServiceProvider::class,
        // Illuminate\Pagination\PaginationServiceProvider::class,
        // Illuminate\Pipeline\PipelineServiceProvider::class,
        // Illuminate\Queue\QueueServiceProvider::class,
        // Illuminate\Redis\RedisServiceProvider::class,
        // Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        // Illuminate\Session\SessionServiceProvider::class,
        // Illuminate\Translation\TranslationServiceProvider::class,
        // Illuminate\Validation\ValidationServiceProvider::class,
        // Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        // Tambahkan package provider Anda di sini jika ada

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class, // Komentari jika tidak pakai real-time broadcast
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,    // <<< PENTING: Ini untuk route Anda
        App\Providers\Filament\AdminPanelProvider::class, // Anda menggunakan Filament, jadi ini mungkin sudah ada atau perlu ada

        // Jika Anda menggunakan Fortify atau Jetstream, provider mereka akan ada di sini
        // App\Providers\FortifyServiceProvider::class,
        // App\Providers\JetstreamServiceProvider::class,

    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        // Tambahkan alias kustom Anda di sini jika ada
    ])->toArray(),

];