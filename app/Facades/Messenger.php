<?php

declare(strict_types=1);

namespace App\Facades;

use App\Domain\Contact\DTOs\SendMessageInputDTO;
use App\Domain\Contact\VOs\MessageBody;
use App\Services\Messenger\Contracts\IMessenger;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(MessageBody|SendMessageInputDTO $data)
 */
class Messenger extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return IMessenger::class;
    }
}

