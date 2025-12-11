<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'title', 'description', 'due_date', 'attachment_path', 'attachment_name', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Get assignments for a specific course
     */
    public function getAssignmentsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get assignment with course information
     */
    public function getAssignmentWithCourse($assignment_id)
    {
        return $this->select('assignments.*, courses.title as course_title, courses.instructor_id')
                    ->join('courses', 'assignments.course_id = courses.id')
                    ->where('assignments.id', $assignment_id)
                    ->first();
    }

    /**
     * Get assignments for enrolled courses (student view)
     */
    public function getAssignmentsForStudent($student_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT a.*, c.title as course_title, c.id as course_id
            FROM assignments a
            INNER JOIN courses c ON a.course_id = c.id
            INNER JOIN enrollments e ON c.id = e.course_id
            WHERE e.user_id = ?
            ORDER BY a.due_date ASC, a.created_at DESC
        ", [$student_id]);
        
        return $query->getResultArray();
    }

    /**
     * Get assignments created by teacher
     */
    public function getAssignmentsByTeacher($teacher_id)
    {
        return $this->select('assignments.*, courses.title as course_title')
                    ->join('courses', 'assignments.course_id = courses.id')
                    ->where('assignments.created_by', $teacher_id)
                    ->orderBy('assignments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all assignments with course and teacher details for admin view
     */
    public function getAllAssignmentsWithDetails()
    {
        return $this->select('assignments.*, courses.title as course_title, users.name as teacher_name')
                    ->join('courses', 'assignments.course_id = courses.id')
                    ->join('users', 'assignments.created_by = users.id')
                    ->orderBy('assignments.created_at', 'DESC')
                    ->findAll();
    }
}
