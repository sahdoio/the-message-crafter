<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

class FinishConversationTemplate extends SimpleTextTemplate
{
    public function __construct()
    {
        $this->text = 'Thank you for your participation! If you have any questions, feel free to reach out.';
    }
}
