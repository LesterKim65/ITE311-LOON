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
                    <?php if (isset($enrolledCourses) && !empty($enrolledCourses)): ?>
                        <div class="row">
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
                        </div>
                    <?php else: ?>
                        <p>No enrolled courses found.</p>
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
                                            <button class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>">Enroll</button>
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

            <script>
                $(document).ready(function() {
                    $('.enroll-btn').click(function(e) {
                        e.preventDefault();
                        var courseId = $(this).data('course-id');
                        var button = $(this);
                        var courseCard = button.closest('.col-md-4');

                        // Add CSRF token to the request
                        $.ajax({
                            url: '<?= site_url('course/enroll') ?>',
                            type: 'POST',
                            data: {
                                course_id: courseId,
                                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Show success message
                                    $('#available-courses .card-body').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                                    
                                    // Disable the button
                                    button.prop('disabled', true).text('Enrolled').removeClass('btn-primary').addClass('btn-secondary');
                                    
                                    // Reload the page after 1.5 seconds to update enrolled courses list
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    // Show error message
                                    $('#available-courses .card-body').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error);
                                console.error('Response:', xhr.responseText);
                                $('#available-courses .card-body').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">An error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                            }
                        });
                    });
                });
            </script>

        <?php elseif ($role == 'teacher'): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Your Taught Courses</h5>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?= esc($course['id']) ?></td>
                                            <td><?= esc($course['title']) ?></td>
                                            <td><?= esc($course['description']) ?></td>
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
