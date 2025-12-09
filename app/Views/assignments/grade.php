<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Grade Assignment
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Grade Assignment</h4>
                <small class="text-muted">Assignment: <?= esc($assignment['title']) ?></small>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h5>Student Information</h5>
                    <?php
                    $db = \Config\Database::connect();
                    $student = $db->query("SELECT name, email FROM users WHERE id = ?", [$submission['user_id']])->getRowArray();
                    ?>
                    <p><strong>Name:</strong> <?= esc($student['name']) ?></p>
                    <p><strong>Email:</strong> <?= esc($student['email']) ?></p>
                </div>

                <div class="mb-4">
                    <h5>Submission Details</h5>
                    <p><strong>Submitted:</strong> <?= $submission['submitted_at'] ? date('M d, Y H:i', strtotime($submission['submitted_at'])) : '-' ?></p>
                    <?php if ($submission['submission_file_name']): ?>
                        <p class="mb-2">
                            <strong>File:</strong> 
                            <a href="<?= site_url('assignments/download-submission/' . $submission['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> <?= esc($submission['submission_file_name']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($submission['submission_notes'])): ?>
                        <p class="mb-2"><strong>Text Submission:</strong></p>
                        <div class="border p-3 bg-light rounded mb-3">
                            <?= nl2br(esc($submission['submission_notes'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!$submission['submission_file_name'] && empty($submission['submission_notes'])): ?>
                        <p class="text-muted">No submission content available.</p>
                    <?php endif; ?>
                </div>

                <form method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (0-100) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control <?= isset($validation) && $validation->hasError('grade') ? 'is-invalid' : '' ?>" 
                               id="grade" name="grade" 
                               value="<?= old('grade', $submission['score']) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('grade')): ?>
                            <div class="invalid-feedback">
                                <?= esc($validation->getError('grade')) ?>
                            </div>
                        <?php endif; ?>
                        <small class="form-text text-muted">Enter a grade from 0 to 100 (can include decimals)</small>
                    </div>

                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback / Comments</label>
                        <textarea class="form-control <?= isset($validation) && $validation->hasError('feedback') ? 'is-invalid' : '' ?>" 
                                  id="feedback" name="feedback" rows="5"><?= old('feedback', $submission['feedback']) ?></textarea>
                        <?php if (isset($validation) && $validation->hasError('feedback')): ?>
                            <div class="invalid-feedback">
                                <?= esc($validation->getError('feedback')) ?>
                            </div>
                        <?php endif; ?>
                        <small class="form-text text-muted">Optional feedback for the student</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('assignments/submissions/' . $submission['assignment_id']) ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

