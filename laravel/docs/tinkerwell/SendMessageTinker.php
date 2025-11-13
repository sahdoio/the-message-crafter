<?php

use App\Actions\Messaging\startConversation;

$useCase = app(startConversation::class);
$useCase->handle("test");
