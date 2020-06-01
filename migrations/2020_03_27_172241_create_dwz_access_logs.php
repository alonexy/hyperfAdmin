<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateDwzAccessLogs extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dwz_access_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('s4id')->comment('短id');
            $table->bigInteger('ip')->comment('ip');
            $table->string('user_agent')->comment('user_agent');

            $table->timestamp('create_time');
            $table->timestamp('update_time');

            $table->index('s4id');
            $table->index('ip');
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
