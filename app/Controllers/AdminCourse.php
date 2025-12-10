<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;

class AdminCourse extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        $userModel = new UserModel();

        // Get total courses and active courses
        $totalCourses = $courseModel->countAll();
        $activeCourses = $courseModel->where('status', 'active')->countAllResults();

        // Get courses with instructor names
        $courses = $courseModel->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.instructor_id')
            ->orderBy('courses.title', 'ASC')
            ->findAll();

        // Get teachers for dropdown
        $teachers = $userModel->where('role', 'teacher')->findAll();

        return view('admin/courses/index', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'teachers' => $teachers,
            'searchTerm' => ''
        ]);
    }

    public function search()
    {
        $searchTerm = $this->request->getGet('search_term');
        if ($searchTerm === null) {
            $searchTerm = $this->request->getPost('search_term');
        }

        $courseModel = new CourseModel();
        $userModel = new UserModel();

        // Get total courses and active courses
        $totalCourses = $courseModel->countAll();
        $activeCourses = $courseModel->where('status', 'active')->countAllResults();

        // Apply search filter
        $courseModel->applySearchFilter($searchTerm)
            ->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.instructor_id')
            ->orderBy('courses.title', 'ASC');
        $courses = $courseModel->findAll();

        // Get teachers for dropdown
        $teachers = $userModel->where('role', 'teacher')->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        return view('admin/courses/index', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'teachers' => $teachers,
            'searchTerm' => $searchTerm
        ]);
    }

    public function getCourse($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        return $this->response->setJSON(['success' => true, 'course' => $course]);
    }

    public function update($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        $data = $this->request->getPost();

        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);

            if ($startDate >= $endDate) {
                return $this->response->setJSON(['success' => false, 'message' => 'Start date must be before end date']);
            }
        }

        // Update course
        if ($courseModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update course']);
        }
    }
}
