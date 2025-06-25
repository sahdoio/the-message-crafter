<?php

declare(strict_types=1);

namespace App\Actions\Contact\Pipes;

use App\DTOs\MessageFlowInputDTO;
use App\Facades\Messenger;
use App\Facades\Repository;
use Closure;
use Domain\Contact\Entities\Contact;

class AskCourse
{
    public function handle(MessageFlowInputDTO $data, Closure $next)
    {
        /** @var Contact $contact */
        $contant = Repository::for(Contact::class)->findOne([
            'phone' => $data->contactPhone,
        ]);

        if (!$contact->hasName()) {
            Messenger::sendText($contact->phone, "Hi! What's your name?");
            return null; // stops pipeline
        }

        return $next($data); // pass to next step
    }
}
