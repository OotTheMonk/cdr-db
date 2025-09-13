<?php
//IMPORTANT TODO: This file is not authenticated. This is a database entry point and should be strongly protected.
//IMPORTANT TODO: dmcc is an unvalidated string. The specification document does not specify what dmcc is, and so could be code or other malicious content that could create possible attack vectors.
//  We should validate the possible range of values for dmcc and reject anything outside that range as soon as possible.
//IMPORTANT TODO: Validate Apache config to ensure this and other APIs do not leak server information
//  e.g. # in httpd.conf or apache2.conf
//         ServerTokens Prod
//         ServerSignature Off

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
            $cdr = CDR::fromRawString($line);
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
    $message = "Upload complete. Failed lines: $exceptionCount.";
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'records' => $results,
        'message' => $message
    ], JSON_PRETTY_PRINT);
    exit;
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded'], JSON_PRETTY_PRINT);
    exit;
}
