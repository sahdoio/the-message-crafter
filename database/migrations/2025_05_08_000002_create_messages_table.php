<?php

use App\Domain\Contact\Enums\MessageChannel;
use App\Domain\Contact\Enums\MessageStatus;
use App\Domain\Contact\Enums\MessageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');

            $table->string('provider')->nullable(); // e.g. 'flow' or 'ai'

            $table->enum('channel', array_column(MessageChannel::cases(), 'value'))->nullable();
            $table->enum('message_type', array_column(MessageType::cases(), 'value'))->nullable();
            $table->string('image_url')->nullable();

            $table->string('message_id')->nullable();
            $table->json('payload')->nullable();

            $table->enum('status', array_column(MessageStatus::cases(), 'value'))->default(MessageStatus::PENDING->value);

            $table->timestamp('sent_at')->nullable();

            $table->nullableMorphs('related');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
