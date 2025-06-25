<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\HandleButtonClicked;
use App\Exceptions\ResourceNotFoundException;
use Domain\Contact\Events\ButtonClicked;

class ButtonClickedListener
{
    public function __construct(
        protected HandleButtonClicked $action
    ) {}

    public function handle(ButtonClicked $event): void
    {
        $this->action->handle();
    }
}
