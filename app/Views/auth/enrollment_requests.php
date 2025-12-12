<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Enrollment Requests
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <h2 class="mb-4">Enrollment Requests</h2>

        <?php if (isset($pendingEnrollments) && !empty($pendingEnrollments)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingEnrollments as $enrollment): ?>
                            <tr>
                                <td><?= esc($enrollment['name']) ?></td>
                                <td><?= esc($enrollment['email']) ?></td>
                                <td><?= esc($enrollment['course_title']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($enrollment['enrolled_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success approve-btn"
                                            data-student-id="<?= esc($enrollment['user_id']) ?>"
                                            data-student-name="<?= esc($enrollment['name']) ?>"
                                            data-course-id="<?= esc($enrollment['course_id']) ?>"
                                            data-course-name="<?= esc($enrollment['course_title']) ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-btn"
                                            data-student-id="<?= esc($enrollment['user_id']) ?>"
                                            data-student-name="<?= esc($enrollment['name']) ?>"
                                            data-course-id="<?= esc($enrollment['course_id']) ?>"
                                            data-course-name="<?= esc($enrollment['course_title']) ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Pending Enrollment Requests</h5>
                    <p class="text-muted">All enrollment requests have been processed.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approve button click handler
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');
            const courseId = this.getAttribute('data-course-id');
            const courseName = this.getAttribute('data-course-name');

            if (confirm(`Are you sure you want to approve ${studentName}'s enrollment in ${courseName}?`)) {
                fetch('<?= site_url('approve-enrollment') ?>', {
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
                        alert('Enrollment approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the enrollment.');
                });
            }
        });
    });

    // Reject button click handler
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');
            const courseId = this.getAttribute('data-course-id');
            const courseName = this.getAttribute('data-course-name');

            if (confirm(`Are you sure you want to reject ${studentName}'s enrollment request for ${courseName}? This action cannot be undone.`)) {
                fetch('<?= site_url('reject-enrollment') ?>', {
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
                        alert('Enrollment rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the enrollment.');
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
