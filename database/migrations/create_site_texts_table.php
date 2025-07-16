<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Site Texts Migration
 * Developer: BERAT K
 * Purpose: Create comprehensive text management system for all site content
 */
class CreateSiteTextsTable extends Migration
{
    /**
     * Run the migrations.
     * Developer: BERAT K
     */
    public function up()
    {
        // Create text_categories table for organizing texts - Developer: BERAT K
        Schema::create('text_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_key')->unique(); // homepage, about, contact, services, etc.
            $table->string('category_name'); // Display name for admin
            $table->text('category_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['category_key', 'is_active']);
        });

        // Create site_texts table for all editable texts - Developer: BERAT K
        Schema::create('site_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('text_categories')->onDelete('cascade');
            $table->string('text_key')->unique(); // Unique identifier for each text
            $table->string('text_label'); // Human readable label for admin
            $table->text('text_value'); // The actual text content
            $table->text('default_value')->nullable(); // Default/fallback value
            $table->string('text_type')->default('text'); // text, textarea, html, email, url, etc.
            $table->string('page_location')->nullable(); // Which page/section this text appears
            $table->text('admin_notes')->nullable(); // Notes for admin users
            $table->boolean('is_required')->default(false); // Cannot be empty
            $table->boolean('is_html')->default(false); // Contains HTML markup
            $table->boolean('is_active')->default(true);
            $table->integer('max_length')->nullable(); // Maximum character limit
            $table->json('text_settings')->nullable(); // Additional settings
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['text_key', 'is_active']);
            $table->index(['category_id', 'page_location']);
            $table->index('text_type');
        });

        // Create text_translations table for multi-language support - Developer: BERAT K
        Schema::create('text_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_text_id')->constrained('site_texts')->onDelete('cascade');
            $table->string('language_code', 5)->default('tr'); // tr, en, etc.
            $table->text('translated_value');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_updated')->nullable();
            $table->string('updated_by')->nullable(); // Who updated this translation
            $table->timestamps();
            
            // Ensure unique combinations - Developer: BERAT K
            $table->unique(['site_text_id', 'language_code']);
            $table->index(['language_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * Developer: BERAT K
     */
    public function down()
    {
        // Drop in reverse order to avoid foreign key constraints - Developer: BERAT K
        Schema::dropIfExists('text_translations');
        Schema::dropIfExists('site_texts');
        Schema::dropIfExists('text_categories');
    }
}