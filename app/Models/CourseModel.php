<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['course_code', 'title', 'description', 'school_year', 'semester', 'schedule', 'instructor_id', 'status', 'start_date', 'end_date', 'created_at', 'updated_at'];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    /**
     * Apply LIKE filters to the query builder when a search term is provided.
     */
    public function applySearchFilter(?string $term): self
    {
        if (!empty($term)) {
            $this->groupStart()
                ->like('courses.title', $term)
                ->orLike('courses.description', $term)
                ->orLike('courses.course_code', $term)
                ->orLike('users.name', $term)
                ->groupEnd();
        }

        return $this;
    }
}

