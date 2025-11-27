<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Courses
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Courses</h1>
        <p class="text-muted mb-0">Browse all courses and search instantly.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchForm" class="d-flex" method="get" action="<?= base_url('courses/search') ?>">
            <div class="input-group">
                <input
                    type="text"
                    id="searchInput"
                    class="form-control"
                    placeholder="Search courses..."
                    name="search_term"
                    value="<?= esc($searchTerm ?? '') ?>"
                >
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<div id="coursesContainer" class="row">
    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4 course-card">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= esc($course['title']) ?></h5>
                        <p class="card-text text-muted flex-grow-1">
                            <?= esc($course['description'] ?? 'No description available.') ?>
                        </p>
                        <a href="<?= base_url('courses/view/' . $course['id']) ?>" class="btn btn-primary mt-3">
                            View Course
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info mb-0">No courses found.</div>
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-12 d-none" id="clientNoMatch">
        <div class="alert alert-warning mb-0">No courses match your filter. Try a different keyword.</div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var $clientNoMatch = $('#clientNoMatch');

        function toggleClientMessage(currentTerm) {
            var totalCards = $('#coursesContainer .course-card').length;
            var visibleCards = $('#coursesContainer .course-card:visible').length;

            if (!totalCards) {
                $clientNoMatch.addClass('d-none');
                return;
            }

            if (currentTerm && visibleCards === 0) {
                $clientNoMatch.removeClass('d-none');
            } else {
                $clientNoMatch.addClass('d-none');
            }
        }

        // Client-side filtering
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.course-card').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
            toggleClientMessage(value.trim());
        });

        // Server-side search with AJAX
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var searchTerm = $('#searchInput').val();

            $.getJSON('<?= base_url('courses/search') ?>', { search_term: searchTerm }, function(data) {
                renderCourses(data);
                toggleClientMessage('');
            }).fail(function() {
                $('#coursesContainer').html('<div class="col-12"><div class="alert alert-danger">Unable to search courses at the moment.</div></div>');
                $clientNoMatch.addClass('d-none');
            });
        });

        function renderCourses(courses) {
            var container = $('#coursesContainer');
            container.empty();

            if (!courses || courses.length === 0) {
                container.html('<div class="col-12"><div class="alert alert-info mb-0">No courses found matching your search.</div></div>');
                return;
            }

            $.each(courses, function(index, course) {
                var description = course.description ? escapeHtml(course.description) : 'No description available.';
                var courseHtml = `
                    <div class="col-md-4 mb-4 course-card">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">${escapeHtml(course.title)}</h5>
                                <p class="card-text text-muted flex-grow-1">${description}</p>
                                <a href="<?= base_url('courses/view/') ?>${course.id}" class="btn btn-primary mt-3">View Course</a>
                            </div>
                        </div>
                    </div>`;
                container.append(courseHtml);
            });

            $clientNoMatch.addClass('d-none');
        }
    });
</script>
<?= $this->endSection() ?>

