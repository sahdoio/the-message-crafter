<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

class EmailSavedSuccessfullyTemplate extends SimpleTextTemplate
{
    public function __construct()
    {
        $this->text = 'Your email has been saved successfully. Thank you for providing your email address! We will use it to keep you updated with important information and notifications.';
    }
}
