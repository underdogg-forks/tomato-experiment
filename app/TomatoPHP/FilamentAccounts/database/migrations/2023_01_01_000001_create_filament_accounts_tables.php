<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('accounts_meta', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('account_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        /*Schema::create('teams', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->string('name');
            $table->timestamps();
        });*/

        Schema::create('team_invitations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('team_id');
            $table->string('email');
            $table->string('token');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('team_memberships', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('account_id');
            $table->string('role')->nullable();
            $table->timestamps();
        });

        Schema::create('account_tenant', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('tenant_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_tenant');
        Schema::dropIfExists('team_memberships');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('account_requests');
        Schema::dropIfExists('accounts_meta');
    }
};
