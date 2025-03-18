<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'          => 'CHAR',
                'constraint'    => 20,
            ],
            'name' => [
                'type'          => 'VARCHAR',
                'constraint'    => '100'
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'pengawas_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'overseer' => [
                'type'          => 'TINYINT',
                'constraint'    => 1,
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
        $this->forge->addKey('id',true);
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->droptable('categories');
    }
}
