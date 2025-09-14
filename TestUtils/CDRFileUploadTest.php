
<!DOCTYPE html>
<html>
<head>
    <title>CDR File Upload Test</title>
    <style>
        .error { color: red; }
        #upload-status { margin-top: 1em; }
    </style>
</head>
<body>
<h2>CDR File Upload Test</h2>
<form id="cdr-upload-form">
    <label>Choose test file:
        <select id="test-file-select">
            <option value="test_file.txt">Test File</option>
            <option value="bad_test_file.txt">Bad Test File</option>
            <option value="invalid_test_file.txt">Invalid Test File</option>
        </select>
    </label>
    <button type="submit">Upload Selected Test File</button>
</form>
<div id="upload-status"></div>
<script>
const apiUrl = '../api/cdr/upload.php';
const testFiles = {
    'test_file.txt': 'test_file.txt',
    'bad_test_file.txt': 'bad_test_file.txt',
    'invalid_test_file.txt': 'invalid_test_file.txt'
};

document.getElementById('cdr-upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const statusDiv = document.getElementById('upload-status');
    statusDiv.textContent = 'Uploading...';
    const select = document.getElementById('test-file-select');
    const fileName = select.value;
    try {
        // Fetch the file as a blob from the server (TestUtils folder)
        const fileResponse = await fetch(fileName);
        if (!fileResponse.ok) throw new Error('Could not load test file: ' + fileName);
        const fileBlob = await fileResponse.blob();
        const formData = new FormData();
        formData.append('file', fileBlob, fileName);
        const response = await fetch(apiUrl, {
            method: 'POST',
            body: formData
        });
        const text = await response.text();
        if (response.ok) {
            statusDiv.textContent = 'Upload successful: ' + text;
        } else {
            statusDiv.textContent = 'Upload failed: ' + text;
        }
    } catch (err) {
        statusDiv.textContent = 'Error: ' + err;
    }
});
</script>
</body>
</html>
