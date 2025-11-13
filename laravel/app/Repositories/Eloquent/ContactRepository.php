<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Messaging\Entities\Contact;
use Domain\Messaging\Repositories\IContactRepository;

class ContactRepository extends BaseRepository implements IContactRepository
{
    protected string $entityClass = Contact::class;
}
