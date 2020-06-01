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

use Hyperf\Database\Seeders\Seeder;

class AddAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Model\User::create([
            'name' => 'alonexy',
            'email' => '961610358@qq.com',
            'password' => md5('123321aa'),
            'status' => 1,
            'rid' => 0,
        ]);
    }
}
