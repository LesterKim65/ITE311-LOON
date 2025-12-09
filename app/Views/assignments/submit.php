<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Submit Assignment
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Submit Assignment</h4>
                <small class="text-muted"><?= esc($assignment['title']) ?></small>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($isPastDue && !$existingSubmission): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This assignment is past due. You cannot submit it now.
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h5>Assignment Details</h5>
                    <p><strong>Course:</strong> <?= esc($assignment['course_title']) ?></p>
                    <p><strong>Due Date:</strong> <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?></p>
                    <?php if ($assignment['description']): ?>
                        <p><strong>Description:</strong></p>
                        <div class="border p-3 bg-light rounded">
                            <?= nl2br(esc($assignment['description'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($assignment['attachment_name']): ?>
                        <p class="mt-3">
                            <strong>Attachment:</strong> 
                            <a href="<?= site_url('assignments/download-attachment/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> <?= esc($assignment['attachment_name']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($existingSubmission): ?>
                    <div class="alert alert-info mb-4">
                        <h6><i class="fas fa-info-circle"></i> Previous Submission</h6>
                        <p class="mb-1"><strong>Submitted:</strong> <?= date('M d, Y H:i', strtotime($existingSubmission['submitted_at'])) ?></p>
                        <?php if ($existingSubmission['submission_file_name']): ?>
                            <p class="mb-1">
                                <strong>File:</strong> 
                                <a href="<?= site_url('assignments/download-submission/' . $existingSubmission['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> <?= esc($existingSubmission['submission_file_name']) ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <?php if ($existingSubmission['submission_notes']): ?>
                            <p class="mb-0">
                                <strong>Text Submission:</strong>
                                <div class="border p-2 bg-light rounded mt-2">
                                    <?= nl2br(esc($existingSubmission['submission_notes'])) ?>
                                </div>
                            </p>
                        <?php endif; ?>
                        <?php if ($existingSubmission['status'] === 'graded'): ?>
                            <hr>
                            <p class="mb-1"><strong>Grade:</strong> <?= number_format($existingSubmission['score'], 2) ?>%</p>
                            <?php if ($existingSubmission['feedback']): ?>
                                <p class="mb-0"><strong>Feedback:</strong> <?= esc($existingSubmission['feedback']) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isPastDue || $existingSubmission): ?>
                    <form method="POST" enctype="multipart/form-data" id="submissionForm">
                        <?= csrf_field() ?>

                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> You can submit either a file OR text, or both. At least one is required.
                        </div>

                        <div class="mb-3">
                            <label for="submission_file" class="form-label">Upload File (Optional)</label>
                            <input type="file" class="form-control" id="submission_file" name="submission_file" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar">
                            <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG, ZIP, RAR (Max: 50MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="submission_notes" class="form-label">Text Submission <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= isset($validation) && $validation->hasError('submission_notes') ? 'is-invalid' : '' ?>" 
                                      id="submission_notes" name="submission_notes" rows="8" 
                                      placeholder="Enter your assignment submission text here..."><?= old('submission_notes', $existingSubmission['submission_notes'] ?? '') ?></textarea>
                            <?php if (isset($validation) && $validation->hasError('submission_notes')): ?>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('submission_notes')) ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">You must provide either a file upload or text submission (or both).</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('assignments') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> <?= $existingSubmission ? 'Resubmit' : 'Submit Assignment' ?>
                            </button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('submissionForm').addEventListener('submit', function(e) {
                            var fileInput = document.getElementById('submission_file');
                            var textInput = document.getElementById('submission_notes');
                            var hasFile = fileInput.files && fileInput.files.length > 0;
                            var hasText = textInput.value.trim().length > 0;
                            
                            if (!hasFile && !hasText) {
                                e.preventDefault();
                                alert('Please provide either a file upload or text submission (or both).');
                                return false;
                            }
                        });
                    </script>
                <?php else: ?>
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('assignments') ?>" class="btn btn-secondary">Back to Assignments</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

