<?php

declare(strict_types=1);

namespace Domain\Messaging\Exceptions;

use DomainException;

class ConversationAlreadyStartedException extends DomainException {}
