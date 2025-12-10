<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Admin - Course Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Course Management</h1>
            <p class="text-muted mb-0">Manage all courses in the system.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">Total Courses</h5>
                            <p class="card-text text-muted mb-0"><?= $totalCourses ?></p>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">Active Courses</h5>
                            <p class="card-text text-muted mb-0"><?= $activeCourses ?></p>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form id="searchForm" class="d-flex" method="get" action="<?= base_url('admin/courses/search') ?>">
                <div class="input-group">
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search by title, course code, or teacher..."
                        name="search_term"
                        value="<?= esc($searchTerm ?? '') ?>"
                    >
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Schedule</th>
                            <th>Teacher</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= esc($course['course_code'] ?? '') ?></td>
                                    <td><?= esc($course['title']) ?></td>
                                    <td><?= esc($course['description'] ?? '') ?></td>
                                    <td><?= esc($course['school_year'] ?? '') ?></td>
                                    <td><?= esc($course['semester'] ?? '') ?></td>
                                    <td><?= esc($course['schedule'] ?? '') ?></td>
                                    <td><?= esc($course['teacher_name']) ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-dropdown" data-course-id="<?= $course['id'] ?>">
                                            <option value="active" <?= ($course['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= ($course['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-btn" data-course-id="<?= $course['id'] ?>">
                                            <i class="fas fa-edit"></i> Edit Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">No courses found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourseForm">
                <div class="modal-body">
                    <input type="hidden" id="courseId" name="course_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="courseCode" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="courseCode" name="course_code" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schoolYear" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="schoolYear" name="school_year">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="1st">1st</option>
                                <option value="2nd">2nd</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="courseTitle" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="courseTitle" name="title" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher" class="form-label">Teacher</label>
                            <select class="form-select" id="teacher" name="instructor_id" required>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule" class="form-label">Schedule</label>
                            <input type="text" class="form-control" id="schedule" name="schedule">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Status dropdown change
    $('.status-dropdown').on('change', function() {
        var courseId = $(this).data('course-id');
        var status = $(this).val();

        $.post('<?= base_url('admin/courses/update/') ?>' + courseId, { status: status }, function(response) {
            if (response.success) {
                // Update summary cards if needed
                location.reload();
            } else {
                alert('Failed to update status');
            }
        }, 'json');
    });

    // Edit button click
    $('.edit-btn').on('click', function() {
        var courseId = $(this).data('course-id');

        $.get('<?= base_url('admin/courses/get/') ?>' + courseId, function(response) {
            if (response.success) {
                var course = response.course;
                $('#courseId').val(course.id);
                $('#courseCode').val(course.course_code || '');
                $('#schoolYear').val(course.school_year || '');
                $('#semester').val(course.semester || '');
                $('#startDate').val(course.start_date || '');
                $('#endDate').val(course.end_date || '');
                $('#courseTitle').val(course.title);
                $('#description').val(course.description || '');
                $('#teacher').val(course.instructor_id);
                $('#schedule').val(course.schedule || '');
                $('#editCourseModal').modal('show');
            } else {
                alert('Failed to load course details');
            }
        }, 'json');
    });

    // Edit form submit
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();
        var courseId = $('#courseId').val();
        var formData = $(this).serialize();

        $.post('<?= base_url('admin/courses/update/') ?>' + courseId, formData, function(response) {
            if (response.success) {
                $('#editCourseModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        }, 'json');
    });

    // Search form
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();
        window.location.href = '<?= base_url('admin/courses/search') ?>?search_term=' + encodeURIComponent(searchTerm);
    });
});
</script>
<?= $this->endSection() ?>
