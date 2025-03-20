<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IoTDeviceSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'device_name' => 'Device 1',
                'device_token' => 'jgk0advefk90gj4ngin4290'
            ]
        ];

        $this->db->table('iot_devices')->insertBatch($data);
    }
}
