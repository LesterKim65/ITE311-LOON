<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Admin - Assignment Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Assignment Management</h1>
            <p class="text-muted mb-0">View all assignments across the system.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">Total Assignments</h5>
                            <p class="card-text text-muted mb-0"><?= count($assignments) ?></p>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($assignments)): ?>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= esc($assignment['title']) ?></td>
                                    <td><?= esc($assignment['course_title']) ?></td>
                                    <td><?= esc($assignment['teacher_name']) ?></td>
                                    <td>
                                        <?php
                                        $description = esc($assignment['description'] ?? '');
                                        echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($assignment['due_date']): ?>
                                            <span class="badge bg-info"><?= esc(date('M d, Y H:i', strtotime($assignment['due_date']))) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">No due date</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php if ($assignment['created_at']): ?>
                                            <span class="text-muted small"><?= esc(date('M d, Y', strtotime($assignment['created_at']))) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No assignments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
