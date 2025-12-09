<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Create Assignment
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create Assignment</h4>
                <small class="text-muted">Course: <?= esc($course['title']) ?></small>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="course_id" value="<?= esc($course_id) ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('title') ? 'is-invalid' : '' ?>" 
                               id="title" name="title" value="<?= old('title') ?>" required>
                        <?php if (isset($validation) && $validation->hasError('title')): ?>
                            <div class="invalid-feedback">
                                <?= esc($validation->getError('title')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Instructions / Description</label>
                        <textarea class="form-control <?= isset($validation) && $validation->hasError('description') ? 'is-invalid' : '' ?>" 
                                  id="description" name="description" rows="5"><?= old('description') ?></textarea>
                        <?php if (isset($validation) && $validation->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= esc($validation->getError('description')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control <?= isset($validation) && $validation->hasError('due_date') ? 'is-invalid' : '' ?>" 
                               id="due_date" name="due_date" value="<?= old('due_date') ?>" required>
                        <?php if (isset($validation) && $validation->hasError('due_date')): ?>
                            <div class="invalid-feedback">
                                <?= esc($validation->getError('due_date')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" 
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar">
                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG, ZIP, RAR (Max: 50MB)</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('assignments/course/' . $course_id) ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


