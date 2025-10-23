<!DOCTYPE html>
<html>
<head>
    <title>Debug Upload</title>
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
    <h1>Debug Material Upload</h1>
    
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
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Result:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Error:</h3><pre>' + error + '</pre>';
            });
        });
    </script>
</body>
</html>
