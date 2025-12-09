<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Submissions - <?= esc($assignment['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Submissions</h2>
                <h5 class="text-muted"><?= esc($assignment['title']) ?></h5>
                <small class="text-muted">Course: <?= esc($assignment['course_title']) ?></small>
            </div>
            <a href="<?= site_url('assignments/course/' . $assignment['course_id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Assignment Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Due Date:</strong> <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?></p>
                <?php if ($assignment['description']): ?>
                    <p><strong>Description:</strong> <?= esc($assignment['description']) ?></p>
                <?php endif; ?>
                <?php if ($assignment['attachment_name']): ?>
                    <p>
                        <strong>Attachment:</strong> 
                        <a href="<?= site_url('assignments/download-attachment/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> <?= esc($assignment['attachment_name']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Student Submissions</h5>
            </div>
            <div class="card-body">
                <?php if (empty($submissions) && empty($nonSubmittingStudents)): ?>
                    <p class="text-center text-muted py-4">No students enrolled in this course.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Submission</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($submissions as $submission): ?>
                                    <tr>
                                        <td><?= esc($submission['student_name']) ?></td>
                                        <td><?= esc($submission['student_email']) ?></td>
                                        <td>
                                            <?php if ($submission['submission_file_name']): ?>
                                                <a href="<?= site_url('assignments/download-submission/' . $submission['id']) ?>" class="btn btn-sm btn-outline-primary mb-1">
                                                    <i class="fas fa-download"></i> <?= esc($submission['submission_file_name']) ?>
                                                </a>
                                                <br>
                                            <?php endif; ?>
                                            <?php if (!empty($submission['submission_notes'])): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-file-alt"></i> Text submission
                                                    <button type="button" class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#textModal<?= $submission['id'] ?>">
                                                        View
                                                    </button>
                                                </small>
                                                <!-- Text Submission Modal -->
                                                <div class="modal fade" id="textModal<?= $submission['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Text Submission - <?= esc($submission['student_name']) ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="border p-3 bg-light rounded">
                                                                    <?= nl2br(esc($submission['submission_notes'])) ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php elseif (!$submission['submission_file_name']): ?>
                                                <span class="text-muted">No submission</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $submission['submitted_at'] ? date('M d, Y H:i', strtotime($submission['submitted_at'])) : '-' ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $submission['status'];
                                            $badgeClass = $status === 'graded' ? 'bg-success' : ($status === 'submitted' ? 'bg-primary' : 'bg-secondary');
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                                        </td>
                                        <td>
                                            <?= $submission['score'] !== null ? number_format($submission['score'], 2) . '%' : '-' ?>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('assignments/grade/' . $submission['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> <?= $submission['score'] !== null ? 'Update Grade' : 'Grade' ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php foreach ($nonSubmittingStudents as $student): ?>
                                    <tr class="table-warning">
                                        <td><?= esc($student['name']) ?></td>
                                        <td><?= esc($student['email']) ?></td>
                                        <td><span class="text-muted">-</span></td>
                                        <td><span class="text-muted">-</span></td>
                                        <td><span class="badge bg-warning">Not Submitted</span></td>
                                        <td><span class="text-muted">-</span></td>
                                        <td><span class="text-muted">-</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

