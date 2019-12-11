<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;

class AddAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\User::create([
                              'name' => 'alonexy',
                              'email' => '961610358@qq.com',
                              'password' => md5('123321aa'),
                              'status'=>1,
                              'rid'=>0,
                          ]);
    }
}
