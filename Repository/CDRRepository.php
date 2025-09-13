<?php
// Repository/CDRRepository.php
// Handles all database operations for CDR objects.

require_once __DIR__ . '/../Database/ConnectionManager.php';
require_once __DIR__ . '/../Classes/CDR.php';

class CDRRepository {
    private $conn;

    public function __construct() {
        $this->conn = ConnectionManager::getConnection();
    }

    public function save(CDR $cdr) {
        // Insert or update logic based on uniqueId
        $sql = "REPLACE INTO cdr (uniqueId, timeReceived, softDeleted, id, mnc, bytes_used, dmcc, cellid, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'isiiissis',
            $cdr->uniqueId,
            $cdr->timeReceived,
            $cdr->softDeleted,
            $cdr->id,
            $cdr->mnc,
            $cdr->bytes_used,
            $cdr->dmcc,
            $cdr->cellid,
            $cdr->ip
        );
        return $stmt->execute();
    }

    public function findById($uniqueId) {
        $sql = "SELECT * FROM cdr WHERE uniqueId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $uniqueId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return CDR::fromArray($row);
        }
        return null;
    }

    // Add more methods as needed (delete, findAll, etc.)
}
