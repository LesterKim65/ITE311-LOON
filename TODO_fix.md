# TODO: Fix @enrollmentable and @materialtable

## Step 1: Create Migration to Alter Enrollments Table
- [ ] Generate migration file: `php spark make:migration AlterEnrollmentsTable`
- [ ] Edit the migration file to:
  - Rename 'enrollment_date' to 'enrolled_at'
  - Add 'status' VARCHAR(20) DEFAULT 'active'
- [ ] Define down() method to reverse changes

## Step 2: Create Migration to Alter Materials Table
- [ ] Generate migration file: `php spark make:migration AlterMaterialsTable`
- [ ] Edit the migration file to add 'status' VARCHAR(20) DEFAULT 'active'
- [ ] Define down() method to drop the 'status' column

## Step 3: Update MaterialModel
- [ ] Edit `app/Models/MaterialModel.php` to include 'status' in allowedFields

## Step 4: Update Materials Controller
- [ ] Edit `app/Controllers/Materials.php` upload method to set 'status' => 'active' in data array

## Step 5: Run Migrations
- [ ] Run `php spark migrate` to apply the changes

## Step 6: Test Functionality
- [ ] Test enrollment functionality
- [ ] Test material upload/download
