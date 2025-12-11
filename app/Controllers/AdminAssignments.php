<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use CodeIgniter\Controller;

class AdminAssignments extends Controller
{
    protected $assignmentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
    }

    public function index()
    {
        $data['assignments'] = $this->assignmentModel->getAllAssignmentsWithDetails();
        return view('admin/assignments/index', $data);
    }
}
