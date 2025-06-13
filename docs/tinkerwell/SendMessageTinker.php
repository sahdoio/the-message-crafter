<?php

use App\UseCases\Contact\SendMessage;

$useCase = app(SendMessage::class);
$useCase->handle("test");
