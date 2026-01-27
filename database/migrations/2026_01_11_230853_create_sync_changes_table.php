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
      Schema::create('sync_changes', function (Blueprint $table) {
    $table->bigIncrements('id'); // cursor
    $table->string('entity_type');
    $table->uuid('entity_id');
    $table->enum('operation', ['create', 'update', 'delete']);
    $table->json('payload');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_changes');
    }
};
