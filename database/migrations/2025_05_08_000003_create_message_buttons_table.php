<?php

use App\Domain\Contact\Enums\MessageButtonType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageButtonsTable extends Migration
{
    public function up(): void
    {
        Schema::create('message_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('button_id');
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->enum('type', array_column(MessageButtonType::cases(), 'value'))->default(MessageButtonType::TEXT->value);
            $table->string('action');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_buttons');
    }
}
