<?php

use App\Actions\Contact\SendMessage;

$useCase = app(SendMessage::class);
$useCase->handle("test");
