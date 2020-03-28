<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateDwzLogs extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dwz_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("uri")->comment('源地址');
            $table->string("uk")->comment('源地址唯一key');
            $table->string("s4id")->comment('短id');
            $table->bigInteger("ip")->comment('ip');
            $table->timestamp("create_time");
            $table->timestamp("update_time");

            $table->index("uk");
            $table->index("ip");
            $table->index("s4id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("dwz_logs");
    }
}
