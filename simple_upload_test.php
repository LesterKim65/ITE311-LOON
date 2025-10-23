<?php
// Simple upload test with better error handling
error_reporting(0); // Disable error display
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
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
                    // Test database insert
                    $db = new mysqli('localhost', 'root', '', 'lms_loon');
                    if ($db->connect_error) {
                        $response = ['success' => false, 'message' => 'Database connection failed: ' . $db->connect_error];
                    } else {
                        $sql = "INSERT INTO materials (course_id, file_name, file_path, instructor_id, created_at) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $db->prepare($sql);
                        $stmt->bind_param("issis", 1, $file['name'], $fullPath, 1, date('Y-m-d H:i:s'));
                        
                        if ($stmt->execute()) {
                            $response = [
                                'success' => true, 
                                'message' => 'Upload successful', 
                                'id' => $db->insert_id,
                                'file_name' => $file['name'],
                                'file_path' => $fullPath
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
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        input[type="file"] { margin: 10px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Simple Upload Test</h1>
    <p>This bypasses CodeIgniter completely to test the upload functionality.</p>
    
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="material_file">Select File:</label>
            <input type="file" id="material_file" name="material_file" required>
        </div>
        <button type="submit">Upload Test File</button>
    </form>
    
    <div id="result" class="result" style="display: none;"></div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('material_file');
            formData.append('material_file', fileInput.files[0]);
            
            console.log('Sending request to:', window.location.pathname);
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        throw new Error('Server returned HTML instead of JSON: ' + text.substring(0, 200));
                    });
                }
            })
            .then(data => {
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Result:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Error:</h3><pre>' + error.message + '</pre>';
            });
        });
    </script>
</body>
</html>








