<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Upload Material
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Upload Material for Course</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Debug flash messages -->
                    <div class="alert alert-info">
                        <strong>Flash Messages Debug:</strong><br>
                        Success: <?= session()->getFlashdata('success') ?: 'None' ?><br>
                        Error: <?= session()->getFlashdata('error') ?: 'None' ?><br>
                        Debug: <?= session()->getFlashdata('debug') ?: 'None' ?>
                    </div>

                    <?php
                    $userRole = session()->get('role');
                    $userId = session()->get('id');

                    // Check if user has courses assigned (like in Auth controller)
                    $db = \Config\Database::connect();
                    $hasCourses = $db->query("SELECT COUNT(*) as count FROM courses WHERE instructor_id = ?", [$userId])->getRow();
                    $isInstructor = $hasCourses && $hasCourses->count > 0;

                    // Only teachers, instructors, and users with assigned courses can upload materials
                    if ($userRole !== 'teacher' && $userRole !== 'instructor' && !$isInstructor) { ?>
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Access Restricted</h5>
                            <p>Only teachers, instructors, and course instructors can upload materials.</p>
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">Back to Dashboard</a>
                        </div>
                    <?php } else {
                        // Check if this teacher is the instructor of the course
                        $db = \Config\Database::connect();
                        $course = $db->query("SELECT instructor_id FROM courses WHERE id = ?", [$course_id])->getRow();

                        if (!$course || $course->instructor_id != $userId) { ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Access Restricted</h5>
                                <p>You can only upload materials to courses you teach.</p>
                                <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">Back to Dashboard</a>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <strong>Debug Info:</strong><br>
                                User ID: <?= session()->get('id') ?><br>
                                User Role: <?= session()->get('role') ?><br>
                                Course ID: <?= $course_id ?><br>
                                Is Instructor: <?= $isInstructor ? 'YES' : 'NO' ?><br>
                                Form Action: <?= site_url('admin/course/' . $course_id . '/upload') ?><br>
                                Current URL: <?= current_url() ?>
                            </div>
                            <form action="<?= site_url('admin/course/' . $course_id . '/upload') ?>" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="material_file" class="form-label">Select File</label>
                                    <input type="file" class="form-control" id="material_file" name="material_file" required>
                                    <div class="form-text">Allowed file types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG, ZIP. Maximum size: 50MB.</div>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload Material</button>
                                <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
                            </form>
                            
                            <script>
                                document.getElementById('material_file').addEventListener('change', function() {
                                    console.log('File selected:', this.files[0]);
                                });
                                
                                document.querySelector('form').addEventListener('submit', function(e) {
                                    console.log('Form submit event triggered');
                                    console.log('Form data:', new FormData(this));
                                });
                            </script>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
