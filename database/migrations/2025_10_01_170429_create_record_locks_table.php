<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('record_locks', function (Blueprint $table) {
            $table->id();
            $table->string('lockable_type'); // z. B. App\Models\Post
            $table->unsignedBigInteger('lockable_id'); // z. B. Post-ID
            $table->unsignedBigInteger('user_id'); // wer sperrt
            $table->timestamp('locked_at')->default(now());
            $table->timestamps();
            $table->unique(['lockable_type', 'lockable_id']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_locks');
    }
};
