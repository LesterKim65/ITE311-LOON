<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Assignments extends BaseController
{
    protected $assignmentModel;
    protected $submissionModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Teacher: Create assignment
     */
    public function create($course_id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'teacher' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only teachers can create assignments.');
        }

        $user_id = session()->get('id');

        // Get course_id from URL or POST
        if (!$course_id) {
            $course_id = $this->request->getPost('course_id');
        }

        if (!$course_id) {
            return redirect()->to('/dashboard')->with('error', 'Course ID is required.');
        }

        // Verify teacher owns this course
        $course = $this->courseModel->find($course_id);
        if (!$course || ($course['instructor_id'] != $user_id && $role !== 'admin')) {
            return redirect()->to('/dashboard')->with('error', 'You do not have access to this course.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'description' => 'permit_empty',
                'due_date' => 'required|valid_date',
                'course_id' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                return view('assignments/create', [
                    'course_id' => $course_id,
                    'course' => $course,
                    'validation' => $this->validator
                ]);
            }

            $data = [
                'course_id' => $course_id,
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'due_date' => $this->request->getPost('due_date'),
                'created_by' => $user_id
            ];

            // Handle file upload
            $file = $this->request->getFile('attachment');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
                $maxSize = 50 * 1024 * 1024; // 50MB
                $extension = strtolower($file->getClientExtension());
                $size = $file->getSize();

                if (!in_array($extension, $allowedTypes)) {
                    return view('assignments/create', [
                        'course_id' => $course_id,
                        'course' => $course,
                        'error' => 'Invalid file type. Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG, ZIP, RAR.'
                    ]);
                } elseif ($size > $maxSize) {
                    return view('assignments/create', [
                        'course_id' => $course_id,
                        'course' => $course,
                        'error' => 'File size exceeds 50MB.'
                    ]);
                } else {
                    $uploadPath = WRITEPATH . 'uploads/assignments/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $newName = uniqid() . '_' . $file->getClientName();
                    if ($file->move($uploadPath, $newName)) {
                        $data['attachment_path'] = 'uploads/assignments/' . $newName;
                        $data['attachment_name'] = $file->getClientName();
                    } else {
                        return view('assignments/create', [
                            'course_id' => $course_id,
                            'course' => $course,
                            'error' => 'Failed to upload file.'
                        ]);
                    }
                }
            }

            if ($this->assignmentModel->insert($data)) {
                return redirect()->to('/assignments/course/' . $course_id)->with('success', 'Assignment created successfully!');
            } else {
                return view('assignments/create', [
                    'course_id' => $course_id,
                    'course' => $course,
                    'error' => 'Failed to create assignment.'
                ]);
            }
        }

        return view('assignments/create', [
            'course_id' => $course_id,
            'course' => $course
        ]);
    }

    /**
     * List assignments for a course (Teacher view)
     */
    public function listByCourse($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $user_id = session()->get('id');

        // Verify access
        $course = $this->courseModel->find($course_id);
        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found.');
        }

        if ($role === 'teacher' && $course['instructor_id'] != $user_id) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $assignments = $this->assignmentModel->getAssignmentsByCourse($course_id);

        return view('assignments/list_teacher', [
            'course' => $course,
            'assignments' => $assignments
        ]);
    }

    /**
     * Student: List all assignments for enrolled courses
     */
    public function list()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $student_id = session()->get('id');
        $assignments = $this->assignmentModel->getAssignmentsForStudent($student_id);

        // Get submission status for each assignment
        foreach ($assignments as &$assignment) {
            $submission = $this->submissionModel->getSubmission($assignment['id'], $student_id);
            if ($submission) {
                $assignment['submission'] = $submission;
                $assignment['status'] = $submission['status'];
            } else {
                $assignment['submission'] = null;
                $assignment['status'] = 'not_submitted';
            }
        }

        return view('assignments/list_student', [
            'assignments' => $assignments
        ]);
    }

    /**
     * Student: Submit assignment
     */
    public function submit($assignment_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $student_id = session()->get('id');

        // Get assignment with course info
        $assignment = $this->assignmentModel->getAssignmentWithCourse($assignment_id);
        if (!$assignment) {
            return redirect()->to('/assignments')->with('error', 'Assignment not found.');
        }

        // Check if student is enrolled
        if (!$this->enrollmentModel->isAlreadyEnrolled($student_id, $assignment['course_id'])) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course.');
        }

        // Check if already submitted
        $existingSubmission = $this->submissionModel->getSubmission($assignment_id, $student_id);
        
        // Check if due date has passed
        $dueDate = new \DateTime($assignment['due_date']);
        $now = new \DateTime();
        $isPastDue = $now > $dueDate;

        if ($this->request->getMethod() === 'POST') {
            // Prevent resubmission after due date (unless already submitted)
            if ($isPastDue && !$existingSubmission) {
                return view('assignments/submit', [
                    'assignment' => $assignment,
                    'error' => 'Cannot submit assignment after due date.',
                    'isPastDue' => true
                ]);
            }

            // Get file and text inputs
            $file = $this->request->getFile('submission_file');
            $submissionNotes = $this->request->getPost('submission_notes');
            
            // Validate that at least one is provided
            $hasFile = $file && $file->isValid() && !$file->hasMoved();
            $hasText = !empty(trim($submissionNotes));
            
            if (!$hasFile && !$hasText) {
                return view('assignments/submit', [
                    'assignment' => $assignment,
                    'error' => 'Please provide either a file upload or text submission (or both).',
                    'isPastDue' => $isPastDue,
                    'existingSubmission' => $existingSubmission
                ]);
            }

            // Validate text if provided
            $rules = [
                'submission_notes' => 'permit_empty|max_length[5000]'
            ];

            if (!$this->validate($rules)) {
                return view('assignments/submit', [
                    'assignment' => $assignment,
                    'validation' => $this->validator,
                    'isPastDue' => $isPastDue,
                    'existingSubmission' => $existingSubmission
                ]);
            }

            // Prepare submission data
            $data = [
                'assignment_id' => $assignment_id,
                'user_id' => $student_id,
                'submission_type' => 'assignment',
                'submission_notes' => $submissionNotes,
                'status' => 'submitted',
                'submitted_at' => date('Y-m-d H:i:s')
            ];

            // Handle file upload if provided
            if ($hasFile) {
                // Validate file
                $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
                $maxSize = 50 * 1024 * 1024; // 50MB
                $extension = strtolower($file->getClientExtension());
                $size = $file->getSize();

                if (!in_array($extension, $allowedTypes)) {
                    return view('assignments/submit', [
                        'assignment' => $assignment,
                        'error' => 'Invalid file type. Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG, ZIP, RAR.',
                        'isPastDue' => $isPastDue,
                        'existingSubmission' => $existingSubmission
                    ]);
                } elseif ($size > $maxSize) {
                    return view('assignments/submit', [
                        'assignment' => $assignment,
                        'error' => 'File size exceeds 50MB.',
                        'isPastDue' => $isPastDue,
                        'existingSubmission' => $existingSubmission
                    ]);
                }

                // Upload file
                $uploadPath = WRITEPATH . 'uploads/submissions/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $newName = uniqid() . '_' . $file->getClientName();
                if ($file->move($uploadPath, $newName)) {
                    $data['submission_file_path'] = 'uploads/submissions/' . $newName;
                    $data['submission_file_name'] = $file->getClientName();
                } else {
                    return view('assignments/submit', [
                        'assignment' => $assignment,
                        'error' => 'Failed to upload file.',
                        'isPastDue' => $isPastDue,
                        'existingSubmission' => $existingSubmission
                    ]);
                }
            } else {
                // No file provided, keep existing file if updating
                if ($existingSubmission) {
                    $data['submission_file_path'] = $existingSubmission['submission_file_path'];
                    $data['submission_file_name'] = $existingSubmission['submission_file_name'];
                } else {
                    $data['submission_file_path'] = null;
                    $data['submission_file_name'] = null;
                }
            }

            // Save submission
            if ($existingSubmission) {
                // Update existing submission
                // Delete old file only if new file is uploaded
                if ($hasFile && $existingSubmission['submission_file_path']) {
                    $oldPath = WRITEPATH . $existingSubmission['submission_file_path'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $this->submissionModel->update($existingSubmission['id'], $data);
            } else {
                // Create new submission
                $this->submissionModel->insert($data);
            }

            return redirect()->to('/assignments')->with('success', 'Assignment submitted successfully!');
        }

        return view('assignments/submit', [
            'assignment' => $assignment,
            'isPastDue' => $isPastDue,
            'existingSubmission' => $existingSubmission
        ]);
    }

    /**
     * Teacher: View submissions for an assignment
     */
    public function viewSubmissions($assignment_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $user_id = session()->get('id');

        if ($role !== 'teacher' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        // Get assignment with course info
        $assignment = $this->assignmentModel->getAssignmentWithCourse($assignment_id);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found.');
        }

        // Verify teacher owns this course
        if ($assignment['instructor_id'] != $user_id && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        // Get all submissions
        $submissions = $this->submissionModel->getSubmissionsByAssignment($assignment_id);

        // Get students who haven't submitted
        $nonSubmittingStudents = $this->submissionModel->getNonSubmittingStudents($assignment_id, $assignment['course_id']);

        return view('assignments/submissions', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'nonSubmittingStudents' => $nonSubmittingStudents
        ]);
    }

    /**
     * Teacher: Grade assignment submission
     */
    public function grade($submission_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $user_id = session()->get('id');

        if ($role !== 'teacher' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $submission = $this->submissionModel->find($submission_id);
        if (!$submission) {
            return redirect()->to('/dashboard')->with('error', 'Submission not found.');
        }

        // Verify it's an assignment submission
        if ($submission['submission_type'] !== 'assignment') {
            return redirect()->to('/dashboard')->with('error', 'Invalid submission type.');
        }

        // Get assignment with course info
        if (empty($submission['assignment_id'])) {
            return redirect()->to('/dashboard')->with('error', 'Assignment ID not found in submission.');
        }
        
        $assignment = $this->assignmentModel->getAssignmentWithCourse($submission['assignment_id']);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found.');
        }

        // Verify teacher owns this course
        if ($assignment['instructor_id'] != $user_id && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'grade' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
                'feedback' => 'permit_empty|max_length[2000]'
            ];

            if (!$this->validate($rules)) {
                return view('assignments/grade', [
                    'submission' => $submission,
                    'assignment' => $assignment,
                    'validation' => $this->validator
                ]);
            }

            $grade = $this->request->getPost('grade');
            $feedback = $this->request->getPost('feedback');

            // Update the submission with grade
            $updateData = [
                'score' => $grade,
                'feedback' => $feedback,
                'status' => 'graded',
                'graded_at' => date('Y-m-d H:i:s')
            ];

            if ($this->submissionModel->update($submission_id, $updateData)) {
                return redirect()->to('/assignments/submissions/' . $submission['assignment_id'])->with('success', 'Grade submitted successfully!');
            } else {
                $errors = $this->submissionModel->errors();
                $errorMsg = 'Failed to submit grade.';
                if (!empty($errors)) {
                    $errorMsg .= ' ' . implode(', ', $errors);
                }
                return view('assignments/grade', [
                    'submission' => $submission,
                    'assignment' => $assignment,
                    'error' => $errorMsg
                ]);
            }
        }

        return view('assignments/grade', [
            'submission' => $submission,
            'assignment' => $assignment
        ]);
    }

    /**
     * Download assignment attachment (teacher/student)
     */
    public function downloadAttachment($assignment_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $assignment = $this->assignmentModel->find($assignment_id);
        if (!$assignment || !$assignment['attachment_path']) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $role = session()->get('role');
        $user_id = session()->get('id');

        // Verify access
        if ($role === 'teacher') {
            $course = $this->courseModel->find($assignment['course_id']);
            if ($course['instructor_id'] != $user_id) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } elseif ($role === 'student') {
            if (!$this->enrollmentModel->isAlreadyEnrolled($user_id, $assignment['course_id'])) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } else {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = WRITEPATH . $assignment['attachment_path'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($filePath, null)->setFileName($assignment['attachment_name']);
    }

    /**
     * Download submission file (teacher/student)
     */
    public function downloadSubmission($submission_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $submission = $this->submissionModel->find($submission_id);
        if (!$submission || !$submission['submission_file_path']) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $role = session()->get('role');
        $user_id = session()->get('id');

        // Verify access
        if ($role === 'teacher') {
            $assignment = $this->assignmentModel->getAssignmentWithCourse($submission['assignment_id']);
            if ($assignment['instructor_id'] != $user_id) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } elseif ($role === 'student') {
            if ($submission['user_id'] != $user_id) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } else {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = WRITEPATH . $submission['submission_file_path'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($filePath, null)->setFileName($submission['submission_file_name']);
    }
}

