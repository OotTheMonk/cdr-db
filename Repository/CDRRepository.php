<?php
// Handles all database operations for CDR objects.
// Uses prepared statements to prevent SQL injection.

require_once __DIR__ . '/../Database/ConnectionManager.php';
require_once __DIR__ . '/../Classes/CDR.php';

class CDRRepository {
    private $conn;

    public function __construct() {
        $this->conn = GetLocalMySQLConnection();
    }

    public function save(CDR $cdr) {
        // Insert or update logic based on uniqueId
        $usage = $cdr->getNormalizedUsage();
        $uniqueId = $cdr->getUniqueId();
        $softDeleted = $cdr->getSoftDeleted();
        $id = $usage['id'];
        $mnc = $usage['mnc'];
        $bytes_used = $usage['bytes_used'];
        $dmcc = $usage['dmcc'];
        $cellid = $usage['cellid'];
        $ip = $usage['ip'];
        $sql = "REPLACE INTO cdr (uniqueId, softDeleted, id, mnc, bytes_used, dmcc, cellid, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'isiiisis',
            $uniqueId,
            $softDeleted,
            $id,
            $mnc,
            $bytes_used,
            $dmcc,
            $cellid,
            $ip
        );
        return $stmt->execute();
    }

    /**
     * Returns all non-soft-deleted CDRs as normalized arrays.
     * Returns empty array if DB connection or query fails.
     */
    public function getAll() {
        if (!$this->conn) {
            return [];
        }
        $result = $this->conn->query('SELECT * FROM cdr WHERE softDeleted = 0');
        if ($result === false) {
            return [];
        }
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $cdr = CDR::fromDbRow($row);
            $records[] = $cdr->getNormalizedUsage();
        }
        return $records;
    }

    //This is not used for the example project, but gives an example of what kind of retrieval methods you can add
    public function findById($uniqueId) {
        $sql = "SELECT * FROM cdr WHERE uniqueId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $uniqueId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return CDR::fromDbRow($row);
        }
        return null;
    }

    /**
     * Truncates (clears) all records from the cdr table.
     * NOTE: You would likely not want to include this in a production system.
     * This is mostly to support the spec of the assignment to upload a file -> See that file in the db
     */
    public function truncateTable() {
        $sql = "TRUNCATE TABLE cdr";
        return $this->conn->query($sql);
    }
}
