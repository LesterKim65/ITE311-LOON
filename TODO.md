# TODO: Implement Materials Management System

## Step 1: Create Database Migration for Materials Table
- [x] Generate migration file: `php spark make:migration CreateMaterialsTable`
- [x] Edit the migration file to define table structure:
  - id (INT, unsigned, auto_increment, primary key)
  - course_id (INT, unsigned, foreign key to courses.id with CASCADE)
  - file_name (VARCHAR(255))
  - file_path (VARCHAR(255))
  - created_at (DATETIME, null)
- [x] Define down() method to drop the 'materials' table
- [x] Run migration: `php spark migrate`
- [x] Create upload directory: `writable/uploads/materials/`

## Step 2: Create MaterialModel
- [x] Create file: `app/Models/MaterialModel.php`
- [x] Define class extending Model with:
  - table = 'materials'
  - primaryKey = 'id'
  - allowedFields = ['course_id', 'file_name', 'file_path', 'created_at']
  - useTimestamps = false
  - returnType = 'array'
- [x] Add insertMaterial($data) method: return $this->insert($data)
- [x] Add getMaterialsByCourse($course_id) method: return $this->where('course_id', $course_id)->findAll()

## Step 3: Create Materials Controller
- [x] Create file: `app/Controllers/Materials.php` extending BaseController
- [x] Add upload($course_id) method:
  - Check for POST request
  - Load upload and validation libraries
  - Configure upload preferences (path: writable/uploads/materials/, allowed types, max size)
  - Perform file upload
  - Save to database using MaterialModel
  - Set flash messages and redirect
- [x] Add delete($material_id) method:
  - Retrieve material record
  - Delete file from filesystem
  - Delete database record
  - Redirect with flash message
- [x] Add download($material_id) method:
  - Check user login and enrollment
  - Retrieve file path
  - Force file download

## Step 4: Implement File Upload Functionality
- [x] Ensure upload directory exists: `writable/uploads/materials/`
- [ ] Test upload functionality in upload() method

## Step 5: Create File Upload View
- [x] Create file: `app/Views/materials/upload.php`
- [x] Create form with enctype="multipart/form-data"
- [x] Add file input with accept attribute for allowed file types
- [x] Style with Bootstrap classes
- [x] Display flash messages

## Step 6: Display Downloadable Materials for Students
- [x] Modify `app/Views/auth/dashboard.php` in student section
- [x] Add new card section for "Course Materials"
- [x] Loop through enrolled courses
- [x] For each course, fetch materials using MaterialModel
- [x] Display file names with download links

## Step 7: Implement Download Method
- [x] Complete download($material_id) method in Materials controller
- [x] Use CodeIgniter's force_download helper

## Step 8: Update Routes
- [x] Add routes to `app/Config/Routes.php`:
  - $routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
  - $routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
  - $routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
  - $routes->get('/materials/download/(:num)', 'Materials::download/$1');
