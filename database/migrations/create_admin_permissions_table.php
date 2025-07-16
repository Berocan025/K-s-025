<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin Permissions Migration
 * Developer: BERAT K
 * Purpose: Create admin permissions system for granular access control
 */
class CreateAdminPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     * Developer: BERAT K
     */
    public function up()
    {
        // Create admin_roles table - Developer: BERAT K
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['name', 'is_active']);
        });

        // Create admin_permissions table - Developer: BERAT K
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('category')->default('general'); // text_management, content_ordering, user_management, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance - Developer: BERAT K
            $table->index(['category', 'is_active']);
        });

        // Create role_permissions pivot table - Developer: BERAT K
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('admin_permissions')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combinations - Developer: BERAT K
            $table->unique(['role_id', 'permission_id']);
        });

        // Add role_id to users table - Developer: BERAT K
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('admin_role_id')->nullable()->constrained('admin_roles')->onDelete('set null');
            $table->boolean('is_admin')->default(false);
            $table->timestamp('last_admin_login')->nullable();
            
            // Add index for admin users - Developer: BERAT K
            $table->index(['is_admin', 'admin_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     * Developer: BERAT K
     */
    public function down()
    {
        // Drop in reverse order to avoid foreign key constraints - Developer: BERAT K
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_role_id']);
            $table->dropColumn(['admin_role_id', 'is_admin', 'last_admin_login']);
        });
        
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
    }
}