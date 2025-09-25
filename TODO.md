- [x] Modify app/Views/template/header.php to conditionally hide Home, About, Contact navigation links when on the dashboard page (/dashboard).
- [x] Test the dashboard to ensure links are hidden.
- [x] Test other pages to ensure links are still visible.

## Add Navigation for Student and Instructor

- [ ] Update app/Config/Routes.php to add routes for new navigation links: /enrolled-courses (CourseController::enrolled), /courses (CourseController::index), /create-course (CourseController::create), /manage-courses (CourseController::manage), /manage-users (Auth::manageUsers).

- [x] Update app/Views/template/header.php to add conditional navigation items for role == 'student': Enrolled Courses (/enrolled-courses), Browse Courses (/courses); for role == 'teacher': Create Course (/create-course) alongside Manage Courses.

- [ ] Test navigation rendering by logging in as student and teacher (suggest running php spark serve if needed).

- [ ] Note: New routes point to non-existent CourseController methods; create stubs or views if required in future steps.
