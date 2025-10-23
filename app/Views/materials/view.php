<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Course Materials
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Course Materials</h4>
                    <a href="<?= site_url('admin/course/' . $course_id . '/upload') ?>" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload New Material
                    </a>
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

                    <?php if (empty($materials)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Materials Found</h5>
                            <p class="text-muted">Upload your first material to get started.</p>
                            <a href="<?= site_url('admin/course/' . $course_id . '/upload') ?>" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Material
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Upload Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materials as $material): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-file"></i>
                                                <strong><?= esc($material['file_name']) ?></strong>
                                            </td>
                                            <td><?= date('M j, Y g:i A', strtotime($material['created_at'])) ?></td>
                                            <td>
                                                <?php if (file_exists($material['file_path'])): ?>
                                                    <span class="badge bg-success">Available</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Missing</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (file_exists($material['file_path'])): ?>
                                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php endif; ?>
                                                <button onclick="deleteMaterial(<?= $material['id'] ?>)" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
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
</div>

<script>
function deleteMaterial(materialId) {
    if (confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url('materials/delete') ?>/' + materialId;
        
        // Add CSRF token if needed
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>



