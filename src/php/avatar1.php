<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Avatar Creator</title>
</head>
<body>
    <h2>Upload Your Face Image</h2>
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="file" id="fileInput" required><br><br>
        <input type="text" name="user_id" id="userIdInput" placeholder="Enter your User ID" required><br><br>
        <button type="submit">Upload</button>
    </form>

    <h3 id="result"></h3>

    <script>
        const form = document.getElementById('uploadForm');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            
            const response = await fetch('http://localhost/upload', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            document.getElementById('result').innerText = result.message;
            if (result.avatar_path) {
                document.getElementById('result').innerHTML += `<br><img src="/${result.avatar_path}" width="200"/>`;
            }
        });
    </script>
</body>
</html>
