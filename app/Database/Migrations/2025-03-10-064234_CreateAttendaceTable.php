<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendaceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'contraint'         => '11',
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Hadir', 'Ijin', 'Hadir (Online)'],
                'default'    => 'Hadir',
            ],
            'no_limit' => [
                'type'          => 'TINYINT',
                'constraint'    => 1,
                'unsigned'      => true,
            ],
            'checkin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'checkout' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'activity' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendances');
    }

    public function down()
    {
        $this->forge->dropTable('attendances');
    }
}
