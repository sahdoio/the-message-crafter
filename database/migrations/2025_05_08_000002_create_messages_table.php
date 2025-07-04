<?php

use Domain\Contact\Enums\MessageChannel;
use Domain\Contact\Enums\MessageProvider;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Enums\MessageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->string('conversation_step')->nullable();

            $table->enum('provider', array_column(MessageProvider::cases(), 'value'))->nullable();
            $table->enum('channel', array_column(MessageChannel::cases(), 'value'))->nullable();
            $table->enum('message_type', array_column(MessageType::cases(), 'value'))->nullable();
            $table->string('image_url')->nullable();

            $table->string('message_id')->nullable();
            $table->json('payload')->nullable();

            $table->enum('status', array_column(MessageStatus::cases(), 'value'))->default(MessageStatus::PENDING->value);

            $table->unsignedBigInteger('selected_button_id')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
