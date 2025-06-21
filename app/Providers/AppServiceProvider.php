<?php

namespace App\Providers;

use App\Events\ConversationStartedEvent;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\ContactRepository;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\IRepository;
use App\Services\Messenger\Contracts\IMessenger;
use App\Services\Messenger\Whatsapp\Messenger;
use Domain\Contact\Events\ConversationStarted;
use Domain\Contact\Repositories\IContactRepository;
use Domain\Contact\Repositories\IMessageRepository;
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

        // Repositories
        $this->bindRepositories();

        // Events
        $this->bindEvents();
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

    private function bindRepositories(): void
    {
        $this->app->bind(IRepository::class, BaseRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IContactRepository::class, ContactRepository::class);
        $this->app->bind(IMessageRepository::class, MessageRepository::class);
    }

    private function bindEvents(): void
    {
        $this->app->singleton('domainEvent.map', function () {
            return [
                ConversationStarted::class => ConversationStartedEvent::class,
            ];
        });
    }
}
