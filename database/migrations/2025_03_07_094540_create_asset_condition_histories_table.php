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
        Schema::create('asset_condition_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

            // Condition details
            $table->enum('condition', ['good', 'fair', 'poor', 'damaged'])->default('good');
            $table->text('notes')->nullable();

            // Image of the condition (optional)
            $table->string('condition_image_path')->nullable();

            // Who recorded this condition
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');

            // Location or context of the condition assessment
            $table->string('location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_condition_histories');
    }
};
