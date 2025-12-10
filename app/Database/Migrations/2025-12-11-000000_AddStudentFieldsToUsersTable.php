<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentFieldsToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'role'
            ],
            'year_level' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'after' => 'program'
            ],
            'section' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'year_level'
            ]
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['program', 'year_level', 'section']);
    }
}
