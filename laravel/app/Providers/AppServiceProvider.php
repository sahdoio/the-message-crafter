<?php

namespace App\Providers;

use App\Events\ConversationFinishedEvent;
use App\Events\ConversationStartedEvent;
use App\Events\MessageReceivedEvent;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\ContactRepository;
use App\Repositories\Eloquent\ConversationRepository;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\IRepository;
use App\Services\Messenger\Contracts\IMessenger;
use App\Services\Messenger\Whatsapp\Messenger;
use App\Support\Events\LaravelDomainEventBus;
use Domain\Membership\Repositories\IUserRepository;
use Domain\Messaging\Events\ConversationFinished;
use Domain\Messaging\Events\ConversationStarted;
use Domain\Messaging\Events\MessageReceived;
use Domain\Messaging\Repositories\IContactRepository;
use Domain\Messaging\Repositories\IConversationRepository;
use Domain\Messaging\Repositories\IMessageRepository;
use Domain\Shared\Events\IDomainEventBus;
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
        $this->app->bind(IConversationRepository::class, ConversationRepository::class);
    }

    private function bindEvents(): void
    {
        $eventMap = [
            ConversationStarted::class => ConversationStartedEvent::class,
            ConversationFinished::class => ConversationFinishedEvent::class,
            MessageReceived::class => MessageReceivedEvent::class,
        ];

        $this->app->bind(IDomainEventBus::class, function () use ($eventMap) {
            return new LaravelDomainEventBus($eventMap);
        });
    }
}
