<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstructorIdToMaterialsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'instructor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'file_path',
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('instructor_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->forge->dropForeignKey('materials', 'materials_instructor_id_foreign');

        // Drop the column
        $this->forge->dropColumn('materials', 'instructor_id');
    }
}
