<?php

declare(strict_types=1);

namespace Domain\Shared\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SkipPersistence
{

}
