<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSubmissionsTableForAssignments extends Migration
{
    public function up()
    {
        // Make quiz_id nullable (since assignments won't have quiz_id)
        $this->forge->modifyColumn('submissions', [
            'quiz_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Add assignment_id field
        $this->forge->addColumn('submissions', [
            'assignment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'quiz_id',
            ],
        ]);

        // Add submission type field to distinguish between quiz and assignment
        $this->forge->addColumn('submissions', [
            'submission_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'quiz',
                'after'      => 'assignment_id',
            ],
        ]);

        // Add file submission fields
        $this->forge->addColumn('submissions', [
            'submission_file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'submission_type',
            ],
            'submission_file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'submission_file_path',
            ],
            'submission_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'submission_file_name',
            ],
        ]);

        // Rename score to grade and make it decimal for assignments
        $this->forge->modifyColumn('submissions', [
            'score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Add status field
        $this->forge->addColumn('submissions', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'submitted',
                'after'      => 'submission_notes',
            ],
        ]);

        // Add feedback field
        $this->forge->addColumn('submissions', [
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ],
        ]);

        // Add submitted_at and graded_at fields
        $this->forge->addColumn('submissions', [
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'feedback',
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'submitted_at',
            ],
        ]);

        // Add foreign key for assignment_id
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'CASCADE', 'CASCADE');
        
        // Add index for assignment_id and user_id combination
        $this->forge->addKey(['assignment_id', 'user_id']);
    }

    public function down()
    {
        // Remove foreign key
        $this->forge->dropForeignKey('submissions', 'submissions_assignment_id_foreign');
        
        // Remove added columns
        $this->forge->dropColumn('submissions', ['assignment_id', 'submission_type', 'submission_file_path', 
            'submission_file_name', 'submission_notes', 'status', 'feedback', 'submitted_at', 'graded_at']);
        
        // Revert score to INT
        $this->forge->modifyColumn('submissions', [
            'score' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
        ]);
        
        // Revert quiz_id to NOT NULL
        $this->forge->modifyColumn('submissions', [
            'quiz_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}


