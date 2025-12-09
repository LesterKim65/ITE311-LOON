<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Assignments - <?= esc($course['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Assignments - <?= esc($course['title']) ?></h2>
            <a href="<?= site_url('assignments/create/' . $course['id']) ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Assignment
            </a>
        </div>

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

        <?php if (empty($assignments)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No assignments yet</h5>
                    <p class="text-muted">Create your first assignment for this course</p>
                    <a href="<?= site_url('assignments/create/' . $course['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?= esc($assignment['title']) ?></h5>
                                <span class="badge bg-info">
                                    <?= date('M d, Y', strtotime($assignment['due_date'])) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <?= esc(substr($assignment['description'] ?? 'No description', 0, 150)) ?>
                                    <?= strlen($assignment['description'] ?? '') > 150 ? '...' : '' ?>
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                    </small>
                                </div>
                                <?php if ($assignment['attachment_name']): ?>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-paperclip"></i> <?= esc($assignment['attachment_name']) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="<?= site_url('assignments/submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Submissions
                                </a>
                                <?php if ($assignment['attachment_name']): ?>
                                    <a href="<?= site_url('assignments/download-attachment/' . $assignment['id']) ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-download"></i> Download Attachment
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


