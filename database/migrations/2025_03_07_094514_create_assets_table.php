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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique(); // Unique identifier for the asset
            $table->string('name');
            $table->text('description')->nullable();

            // Purchase details
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->integer('expected_lifetime_months')->nullable();

            // Current status
            $table->enum('current_condition', ['good', 'fair', 'poor', 'damaged'])->default('good');
            $table->boolean('is_active')->default(true);

            // Relationships
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who last updated

            // Tracking
            $table->timestamp('last_scanned_at')->nullable();
            $table->text('notes')->nullable();

            // QR Code and Image storage
            $table->string('qr_code_path')->nullable();
            $table->string('primary_image_path')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Allow soft deleting assets
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
