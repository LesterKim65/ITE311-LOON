<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at', 'status'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function enrollUser($data)
    {
        $data['enrolled_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending';
        return $this->insert($data);
    }

    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'enrollments.course_id = courses.id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'active') // Only show active enrollments
                    ->findAll();
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->first() !== null;
    }

    public function getPendingEnrollmentsByCourse($course_id)
    {
        return $this->select('enrollments.*, users.name, users.email')
                    ->join('users', 'enrollments.user_id = users.id')
                    ->where('enrollments.course_id', $course_id)
                    ->where('enrollments.status', 'pending')
                    ->findAll();
    }

    public function approveEnrollment($enrollment_id)
    {
        return $this->update($enrollment_id, ['status' => 'active']);
    }

    public function getEnrollmentsByCourse($course_id)
    {
        return $this->select('enrollments.id as enrollment_id, enrollments.user_id as id, enrollments.course_id, enrollments.enrolled_at, enrollments.status, users.name, users.email, users.program, users.year_level, users.section')
                    ->join('users', 'enrollments.user_id = users.id')
                    ->where('enrollments.course_id', $course_id)
                    ->whereIn('enrollments.status', ['active', 'pending'])
                    ->findAll();
    }
}
