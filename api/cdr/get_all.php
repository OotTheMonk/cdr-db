<?php
// Returns all CDR records as JSON, exposing only primary data fields
require_once __DIR__ . '/../../Repository/CDRRepository.php';
require_once __DIR__ . '/../../Classes/CDR.php';

header('Content-Type: application/json');
// Prevent caching to ensure fresh data on each request
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$repo = new CDRRepository();
$records = $repo->getAll();
echo json_encode($records, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
