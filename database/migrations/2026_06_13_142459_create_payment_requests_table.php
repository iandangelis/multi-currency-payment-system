<?php

use App\Models\User;
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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->decimal('converted_amount', 15, 2);
            $table->string('currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('exchange_rate', 18, 8);
            $table->string('status');
            $table->foreignIdFor(User::class, 'requester_id')->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
