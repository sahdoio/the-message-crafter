<?php

use App\Actions\Contact\startConversation;

$useCase = app(startConversation::class);
$useCase->handle("test");
