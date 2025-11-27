<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['title', 'description', 'instructor_id', 'created_at', 'updated_at'];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    /**
     * Apply LIKE filters to the query builder when a search term is provided.
     */
    public function applySearchFilter(?string $term): self
    {
        if (!empty($term)) {
            $this->groupStart()
                ->like('title', $term)
                ->orLike('description', $term)
                ->groupEnd();
        }

        return $this;
    }
}

