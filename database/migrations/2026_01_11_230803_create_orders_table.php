<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('client_id');
            $table->uuid('created_by');

            $table->enum('status', ['draft', 'confirmed', 'synced'])
                  ->default('draft');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('client_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
