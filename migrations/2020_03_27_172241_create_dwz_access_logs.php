<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateDwzAccessLogs extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dwz_access_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("s4id")->comment('短id');
            $table->bigInteger("ip")->comment('ip');
            $table->string("user_agent")->comment('user_agent');

            $table->timestamp("create_time");
            $table->timestamp("update_time");

            $table->index("s4id");
            $table->index("ip");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwz_access_logs');
    }
}
