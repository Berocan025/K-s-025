<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Content Sections Migration
 * Developer: BERAT K
 * Purpose: Create content ordering system for homepage sections
 */
class CreateContentSectionsTable extends Migration
{
    /**
     * Run the migrations.
     * Developer: BERAT K
     */
    public function up()
    {
        // Create content_sections table for homepage ordering - Developer: BERAT K
        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // platform_services, platforms, premium_products, etc.
            $table->string('section_name'); // Display name for admin panel
            $table->string('section_title'); // Title shown on frontend
            $table->text('section_description')->nullable(); // Description for admin
            $table->integer('sort_order')->default(0); // Order position
            $table->boolean('is_active')->default(true); // Enable/disable section
            $table->boolean('is_visible')->default(true); // Show/hide on frontend
            $table->json('section_settings')->nullable(); // Additional settings like styling, etc.
            $table->string('template_file')->nullable(); // Template file name if custom
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['sort_order', 'is_active', 'is_visible']);
            $table->index('section_key');
        });

        // Create content_items table for items within sections - Developer: BERAT K
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('content_sections')->onDelete('cascade');
            $table->string('item_title');
            $table->text('item_description')->nullable();
            $table->string('item_image')->nullable(); // Image path
            $table->string('item_link')->nullable(); // Link URL
            $table->string('item_button_text')->nullable(); // Button text
            $table->integer('item_order')->default(0); // Order within section
            $table->boolean('is_active')->default(true);
            $table->json('item_meta')->nullable(); // Additional metadata
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['section_id', 'item_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * Developer: BERAT K
     */
    public function down()
    {
        // Drop in reverse order to avoid foreign key constraints - Developer: BERAT K
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_sections');
    }
}