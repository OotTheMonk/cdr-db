<?php
// Endpoint: /api/cdr/upload.php
// Accepts a file upload and processes each line
require_once '../../Classes/CDR.php';
require_once '../../Repository/CDRRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $handle = fopen($file, 'r');
    $repo = new CDRRepository();
    //Note: In a real system, you would likely want to append or deduplicate instead of truncating
    $repo->truncateTable(); // Clear existing records before upload
    $results = [];
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line === '') continue;
        $fields = explode(',', $line);
        $id = $fields[0];
        $lastDigit = substr($id, -1);
    $cdr = new CDR($line);
        if ($cdr) {
            $repo->save($cdr);
            $results[] = $cdr->getNormalizedUsage();
        }
    }
    fclose($handle);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'records' => $results], JSON_PRETTY_PRINT);
    exit;
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded'], JSON_PRETTY_PRINT);
    exit;
}
