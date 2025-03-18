<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'          => '1',
                'name'        => 'Admin Enuma',
                'username'    => 'adminenuma',
                'password'    => password_hash('12345678', PASSWORD_DEFAULT),
                'category_id' => '1',
                'can_login'   => '1',
                'active'      => '1',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
