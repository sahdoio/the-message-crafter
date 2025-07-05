<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

class AskEmailTemplate extends SimpleTextTemplate
{
    public function __construct()
    {
        $this->text = 'Please provide your email address so we can process your request further. Example: jon.doe@test.com';
    }
}
