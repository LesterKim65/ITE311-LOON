<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <h2 class="mb-4 text-center">Dashboard</h2>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?= esc($name) ?>!</h5>
                <p class="card-text">Your role: <?= esc($role) ?></p>
                <a href="<?= site_url('logout') ?>" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if ($role == 'student'): ?>
            <!-- Enrolled Courses -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Your Enrolled Courses</h5>
                </div>
                <div class="card-body" id="enrolled-courses">
                    <div class="row" id="enrolled-courses-container">
                        <?php if (isset($enrolledCourses) && !empty($enrolledCourses)): ?>
                            <?php foreach ($enrolledCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                            <p class="card-text"><?= esc($course['description']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center text-muted mb-0">No enrolled courses found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Assignments -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Assignments</h5>
                    <a href="<?= site_url('assignments') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-clipboard-list"></i> View All Assignments
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    $assignmentModel = new \App\Models\AssignmentModel();
                    $assignmentSubmissionModel = new \App\Models\AssignmentSubmissionModel();
                    $hasAssignments = false;
                    if (isset($enrolledCourses) && !empty($enrolledCourses)):
                        foreach ($enrolledCourses as $course):
                            $assignments = $assignmentModel->getAssignmentsByCourse($course['id']);
                            if (!empty($assignments)):
                                $hasAssignments = true;
                    ?>
                            <h6 class="mb-3"><?= esc($course['title']) ?> Assignments</h6>
                            <div class="row mb-3">
                                <?php foreach (array_slice($assignments, 0, 3) as $assignment): ?>
                                    <?php
                                    $submission = $assignmentSubmissionModel->getSubmission($assignment['id'], session()->get('id'));
                                    $status = $submission ? $submission['status'] : 'not_submitted';
                                    $dueDate = new \DateTime($assignment['due_date']);
                                    $now = new \DateTime();
                                    $isPastDue = $now > $dueDate;
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card <?= $isPastDue && $status !== 'graded' ? 'border-warning' : '' ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= esc($assignment['title']) ?></h6>
                                                <p class="card-text small text-muted">
                                                    Due: <?= date('M d, Y', strtotime($assignment['due_date'])) ?>
                                                </p>
                                                <div class="mb-2">
                                                    <?php
                                                    $badgeClass = 'bg-secondary';
                                                    if ($status === 'graded') {
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($status === 'submitted') {
                                                        $badgeClass = 'bg-primary';
                                                    } elseif ($isPastDue) {
                                                        $badgeClass = 'bg-danger';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                                    </span>
                                                </div>
                                                <?php if ($status === 'graded' && $submission): ?>
                                                    <p class="mb-2"><strong>Grade:</strong> <?= number_format($submission['score'], 2) ?>%</p>
                                                <?php endif; ?>
                                                <a href="<?= site_url('assignments/submit/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                                    <?= $status === 'not_submitted' ? 'Submit' : 'View' ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($assignments) > 3): ?>
                                <p class="text-center">
                                    <a href="<?= site_url('assignments') ?>" class="btn btn-sm btn-outline-primary">
                                        View All Assignments
                                    </a>
                                </p>
                            <?php endif; ?>
                    <?php
                            endif;
                        endforeach;
                    endif;
                    if (!$hasAssignments):
                    ?>
                        <p class="text-center text-muted mb-0">No assignments available for your enrolled courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Course Materials -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Course Materials</h5>
                </div>
                <div class="card-body">
                    <?php
                    $materialModel = new \App\Models\MaterialModel();
                    $hasMaterials = false;
                    if (isset($enrolledCourses) && !empty($enrolledCourses)):
                        foreach ($enrolledCourses as $course):
                            $materials = $materialModel->getMaterialsByCourse($course['id']);
                            if (!empty($materials)):
                                $hasMaterials = true;
                    ?>
                            <h6 class="mb-3"><?= esc($course['title']) ?> Materials</h6>
                            <ul class="list-group mb-3">
                                <?php foreach ($materials as $material): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= esc($material['file_name']) ?>
                                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-primary">Download</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                    <?php
                            endif;
                        endforeach;
                    endif;
                    if (!$hasMaterials):
                    ?>
                        <p class="text-center text-muted mb-0">No materials available for your enrolled courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Courses -->
            <div class="card">
                <div class="card-header">
                    <h5>Available Courses</h5>
                </div>
                <div class="card-body" id="available-courses">
                    <?php if (isset($availableCourses) && !empty($availableCourses)): ?>
                        <div class="row">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                            <p class="card-text"><?= esc($course['description']) ?></p>
                                            <button type="button" class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>">Enroll</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No available courses found.</p>
                    <?php endif; ?>
                </div>
            </div>



        <?php elseif ($role == 'teacher' || (isset($courses) && !empty($courses))): ?>
            <!-- Assignments Overview -->
            <?php if (isset($courses) && !empty($courses)): ?>
                <?php
                $assignmentModel = new \App\Models\AssignmentModel();
                $assignmentSubmissionModel = new \App\Models\AssignmentSubmissionModel();
                $allAssignments = [];
                $pendingGrading = 0;
                foreach ($courses as $course) {
                    $assignments = $assignmentModel->getAssignmentsByCourse($course['id']);
                    foreach ($assignments as $assignment) {
                        $assignment['course_title'] = $course['title'];
                        $assignment['course_id'] = $course['id'];
                        $allAssignments[] = $assignment;
                        // Count submissions that need grading
                        $submissions = $assignmentSubmissionModel->getSubmissionsByAssignment($assignment['id']);
                        foreach ($submissions as $sub) {
                            if ($sub['status'] !== 'graded') {
                                $pendingGrading++;
                            }
                        }
                    }
                }
                ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list"></i> Assignments Overview
                        </h5>
                        <?php if ($pendingGrading > 0): ?>
                            <span class="badge bg-warning"><?= $pendingGrading ?> Pending Grading</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($allAssignments)): ?>
                            <div class="row">
                                <?php foreach (array_slice($allAssignments, 0, 6) as $assignment): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="<?= site_url('assignments/submissions/' . $assignment['id']) ?>" class="text-decoration-none">
                                                        <?= esc($assignment['title']) ?>
                                                    </a>
                                                </h6>
                                                <p class="card-text small text-muted mb-2">
                                                    <strong>Course:</strong> <?= esc($assignment['course_title']) ?>
                                                </p>
                                                <p class="card-text small mb-2">
                                                    <i class="fas fa-calendar"></i> Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                                </p>
                                                <a href="<?= site_url('assignments/submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View Submissions
                                                </a>
                                                <a href="<?= site_url('assignments/course/' . $assignment['course_id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-list"></i> All Assignments
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($allAssignments) > 6): ?>
                                <div class="text-center mt-3">
                                    <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary">
                                        View All Assignments
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-center text-muted mb-0">No assignments created yet. Create assignments from your courses.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Course Management -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Taught Courses</h5>
                    <div>
                        <a href="<?= site_url('manage-students') ?>" class="btn btn-sm btn-success me-2">
                            <i class="fas fa-users"></i> Manage Students
                        </a>
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            <i class="fas fa-plus"></i> Create Course
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($courses) && !empty($courses)): ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <a href="#" class="text-decoration-none course-title" data-course-id="<?= esc($course['id']) ?>">
                                                    <?= esc($course['title']) ?>
                                                </a>
                                            </h6>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($role == 'teacher'): ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= site_url('assignments/course/' . $course['id']) ?>">
                                                            <i class="fas fa-clipboard-list"></i> Assignments
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>">
                                                            <i class="fas fa-upload"></i> Upload Material
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= site_url('admin/course/' . $course['id'] . '/materials') ?>">
                                                            <i class="fas fa-file-alt"></i> View Materials
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteCourse(<?= esc($course['id']) ?>)">
                                                            <i class="fas fa-trash"></i> Delete Course
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted small">
                                                <?= esc(substr($course['description'], 0, 100)) ?>
                                                <?php if (strlen($course['description']) > 100): ?>...<?php endif; ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Course ID: <?= esc($course['id']) ?></small>
                                                <span class="badge bg-primary">Active</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No courses found</h5>
                            <p class="text-muted">Start by creating your first course</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                <i class="fas fa-plus"></i> Create Your First Course
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


            <script>
                function deleteCourse(courseId) {
                    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
                        // Implement course deletion
                        alert('Course deletion feature coming soon!');
                    }
                }
            </script>

        <?php elseif ($role == 'admin'): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>All Users</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($users) && !empty($users)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?= esc($user['id']) ?></td>
                                                    <td><?= esc($user['name']) ?></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                    <td><?= esc($user['role']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No users found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>All Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($courses) && !empty($courses)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Instructor ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($courses as $course): ?>
                                                <tr>
                                                    <td><?= esc($course['id']) ?></td>
                                                    <td><?= esc($course['title']) ?></td>
                                                    <td><?= esc($course['description']) ?></td>
                                                    <td><?= esc($course['instructor_id']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No courses found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
