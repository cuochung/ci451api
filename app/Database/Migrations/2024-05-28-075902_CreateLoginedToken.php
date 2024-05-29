<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginedToken extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'personnel_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'created_at timestamp default current_timestamp'
        ]);
        $this->forge->createTable('logined_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('logined_tokens');
    }
}
