<?php
// Simple debug page to test material upload
session_start();

// Set a test session for debugging
if (!isset($_SESSION['isLoggedIn'])) {
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['role'] = 'teacher';
    $_SESSION['id'] = 1;
}

echo "<h1>Material Upload Debug Page</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
$db = new mysqli('localhost', 'root', '', 'lms_loon');
if($db->connect_error) {
    echo "<p style='color:red'>Database connection failed: " . $db->connect_error . "</p>";
} else {
    echo "<p style='color:green'>Database connected successfully!</p>";
    
    // Check materials table structure
    $result = $db->query('DESCRIBE materials');
    if($result) {
        echo "<h3>Materials Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check current materials count
    $result = $db->query("SELECT COUNT(*) as count FROM materials");
    $row = $result->fetch_assoc();
    echo "<p>Current materials in table: " . $row['count'] . "</p>";
}

// Test file upload
echo "<h2>File Upload Test</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Attempt:</h3>";
    
    $file = $_FILES['test_file'];
    echo "<p>File name: " . $file['name'] . "</p>";
    echo "<p>File size: " . $file['size'] . " bytes</p>";
    echo "<p>File error: " . $file['error'] . "</p>";
    echo "<p>File temp name: " . $file['tmp_name'] . "</p>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Test upload directory
        $uploadPath = 'writable/uploads/materials/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
            echo "<p>Created upload directory: " . $uploadPath . "</p>";
        }
        
        $newName = uniqid() . '_' . $file['name'];
        $fullPath = $uploadPath . $newName;
        
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            echo "<p style='color:green'>File uploaded successfully to: " . $fullPath . "</p>";
            
            // Test database insert
            $data = [
                'course_id' => 1,
                'file_name' => $file['name'],
                'file_path' => $fullPath,
                'instructor_id' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $sql = "INSERT INTO materials (course_id, file_name, file_path, instructor_id, created_at) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("issis", $data['course_id'], $data['file_name'], $data['file_path'], $data['instructor_id'], $data['created_at']);
            
            if ($stmt->execute()) {
                echo "<p style='color:green'>Database insert successful! Material ID: " . $db->insert_id . "</p>";
            } else {
                echo "<p style='color:red'>Database insert failed: " . $db->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red'>File upload failed!</p>";
        }
    } else {
        echo "<p style='color:red'>File upload error: " . $file['error'] . "</p>";
    }
}

// Show upload form
echo "<h3>Test Upload Form:</h3>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file' required><br><br>";
echo "<button type='submit'>Upload Test File</button>";
echo "</form>";

// Show current materials
echo "<h2>Current Materials in Database:</h2>";
$result = $db->query("SELECT * FROM materials ORDER BY created_at DESC");
if($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Course ID</th><th>File Name</th><th>File Path</th><th>Instructor ID</th><th>Created At</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['course_id'] . "</td>";
        echo "<td>" . $row['file_name'] . "</td>";
        echo "<td>" . $row['file_path'] . "</td>";
        echo "<td>" . $row['instructor_id'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No materials found in database.</p>";
}

$db->close();
?>
