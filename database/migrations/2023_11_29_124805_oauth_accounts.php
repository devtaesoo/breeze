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
        //
        // Schema::create('oauth_accounts', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('user_id')->unsigned()->nullable()->unique();
        //     $table->bigInteger('provider_id')->unsigned()->nullable()->unique();
        //     $table->bigInteger('provider_user_id')->unsigned()->nullable()->unique();   //oauth 제공 user_id
        //     $table->text('access_token')->unique();;
        //     $table->text('refresh_token')->unique();;
        //     $table->timestamp('expires_at');
        //     $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        //     $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // Schema::dropIfExists('oauth_accounts');
    }
};
