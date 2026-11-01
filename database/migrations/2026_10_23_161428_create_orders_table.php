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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending')->index();
            $table->string('payment_method')->default('cash_on_delivery');
            $table->string('payment_status')->default('pending');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('phone', 30);
            $table->string('address_line');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code', 20);
            $table->string('country');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
