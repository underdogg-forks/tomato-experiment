<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('loginBy')->default('email'); // AccountType::EMAIL
            $table->string('address')->nullable();
            $table->string('lang')->default('en');
            $table->string('password');
            $table->string('type')->default('user'); // AccountType::USER
            $table->boolean('is_active')->default(true);
            $table->boolean('is_login')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
