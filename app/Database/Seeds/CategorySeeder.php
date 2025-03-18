<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'          => '1',
                'name'        => 'Admin',
                'description' => 'Admin memiliki akses ke semua fitur dalam sistem.',
            ],
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
