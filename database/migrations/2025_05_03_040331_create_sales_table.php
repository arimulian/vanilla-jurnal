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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_sales')->unique();
            $table->timestamp('date');
            $table->decimal('total_amount', 10, 2);
            $table->integer('discount')->default(0);
            $table->integer('tax')->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->string('status')->default('unpaid');
            $table->string('payment_method')->default('cash');
            $table->foreignId('branch_id')->constrained('branches', 'id')->noActionOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
