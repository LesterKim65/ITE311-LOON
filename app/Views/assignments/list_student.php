<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
My Assignments
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <h2 class="mb-4">My Assignments</h2>

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
                    <h5 class="text-muted">No assignments found</h5>
                    <p class="text-muted">You don't have any assignments yet</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($assignments as $assignment): ?>
                    <?php
                    $dueDate = new \DateTime($assignment['due_date']);
                    $now = new \DateTime();
                    $isPastDue = $now > $dueDate;
                    $status = $assignment['status'] ?? 'not_submitted';
                    $submission = $assignment['submission'] ?? null;
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 <?= $isPastDue && $status !== 'graded' ? 'border-warning' : '' ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?= esc($assignment['title']) ?></h5>
                                <div>
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
                                    <span class="badge <?= $badgeClass ?> me-2">
                                        <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                    </span>
                                    <?php if ($isPastDue && $status !== 'graded'): ?>
                                        <span class="badge bg-warning">Past Due</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    <strong>Course:</strong> <?= esc($assignment['course_title']) ?>
                                </p>
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
                                        <a href="<?= site_url('assignments/download-attachment/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download Attachment
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($status === 'graded' && $submission): ?>
                                    <div class="alert alert-success mt-3">
                                        <h6><i class="fas fa-check-circle"></i> Graded</h6>
                                        <p class="mb-1"><strong>Grade:</strong> <?= number_format($submission['score'], 2) ?>%</p>
                                        <?php if ($submission['feedback']): ?>
                                            <p class="mb-0"><strong>Feedback:</strong> <?= esc($submission['feedback']) ?></p>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            Graded on: <?= $submission['graded_at'] ? date('M d, Y H:i', strtotime($submission['graded_at'])) : '-' ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <?php if ($status === 'not_submitted' || ($status === 'submitted' && !$isPastDue)): ?>
                                    <a href="<?= site_url('assignments/submit/' . $assignment['id']) ?>" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> <?= $status === 'submitted' ? 'Resubmit' : 'Submit Assignment' ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($status === 'submitted'): ?>
                                    <a href="<?= site_url('assignments/submit/' . $assignment['id']) ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i> View Submission
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

