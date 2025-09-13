<?php
//IMPORTANT : This file is not authenticated. This is a database entry point and should be strongly protected.

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
    $exceptionCount = 0;
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line === '') continue;
        //ASSUMPTION: If the file contains invalid lines, we skip them and continue processing
        // An alternative strategy would be to reject the entire file if any line is invalid
        try {
            $fields = explode(',', $line);
            $id = $fields[0];
            $lastDigit = substr($id, -1);
            $cdr = new CDR($line);
            if ($cdr) {
                $repo->save($cdr);
                $results[] = $cdr->getNormalizedUsage();
            }
        } catch (Exception $e) {
            //Note: It is bad practice to expose raw exception messages in a production system
            // This can leak information about the operation of the server to malicious actors
            // If we want exception messages for debugging, we should log them to the server where they are inaccessible from the internet
            $exceptionCount++;
        }
    }
    fclose($handle);
    header('Content-Type: application/json');
    $message = "Upload complete. Failed lines: $exceptionCount.";
    echo json_encode([
        'status' => 'success',
        'records' => $results,
        'message' => $message
    ], JSON_PRETTY_PRINT);
    exit;
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded'], JSON_PRETTY_PRINT);
    exit;
}
