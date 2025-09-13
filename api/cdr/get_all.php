<?php
// Returns all CDR records as JSON, exposing only primary data fields
require_once __DIR__ . '/../../Repository/CDRRepository.php';
require_once __DIR__ . '/../../Classes/CDR.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$repo = new CDRRepository();
$conn = (new ReflectionClass($repo))->getProperty('conn');
$conn->setAccessible(true);
$db = $conn->getValue($repo);

$result = $db->query('SELECT * FROM cdr WHERE softDeleted = 0');
$records = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cdr = CDR::fromDbRow($row);
        $records[] = $cdr->getNormalizedUsage();
    }
}
echo json_encode($records, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
