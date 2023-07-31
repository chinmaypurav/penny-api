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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('creditor_id')->constrained('accounts');
            $table->foreignId('debtor_id')->constrained('accounts');
            $table->decimal('amount', 16);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('transacted_at')->nullable();
            $table->boolean('completed')->default(false);
            $table->string('description')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
