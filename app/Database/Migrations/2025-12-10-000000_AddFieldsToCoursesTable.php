<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'course_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'after'      => 'id',
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'after'      => 'title',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st', '2nd', 'Summer'],
                'after'      => 'school_year',
            ],
            'schedule' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'description',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'after'      => 'instructor_id',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'status',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['course_code', 'school_year', 'semester', 'schedule', 'status', 'start_date', 'end_date']);
    }
}
