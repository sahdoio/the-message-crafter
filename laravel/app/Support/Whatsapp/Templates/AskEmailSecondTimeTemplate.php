<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

class AskEmailSecondTimeTemplate extends SimpleTextTemplate
{
    public function __construct()
    {
        $this->text = 'The email you provided is invalid. Please provide a valid email address so we can process your request further. Example: jon.doe@test.com';
    }
}
