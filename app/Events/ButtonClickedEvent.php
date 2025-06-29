<?php

declare(strict_types=1);

namespace App\Events;

use Domain\Contact\Events\ButtonClicked;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ButtonClickedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ButtonClicked $buttonClicked) {}
}
