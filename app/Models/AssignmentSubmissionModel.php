<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'quiz_id', 'assignment_id', 'submission_type', 'submission_file_path', 
        'submission_file_name', 'submission_notes', 'status', 'score', 'feedback', 
        'submitted_at', 'graded_at', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Get submission for a student and assignment
     */
    public function getSubmission($assignment_id, $student_id)
    {
        return $this->where('assignment_id', $assignment_id)
                    ->where('user_id', $student_id)
                    ->where('submission_type', 'assignment')
                    ->first();
    }

    /**
     * Get all submissions for an assignment
     */
    public function getSubmissionsByAssignment($assignment_id)
    {
        return $this->select('submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'submissions.user_id = users.id')
                    ->where('submissions.assignment_id', $assignment_id)
                    ->where('submissions.submission_type', 'assignment')
                    ->orderBy('submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all submissions for a student
     */
    public function getSubmissionsByStudent($student_id)
    {
        return $this->select('submissions.*, assignments.title as assignment_title, assignments.due_date, courses.title as course_title')
                    ->join('assignments', 'submissions.assignment_id = assignments.id')
                    ->join('courses', 'assignments.course_id = courses.id')
                    ->where('submissions.user_id', $student_id)
                    ->where('submissions.submission_type', 'assignment')
                    ->orderBy('submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get students enrolled in course who haven't submitted
     */
    public function getNonSubmittingStudents($assignment_id, $course_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT u.id, u.name, u.email
            FROM enrollments e
            INNER JOIN users u ON e.user_id = u.id
            WHERE e.course_id = ?
            AND u.role = 'student'
            AND u.id NOT IN (
                SELECT user_id 
                FROM submissions 
                WHERE assignment_id = ? AND submission_type = 'assignment'
            )
            ORDER BY u.name ASC
        ", [$course_id, $assignment_id]);
        
        return $query->getResultArray();
    }

    /**
     * Update grade and feedback
     */
    public function gradeSubmission($submission_id, $grade, $feedback)
    {
        return $this->update($submission_id, [
            'score' => $grade,
            'feedback' => $feedback,
            'status' => 'graded',
            'graded_at' => date('Y-m-d H:i:s')
        ]);
    }
}

