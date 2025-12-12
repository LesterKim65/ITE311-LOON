<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Manage Students
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-12">
        <h2 class="mb-4">Manage Students</h2>

        <?php if (isset($course)): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Course: <?= esc($course['course_code'] . ' - ' . $course['title']) ?></h5>
                    <p class="card-text"><?= esc($course['description']) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search and Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" action="<?= site_url('manage-students') ?>" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, ID, or email" value="<?= isset($search) ? esc($search) : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="year_level" class="form-select">
                            <option value="">Year Level</option>
                            <option value="1" <?= (isset($year_level) && $year_level == '1') ? 'selected' : '' ?>>1st Year</option>
                            <option value="2" <?= (isset($year_level) && $year_level == '2') ? 'selected' : '' ?>>2nd Year</option>
                            <option value="3" <?= (isset($year_level) && $year_level == '3') ? 'selected' : '' ?>>3rd Year</option>
                            <option value="4" <?= (isset($year_level) && $year_level == '4') ? 'selected' : '' ?>>4th Year</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Enrollment Status</option>
                            <option value="active" <?= (isset($status) && $status == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="pending" <?= (isset($status) && $status == 'pending') ? 'selected' : '' ?>>Pending Approval</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="program" class="form-select">
                            <option value="">Program</option>
                            <option value="Computer Science" <?= (isset($program) && $program == 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
                            <option value="Information Technology" <?= (isset($program) && $program == 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                            <option value="Engineering" <?= (isset($program) && $program == 'Engineering') ? 'selected' : '' ?>>Engineering</option>
                            <option value="Business Administration" <?= (isset($program) && $program == 'Business Administration') ? 'selected' : '' ?>>Business Administration</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student List Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Year Level</th>
                                <th>Enrollment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($students) && !empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= esc($student['id']) ?></td>
                                        <td><?= esc($student['name']) ?></td>
                                        <td><?= esc($student['email']) ?></td>
                                        <td><?= esc($student['program'] ?? 'N/A') ?></td>
                                        <td><?= esc($student['year_level'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge
                                                <?= $student['status'] == 'active' ? 'bg-success' :
                                                   ($student['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                                <?= esc(ucfirst($student['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-details-btn"
                                                    data-student-id="<?= esc($student['id']) ?>"
                                                    data-student-name="<?= esc($student['name']) ?>"
                                                    data-student-email="<?= esc($student['email']) ?>"
                                                    data-student-program="<?= esc($student['program'] ?? 'N/A') ?>"
                                                    data-student-year="<?= esc($student['year_level'] ?? 'N/A') ?>"
                                                    data-student-section="<?= esc($student['section'] ?? 'N/A') ?>"
                                                    data-student-enrollment="<?= esc($student['enrolled_at'] ?? 'N/A') ?>"
                                                    data-student-status="<?= esc($student['status']) ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="btn btn-sm btn-warning update-status-btn"
                                                    data-student-id="<?= esc($student['id']) ?>"
                                                    data-current-status="<?= esc($student['status']) ?>">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <button class="btn btn-sm btn-danger remove-student-btn"
                                                    data-student-id="<?= esc($student['id']) ?>"
                                                    data-course-id="<?= isset($course) ? esc($course['id']) : '' ?>">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No students found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Student ID:</strong></div>
                    <div class="col-md-8" id="modal-student-id"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Full Name:</strong></div>
                    <div class="col-md-8" id="modal-student-name"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Email:</strong></div>
                    <div class="col-md-8" id="modal-student-email"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Program/Major:</strong></div>
                    <div class="col-md-8" id="modal-student-program"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Year Level:</strong></div>
                    <div class="col-md-8" id="modal-student-year"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Section:</strong></div>
                    <div class="col-md-8" id="modal-student-section"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Enrollment Date:</strong></div>
                    <div class="col-md-8" id="modal-student-enrollment"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Status:</strong></div>
                    <div class="col-md-8" id="modal-student-status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">Update Student Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="update-student-id" name="student_id">
                    <input type="hidden" id="update-course-id" name="course_id" value="<?= isset($course) ? esc($course['id']) : '' ?>">

                    <div class="mb-3">
                        <label for="current-status" class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="current-status" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="new-status" class="form-label">New Status</label>
                        <select class="form-select" id="new-status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-status-btn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Details button click handler
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');
            const studentEmail = this.getAttribute('data-student-email');
            const studentProgram = this.getAttribute('data-student-program');
            const studentYear = this.getAttribute('data-student-year');
            const studentSection = this.getAttribute('data-student-section');
            const studentEnrollment = this.getAttribute('data-student-enrollment');
            const studentStatus = this.getAttribute('data-student-status');

            document.getElementById('modal-student-id').textContent = studentId;
            document.getElementById('modal-student-name').textContent = studentName;
            document.getElementById('modal-student-email').textContent = studentEmail;
            document.getElementById('modal-student-program').textContent = studentProgram;
            document.getElementById('modal-student-year').textContent = studentYear;
            document.getElementById('modal-student-section').textContent = studentSection;
            document.getElementById('modal-student-enrollment').textContent = studentEnrollment;
            document.getElementById('modal-student-status').textContent = studentStatus;

            const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
            modal.show();
        });
    });

    // Update Status button click handler
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const currentStatus = this.getAttribute('data-current-status');

            document.getElementById('update-student-id').value = studentId;
            document.getElementById('current-status').value = currentStatus;

            const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
            modal.show();
        });
    });

    // Save Status button click handler
    document.getElementById('save-status-btn').addEventListener('click', function() {
        const form = document.getElementById('statusUpdateForm');
        const formData = new FormData(form);

        fetch('<?= site_url('update-student-status') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    });

    // Remove Student button click handler
    document.querySelectorAll('.remove-student-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this student from the course?')) {
                const studentId = this.getAttribute('data-student-id');
                const courseId = this.getAttribute('data-course-id');

                fetch('<?= site_url('remove-student-from-course') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `student_id=${studentId}&course_id=${courseId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Student removed from course successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the student.');
                });
            }
        });
    });


});
</script>
<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current teacher dashboard implementation
- [x] Examine course assignment logic
- [x] Identify bug in course display
- [x] Implement manage students page
- [ ] Fix course assignment display bug
- [ ] Test the implementation
</task_progress>
</write_to_file>
