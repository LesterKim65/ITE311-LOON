<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;

class Auth extends BaseController
{
	public function register()
	{
		helper(['form']);

		log_message('info', 'Auth::register method: {method}', ['method' => $this->request->getMethod()]);

		// Treat as submission whenever there is POST data (handles environments where method check is unreliable)
		if (! empty($this->request->getPost())) {
			log_message('info', 'Auth::register POST received');
			$rules = [
				'name'             => 'required|min_length[3]|max_length[50]',
				'email'            => 'required|valid_email|is_unique[users.email]',
				'password'         => 'required|min_length[6]|max_length[255]',
				'password_confirm' => 'required|matches[password]',
				'role'             => 'required|in_list[student,teacher,admin]'
			];

			if (! $this->validate($rules)) {
				$errorString = (string) implode(' ', $this->validator->getErrors());
				log_message('warning', 'Auth::register validation failed: {errors}', [
					'errors' => $errorString,
				]);
				// Return the view directly so errors show without relying on session/flashdata
				$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
				return view('auth/register', [
					'validation' => $this->validator,
					'unreadCount' => $unreadCount
				]);
			}

			$userModel = new UserModel();

			$data = [
				'name'     => $this->request->getPost('name'),
				'email'    => $this->request->getPost('email'),
				'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
				'role'     => $this->request->getPost('role'),
			];

			try {
				if (! $userModel->save($data)) {
					$errors = $userModel->errors();
					$message = $errors ? implode(' ', $errors) : 'Unknown error.';
					log_message('error', 'Auth::register model save errors: {msg}', ['msg' => $message]);
					return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $message);
				}
			} catch (\Throwable $e) {
				log_message('critical', 'Auth::register exception: {msg}', ['msg' => $e->getMessage()]);
				return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
			}

			// Render login view directly to avoid redirect issues
			$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
			return view('auth/login', [
				'success' => 'Registration successful! Please login.',
				'unreadCount' => $unreadCount
			]);
		}

		$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
		return view('auth/register', ['unreadCount' => $unreadCount]);
	}

	public function login()
	{
		helper(['form']);

		log_message('info', 'Auth::login method: {method}', ['method' => $this->request->getMethod()]);

		if (! empty($this->request->getPost())) {
			$rules = [
				'email'    => 'required|valid_email',
				'password' => 'required|min_length[6]|max_length[255]'
			];

			if (! $this->validate($rules)) {
				log_message('warning', 'Auth::login validation failed: {errors}', [
					'errors' => implode(' ', $this->validator->getErrors()),
				]);
				$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
				return view('auth/login', [
					'validation' => $this->validator,
					'unreadCount' => $unreadCount
				]);
			}

			$userModel = new UserModel();
			$user = $userModel->where('email', $this->request->getPost('email'))->first();

			// Check if user exists and password is correct
			if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
				// Check if user is inactive - prevent login
				$userStatus = isset($user['status']) ? $user['status'] : 'active';
				if ($userStatus === 'inactive') {
					log_message('warning', 'Login attempt for inactive user: {email}', ['email' => $this->request->getPost('email')]);
					$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
					return view('auth/login', ['error' => 'Your account has been deactivated. Please contact an administrator.', 'unreadCount' => $unreadCount]);
				}
				$sessionData = [
					'id'         => $user['id'],
					'name'       => $user['name'],
					'email'      => $user['email'],
					'role'       => $user['role'],
					'status'     => isset($user['status']) ? $user['status'] : 'active',
					'isLoggedIn' => true
				];

				// Set session data
				foreach ($sessionData as $key => $value) {
					session()->set($key, $value);
				}

				log_message('info', 'Login successful for user: {email}', ['email' => $user['email']]);
				log_message('info', 'Session data set: {data}', ['data' => json_encode($sessionData)]);

				// Test if session is working
				$testSession = session()->get('isLoggedIn');
				log_message('info', 'Session test - isLoggedIn: {value}', ['value' => $testSession]);

				return redirect()->to(site_url('dashboard'))
					->with('success', 'Welcome back, ' . $user['name'] . '!');
			}

			log_message('warning', 'Login failed for email: {email}', ['email' => $this->request->getPost('email')]);
			$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
			return view('auth/login', ['error' => 'Invalid login credentials.', 'unreadCount' => $unreadCount]);
		}

		$unreadCount = session()->get('isLoggedIn') ? (new NotificationModel())->getUnreadCount(session()->get('id')) : 0;
		return view('auth/login', ['unreadCount' => $unreadCount]);
	}

	public function dashboard()
	{
		if (! session()->get('isLoggedIn')) {
			return redirect()->to('/login');
		}

		// Check if user was marked inactive (from BaseController check)
		if (isset($this->data['userInactive']) && $this->data['userInactive']) {
			return redirect()->to('/login')
				->with('error', 'Your account has been deactivated. Please contact an administrator.');
		}

		// Double-check user status (extra safety)
		$userModel = new UserModel();
		$user = $userModel->find(session()->get('id'));
		if (!$user || (isset($user['status']) && $user['status'] === 'inactive')) {
			session()->destroy();
			return redirect()->to('/login')
				->with('error', 'Your account has been deactivated. Please contact an administrator.');
		}

		$user_id = session()->get('id');
		$role = session()->get('role');

		$db = \Config\Database::connect();

		$data = [
			'name' => session()->get('name'),
			'role' => $role
		];

		if ($role == 'student') {
			// Get enrolled courses (only active/approved enrollments)
			$enrolledCourses = $db->query("SELECT c.id, c.title, c.description FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.user_id = ? AND e.status = 'active'", [$user_id])->getResultArray();
			$data['enrolledCourses'] = $enrolledCourses;

			// Get all courses with enrollment status
			$allCourses = $db->query("SELECT id, title, description FROM courses")->getResultArray();
			$availableCourses = [];

			foreach ($allCourses as $course) {
				// Check enrollment status for this student
				$enrollment = $db->query("SELECT status FROM enrollments WHERE user_id = ? AND course_id = ?", [$user_id, $course['id']])->getRowArray();

				if ($enrollment) {
					if ($enrollment['status'] == 'active') {
						// Already enrolled and approved - skip
						continue;
					} elseif ($enrollment['status'] == 'pending') {
						$course['enrollment_status'] = 'pending';
					} else {
						// rejected or other status - could show as available again
						$course['enrollment_status'] = 'available';
					}
				} else {
					$course['enrollment_status'] = 'available';
				}

				$availableCourses[] = $course;
			}
			$data['availableCourses'] = $availableCourses;
        } elseif ($role == 'teacher') {
            // Get courses where the teacher is the instructor
            $teacherCourses = $db->query("SELECT id, title, description FROM courses WHERE instructor_id = ?", [$user_id])->getResultArray();
            if (!empty($teacherCourses)) {
                $data['courses'] = $teacherCourses;
            }
        } elseif ($role == 'admin') {
			$users = $db->query("SELECT id, name, email, role FROM users")->getResultArray();
			$courses = $db->query("SELECT id, title, description, instructor_id FROM courses")->getResultArray();
			$data['users'] = $users;
			$data['courses'] = $courses;
		}

		$unreadCount = (new NotificationModel())->getUnreadCount($user_id);
		$data['unreadCount'] = $unreadCount;
		return view('auth/dashboard', $data);
	}

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))
            ->with('success', 'You have been logged out.');
    }

    public function manageStudents()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $db = \Config\Database::connect();

        // Get the course ID from query parameters or use the first course assigned to the teacher
        $course_id = $this->request->getGet('course_id');
        $course = null;

        if ($course_id) {
            // Verify the teacher owns this course
            $course = $db->query("SELECT * FROM courses WHERE id = ? AND instructor_id = ?", [$course_id, $user_id])->getRowArray();
            if (!$course) {
                return redirect()->to('/dashboard')->with('error', 'Course not found or access denied.');
            }
        } else {
            // Get the first course assigned to the teacher
            $course = $db->query("SELECT * FROM courses WHERE instructor_id = ? LIMIT 1", [$user_id])->getRowArray();
            if ($course) {
                $course_id = $course['id'];
            }
        }

        // Get search and filter parameters
        $search = $this->request->getGet('search');
        $year_level = $this->request->getGet('year_level');
        $status = $this->request->getGet('status');
        $program = $this->request->getGet('program');

        // Use EnrollmentModel to get enrollments with user data
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $students = $enrollmentModel->getEnrollmentsByCourse($course_id);

        // Apply search filter (by name, id, or email)
        if (!empty($search)) {
            $students = array_filter($students, function($student) use ($search) {
                return strpos(strtolower($student['name']), strtolower($search)) !== false ||
                       strpos(strtolower($student['id']), strtolower($search)) !== false ||
                       strpos(strtolower($student['email']), strtolower($search)) !== false;
            });
        }

        // Apply filters
        if (!empty($year_level)) {
            // Assuming year_level is in users table, we need to get it
            // For simplicity, let's get it individually or modify the model
            // For now, we'll skip year_level and program filters as they might not be populated
        }
        if (!empty($program)) {
            // Similar to year_level
        }
        if (!empty($status)) {
            $students = array_filter($students, function($student) use ($status) {
                return $student['status'] == $status;
            });
        }



        $data = [
            'name' => session()->get('name'),
            'role' => $role,
            'course' => $course,
            'students' => $students,
            'search' => $search,
            'year_level' => $year_level,
            'status' => $status,
            'program' => $program
        ];

        $unreadCount = (new NotificationModel())->getUnreadCount($user_id);
        $data['unreadCount'] = $unreadCount;

        return view('auth/manage_students', $data);
    }

    public function updateStudentStatus()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $student_id = $this->request->getPost('student_id');
        $course_id = $this->request->getPost('course_id');
        $new_status = $this->request->getPost('status');
        $remarks = $this->request->getPost('remarks');

        // Validate required fields
        if (empty($student_id) || empty($course_id) || empty($new_status)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $db = \Config\Database::connect();

        // Verify the teacher owns this course
        $course = $db->query("SELECT id FROM courses WHERE id = ? AND instructor_id = ?", [$course_id, $user_id])->getRowArray();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        // Verify the student is enrolled in this course
        $enrollment = $db->query("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?", [$student_id, $course_id])->getRowArray();
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student not found in this course']);
        }

        // Update the enrollment status
        $db->query("UPDATE enrollments SET status = ?, updated_at = NOW() WHERE id = ?", [$new_status, $enrollment['id']]);

        return $this->response->setJSON(['success' => true, 'message' => 'Student status updated successfully']);
    }

    public function removeStudentFromCourse()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $student_id = $this->request->getPost('student_id');
        $course_id = $this->request->getPost('course_id');

        // Validate required fields
        if (empty($student_id) || empty($course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $db = \Config\Database::connect();

        // Verify the teacher owns this course
        $course = $db->query("SELECT id FROM courses WHERE id = ? AND instructor_id = ?", [$course_id, $user_id])->getRowArray();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        // Delete the enrollment
        $db->query("DELETE FROM enrollments WHERE user_id = ? AND course_id = ?", [$student_id, $course_id]);

        return $this->response->setJSON(['success' => true, 'message' => 'Student removed from course successfully']);
    }

    public function handleEnrollmentRequests()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Get all pending enrollments for courses taught by this teacher
        $pendingEnrollments = $enrollmentModel->select('enrollments.*, users.name, users.email, courses.title as course_title, courses.id as course_id')
                                              ->join('users', 'enrollments.user_id = users.id')
                                              ->join('courses', 'enrollments.course_id = courses.id')
                                              ->where('courses.instructor_id', $user_id)
                                              ->where('enrollments.status', 'pending')
                                              ->findAll();

        $data = [
            'name' => session()->get('name'),
            'role' => $role,
            'pendingEnrollments' => $pendingEnrollments
        ];

        $unreadCount = (new NotificationModel())->getUnreadCount($user_id);
        $data['unreadCount'] = $unreadCount;

        return view('auth/enrollment_requests', $data);
    }

    public function approveEnrollment()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        // Check if we have POST data (handles environments where method check is unreliable)
        if (empty($this->request->getPost())) {
            return $this->response->setJSON(['success' => false, 'message' => 'No POST data received']);
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $student_id = $this->request->getPost('student_id');
        $course_id = $this->request->getPost('course_id');

        // Validate required fields
        if (empty($student_id) || empty($course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $db = \Config\Database::connect();

        // Verify the teacher owns this course
        $course = $db->query("SELECT id FROM courses WHERE id = ? AND instructor_id = ?", [$course_id, $user_id])->getRowArray();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        // Find the enrollment record
        $enrollment = $db->query("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?", [$student_id, $course_id])->getRowArray();
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Check if enrollment is pending
        $enrollmentFull = $db->query("SELECT status FROM enrollments WHERE id = ?", [$enrollment['id']])->getRowArray();
        if ($enrollmentFull['status'] !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment is not pending approval']);
        }

        // Use EnrollmentModel to approve
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if ($enrollmentModel->approveEnrollment($enrollment['id'])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment approved successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to approve enrollment']);
        }
    }

    public function rejectEnrollment()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $user_id = session()->get('id');
        $role = session()->get('role');

        // Check if user is a teacher
        if ($role !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $student_id = $this->request->getPost('student_id');
        $course_id = $this->request->getPost('course_id');

        // Validate required fields
        if (empty($student_id) || empty($course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $db = \Config\Database::connect();

        // Verify the teacher owns this course
        $course = $db->query("SELECT id FROM courses WHERE id = ? AND instructor_id = ?", [$course_id, $user_id])->getRowArray();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        // Find the enrollment record
        $enrollment = $db->query("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?", [$student_id, $course_id])->getRowArray();
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Check if enrollment is pending
        $enrollmentFull = $db->query("SELECT status FROM enrollments WHERE id = ?", [$enrollment['id']])->getRowArray();
        if ($enrollmentFull['status'] !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment is not pending approval']);
        }

        // Delete the enrollment (reject)
        $db->query("DELETE FROM enrollments WHERE id = ?", [$enrollment['id']]);

        return $this->response->setJSON(['success' => true, 'message' => 'Enrollment rejected successfully']);
    }

    // TEMP: Debug helper to verify DB insert works without the form/CSRF

}
