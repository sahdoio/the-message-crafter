<?php

namespace App\Providers;

use App\Repositories\Eloquent\EloRepository;
use App\Repositories\Eloquent\UserEloRepository;
use App\Repositories\IRepository;
use App\Services\Messenger\Contracts\IMessenger;
use App\Services\Messenger\Whatsapp\Messenger;
use Domain\User\Repositories\IUserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IMessenger::class, Messenger::class);
        $this->app->bind(IRepository::class, EloRepository::class);
        $this->app->bind(IUserRepository::class, UserEloRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('whatsapp', function () {
            return Http::withToken(config('whatsapp.access_token'))
                ->baseUrl(config('whatsapp.base_url') . '/' . config('whatsapp.phone_number_id'));
        });
    }
}
