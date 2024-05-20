<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload Example</title>
</head>
<body>
    <h1>Upload an Image</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" id="fileInput" name="image" accept="image/*">
        <button type="button" onclick="uploadImage()">Upload</button>
    </form>
    <p id="status"></p>

    <script>
        async function uploadImage() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            if (!file) {
                document.getElementById('status').innerText = 'No file selected.';
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('http://localhost/apidemo2/serve/upload.php', {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    const text = await response.text();
                    document.getElementById('status').innerText = 'Upload successful: ' + text;
                } else {
                    const text = await response.text();
                    document.getElementById('status').innerText = 'Upload failed: ' + text;
                }
            } catch (error) {
                console.error('Error uploading file:', error);
                document.getElementById('status').innerText = 'Upload failed.';
            }
        }
    </script>
</body>
</html>
