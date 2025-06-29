<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\HandleButtonClicked;
use App\Events\ButtonClickedEvent;

class ButtonClickedListener
{
    public function __construct(
        protected HandleButtonClicked $action
    ) {}

    public function handle(ButtonClickedEvent $event): void
    {
        $this->action->handle($event->buttonClicked);
    }
}
