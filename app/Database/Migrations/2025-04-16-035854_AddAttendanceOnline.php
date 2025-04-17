<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttendanceOnline extends Migration
{
    public function up()
    {
        $this->forge->addColumn('schedules', [
            'online' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'no_limit',
            ],
        ]);
        $this->forge->addColumn('attendances', [
            'image' => [
                'type'       => 'CHAR',
                'constraint' => 255,
                'null'       => false,
                'after'      => 'description',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('schedules', 'online');
        $this->forge->dropColumn('attendances', 'image');
    }
}
