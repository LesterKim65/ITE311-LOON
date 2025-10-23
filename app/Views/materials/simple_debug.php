<!DOCTYPE html>
<html>
<head>
    <title>Simple Debug Upload</title>
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
    <h1>Simple Debug Upload Test</h1>
    
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
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get as text
                    return response.text().then(text => {
                        throw new Error('Server returned HTML instead of JSON: ' + text.substring(0, 200));
                    });
                }
            })
            .then(data => {
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Success:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
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








