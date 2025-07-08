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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['payment', 'receipt']); // payment = to pay, receipt = to get
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'partial', 'completed'])->default('pending');
            $table->foreignId('from_person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('to_person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('parent_transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
