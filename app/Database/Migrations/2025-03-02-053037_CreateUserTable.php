<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'CHAR',
                'constraint'     => 36,
            ],
            'id_fingerprint' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'category_id' => [
                'type'       => 'CHAR',
                'constraint' => 20,
            ],
            'can_login' => [
                'type'       => 'INT',
                'constraint' => 1,
                'unsigned'   => true,      
            ],
            'active' => [
                'type'       => 'INT',
                'constraint' => 1,
                'unsigned'   => true,  
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
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
