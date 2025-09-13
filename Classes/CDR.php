<?php
//IMPORTANT TODO: dmcc is an unvalidated string. The specification document does not specify what dmcc is, and so could be code or other malicious content that could create possible attack vectors.
//  We should validate the possible range of values for dmcc and reject anything outside that range as soon as possible.

class CDR {
	private $uniqueId;
	private $timeReceived;
	private $softDeleted;
	private $id;
	private $mnc;
	private $bytes_used;
	private $dmcc;
	private $cellid;
	private $ip;
	private $rawString;

       // Hydrate from DB row
       public static function fromDbRow(array $row) {
	       $cdr = new self('', true);
	       $cdr->uniqueId = $row['uniqueId'] ?? null;
	       $cdr->timeReceived = $row['timeReceived'] ?? null;
	       $cdr->softDeleted = $row['softDeleted'] ?? null;
	       $cdr->id = $row['id'] ?? null;
	       $cdr->mnc = $row['mnc'] ?? null;
	       $cdr->bytes_used = $row['bytes_used'] ?? null;
	       $cdr->dmcc = $row['dmcc'] ?? null;
	       $cdr->cellid = $row['cellid'] ?? null;
	       $cdr->ip = $row['ip'] ?? null;
	       return $cdr;
       }

       public static function fromRawString(string $str) {
           return new self($str);
       }

       //Make constructor private to prevent accidental misuse of skipValidation parameter
       private function __construct(string $rawString, bool $skipValidation = false) {
            if($skipValidation) {
                return;
            }
	       $this->rawString = $rawString;
	       $this->uniqueId = null;//uniqueId of null means not yet stored in DB
	       $this->timeReceived = date('Y-m-d H:i:s');
	       $this->softDeleted = 0;
	       $parts = explode(',', $rawString);
	       if (count($parts) < 2) {
		       throw new \InvalidArgumentException('Invalid CDR string: must have at least two comma-separated values.');
	       }        
	       $this->id = (int)$parts[0];
	       if($this->id % 10 === 4) {
		       $this->ExtendedParsing();
	       } else if($this->id % 10 === 6) {
		       $this->HexParsing();
	       } else {
		       $this->BasicParsing();
	       }
       }

       //Public get functions
        public function getUniqueId() {
            return $this->uniqueId;
        }

        public function getSoftDeleted() {
            return $this->softDeleted;
        }

	// Private parsing methods
	   private function BasicParsing() {
		   // Format: <id>,<bytes_used>
		   $parts = explode(',', $this->rawString);
		   // Must have exactly two parts, both numeric
		   if (count($parts) !== 2 || !is_numeric(trim($parts[0])) || !is_numeric(trim($parts[1]))) {
			   throw new \InvalidArgumentException('Invalid Basic Parsing: must have exactly two numeric values.');
		   }
		   $this->id = (int)$parts[0];
		   $this->bytes_used = (int)$parts[1];
		   $this->mnc = null;
		   $this->dmcc = null;
		   $this->cellid = null;
		   $this->ip = null;
	   }

	private function ExtendedParsing() {
		// Format: <id>,<dmcc>,<mnc>,<bytes_used>,<cellid>
		$parts = explode(',', $this->rawString);
        //ASSUMPTION: "Fields are always in the same order" -> no optional fields
		if (count($parts) !== 5) {
			throw new \InvalidArgumentException('Invalid Extended Parsing: must have exactly five comma-separated values.');
		}
		if (!is_numeric(trim($parts[0]))) {
			throw new \InvalidArgumentException('Invalid Extended Parsing: id must be an integer.');
		}
		$this->id = (int)$parts[0];
		$this->dmcc = $parts[1];
		if (!is_numeric(trim($parts[2]))) {
			throw new \InvalidArgumentException('Invalid Extended Parsing: mnc must be an integer.');
		}
		if (!is_numeric(trim($parts[3]))) {
			throw new \InvalidArgumentException('Invalid Extended Parsing: bytes_used must be an integer.');
		}
		if (!is_numeric(trim($parts[4]))) {
			throw new \InvalidArgumentException('Invalid Extended Parsing: cellid must be an integer.');
		}
		$this->mnc = (int)$parts[2];
		$this->bytes_used = (int)$parts[3];
		$this->cellid = (int)$parts[4];
		$this->ip = null;
	}

	private function HexParsing() {
		// Format: <id>,<hex>
		$parts = explode(',', $this->rawString);
        //ASSUMPTION: The specification does not strictly require the hex parsing id to be numeric.
        // Since the specification of the normalized usage object has id as a number, I have chosen to enforce this here.
		if (!is_numeric(trim($parts[0]))) {
			throw new \InvalidArgumentException('Invalid Hex Parsing: id must be an integer.');
		}
		$this->id = (int)$parts[0];
		$hex = isset($parts[1]) ? $parts[1] : '';
		if (strlen($hex) !== 24) {
			// Not enough hex data
			throw new \InvalidArgumentException('Invalid Hex Parsing: hex string must be 24 characters.');
		}
		// Validate that the hex string contains only valid hex characters
		if (!preg_match('/^[0-9a-fA-F]{24}$/', $hex)) {
			throw new \InvalidArgumentException('Invalid Hex Parsing: hex string must contain only hexadecimal characters (0-9, a-f, A-F).');
		}
		// Bytes 1-2: mnc
		$this->mnc = hexdec(substr($hex, 0, 4));
		// Bytes 3-4: bytes_used
		$this->bytes_used = hexdec(substr($hex, 4, 4));
		// Bytes 5-8: cellid
		$this->cellid = hexdec(substr($hex, 8, 8));
		// Bytes 9-12: ip
		$ipHex = substr($hex, 16, 8);
		$ipParts = [];
		for ($i = 0; $i < 8; $i += 2) {
			$ipParts[] = strval(hexdec(substr($ipHex, $i, 2)));
		}
		$this->ip = implode('.', $ipParts);
		$this->dmcc = null;
	}

	// Returns a normalized usage object as an associative array
	public function getNormalizedUsage() {
		return [
			"id" => $this->id,
			"mnc" => $this->mnc,
			"bytes_used" => $this->bytes_used,
			"dmcc" => $this->dmcc,
			"cellid" => $this->cellid,
			"ip" => $this->ip
		];
	}
}

