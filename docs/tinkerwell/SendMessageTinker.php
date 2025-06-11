<?php
use App\Domain\Contact\UseCases\SendMessage;

$useCase = app(SendMessage::class);
$useCase->handle("test");
