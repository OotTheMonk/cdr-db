<?php
// TestUtils/CDRFileUploadTest.php
// Visit this page in browser to upload a test file to the API

// Use absolute URL for API endpoint
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$apiUrl = $protocol . '://' . $host . '/cdr-db/api/cdr/upload.php';


$testFile = __DIR__ . '/test_file.txt';
$badTestFile = __DIR__ . '/bad_test_file.txt';
$invalidTestFile = __DIR__ . '/invalid_test_file.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_bad'])) {
        $fileToUpload = $badTestFile;
    } elseif (isset($_POST['upload_invalid'])) {
        $fileToUpload = $invalidTestFile;
    } else {
        $fileToUpload = $testFile;
    }
    if (!file_exists($fileToUpload)) {
        echo "<pre>Test file not found: $fileToUpload</pre>";
        exit;
    }
    $ch = curl_init();
    $cfile = new CURLFile($fileToUpload);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // Get headers + body
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($err) {
        echo "<pre>cURL Error: $err</pre>";
    } else {
        // Separate headers and body
        $header_size = strpos($response, "\r\n\r\n");
        $body = substr($response, $header_size + 4);
        echo "<pre>API Response:\n" . htmlspecialchars($body) . "</pre>";
    }
    echo '<a href="?">Try Again</a>';
    exit;
}
?>
<!DOCTYPE html>
<html><body>
<h2>CDR File Upload Test</h2>
<form method="post">
    <button type="submit" name="upload_test">Upload Test File</button>
    <button type="submit" name="upload_bad">Upload Bad Test File</button>
    <button type="submit" name="upload_invalid">Upload Invalid Test File</button>
</form>
</body></html>
