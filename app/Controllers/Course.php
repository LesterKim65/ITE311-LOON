<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Course extends BaseController
{
    public function enroll()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        $user_id = session()->get('id');
        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID required']);
        }

        $enrollmentModel = new EnrollmentModel();

        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already enrolled in this course']);
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id
        ];

        if ($enrollmentModel->enrollUser($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Enrolled successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment failed']);
        }
    }
}
