<?php

declare(strict_types=1);

namespace Domain\Messaging\Repositories;

use App\Repositories\IRepository;
use Domain\Messaging\Entities\Contact;

/**
 * @extends IRepository<Contact>
 */
interface IContactRepository extends IRepository
{
}

