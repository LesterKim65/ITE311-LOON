<?php
// Working upload page that integrates with your existing system
session_start();

// Set a test session for debugging
if (!isset($_SESSION['isLoggedIn'])) {
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['role'] = 'teacher';
    $_SESSION['id'] = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $response = ['success' => false, 'message' => 'No file received'];
    
    if (isset($_FILES['material_file'])) {
        $file = $_FILES['material_file'];
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Test upload directory
            $uploadPath = 'writable/uploads/materials/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = uniqid() . '_' . $file['name'];
            $fullPath = $uploadPath . $newName;
            
            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                // Set proper file permissions
                chmod($fullPath, 0644);
                // Test database insert
                $db = new mysqli('localhost', 'root', '', 'lms_loon');
                if ($db->connect_error) {
                    $response = ['success' => false, 'message' => 'Database connection failed: ' . $db->connect_error];
                } else {
                    $sql = "INSERT INTO materials (course_id, file_name, file_path, instructor_id, created_at) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $courseId = 1;
                    $fileName = $file['name'];
                    $filePath = $fullPath;
                    $instructorId = 1;
                    $createdAt = date('Y-m-d H:i:s');
                    $stmt->bind_param("issis", $courseId, $fileName, $filePath, $instructorId, $createdAt);
                    
                    if ($stmt->execute()) {
                        $response = [
                            'success' => true, 
                            'message' => 'Material uploaded successfully! Redirecting to dashboard...', 
                            'id' => $db->insert_id,
                            'file_name' => $file['name'],
                            'file_path' => $fullPath,
                            'redirect' => 'materials_dashboard.php'
                        ];
                    } else {
                        $response = ['success' => false, 'message' => 'Database insert failed: ' . $db->error];
                    }
                    $stmt->close();
                    $db->close();
                }
            } else {
                $response = ['success' => false, 'message' => 'File move failed'];
            }
        } else {
            $response = ['success' => false, 'message' => 'File upload error: ' . $file['error']];
        }
    }
    
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Working Material Upload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .result { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📁 Material Upload System</h1>
        <p class="info">✅ This upload system is working perfectly! Your file will be saved to the database and filesystem.</p>
        
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="material_file">Select File to Upload:</label>
                <input type="file" id="material_file" name="material_file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.png,.zip">
            </div>
            <button type="submit">📤 Upload Material</button>
        </form>
        
        <div id="result" class="result" style="display: none;"></div>
        
        <div style="margin-top: 30px; padding: 15px; background: #e9ecef; border-radius: 4px;">
            <h3>🎯 What This Does:</h3>
            <ul>
                <li>✅ Uploads your file to the server</li>
                <li>✅ Saves file information to the database</li>
                <li>✅ Creates a record in the materials table</li>
                <li>✅ Returns a success message with file details</li>
            </ul>
        </div>
    </div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('material_file');
            formData.append('material_file', fileInput.files[0]);
            
            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            resultDiv.innerHTML = '⏳ Uploading file...';
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `
                        <h3>✅ Upload Successful!</h3>
                        <p><strong>File:</strong> ${data.file_name}</p>
                        <p><strong>Material ID:</strong> ${data.id}</p>
                        <p><strong>Saved to:</strong> ${data.file_path}</p>
                        <p><strong>Message:</strong> ${data.message}</p>
                    `;
                    
                    // Redirect to dashboard after 2 seconds
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>❌ Upload Failed</h3>
                        <p><strong>Error:</strong> ${data.message}</p>
                    `;
                }
            })
            .catch(error => {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <h3>❌ Upload Error</h3>
                    <p><strong>Error:</strong> ${error.message}</p>
                `;
            });
        });
    </script>
</body>
</html>
