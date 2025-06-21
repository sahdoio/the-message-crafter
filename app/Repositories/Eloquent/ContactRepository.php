<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Contact\Entities\Contact;
use Domain\Contact\Repositories\IContactRepository;

class ContactRepository extends BaseRepository implements IContactRepository
{
    protected string $entityClass = Contact::class;
}
