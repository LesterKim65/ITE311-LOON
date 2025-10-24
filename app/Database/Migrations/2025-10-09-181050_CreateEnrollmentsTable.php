<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('enrollments')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'course_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'enrolled_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'active',
                ],
            ]);

            $this->forge->addKey('id', true); // Primary Key
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('enrollments');
        }
    }

    public function down()
    {
        $this->forge->dropTable('enrollments');
    }
}
