-- Migrate data from assignment_submissions to submissions table
-- Then drop assignment_submissions table

-- First, migrate the data
INSERT INTO submissions (
    user_id, 
    assignment_id, 
    submission_type,
    submission_file_path,
    submission_file_name,
    submission_notes,
    status,
    score,
    feedback,
    submitted_at,
    graded_at,
    created_at,
    updated_at
)
SELECT 
    student_id as user_id,
    assignment_id,
    'assignment' as submission_type,
    submission_file_path,
    submission_file_name,
    submission_notes,
    COALESCE(status, 'submitted') as status,
    grade as score,
    feedback,
    submitted_at,
    graded_at,
    created_at,
    updated_at
FROM assignment_submissions
WHERE NOT EXISTS (
    SELECT 1 FROM submissions s 
    WHERE s.user_id = assignment_submissions.student_id 
    AND s.assignment_id = assignment_submissions.assignment_id 
    AND s.submission_type = 'assignment'
);

-- Drop the old table
DROP TABLE IF EXISTS assignment_submissions;


