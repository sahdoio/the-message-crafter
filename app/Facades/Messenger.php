<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\Messenger\Contracts\IMessenger;
use Domain\Contact\Entities\Message;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(Message $message)
 */
class Messenger extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return IMessenger::class;
    }
}

