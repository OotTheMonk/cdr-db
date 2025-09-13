<?php


class CDR {
	private $rawString;
	private $id;
	private $mnc;
	private $bytes_used;
	private $dmcc;
	private $cellid;
	private $ip;

	public function __construct(string $rawString) {
		$this->rawString = $rawString;
		$parts = explode(',', $rawString);
		   if (count($parts) < 2) {
			   throw new \InvalidArgumentException('Invalid CDR string: must have at least two comma-separated values.');
		   }
		$this->id = (int)$parts[0];
		$idStr = (string)$parts[0];
		$lastChar = substr($idStr, -1);
		if ($lastChar === '4') {
			$this->ExtendedParsing();
		} elseif ($lastChar === '6') {
			$this->HexParsing();
		} else {
			$this->BasicParsing();
		}
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
		$this->id = (int)$parts[0];
		$this->dmcc = $parts[1];
		// Validate integer fields using is_numeric(trim())
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
		$this->id = (int)$parts[0];
		$hex = isset($parts[1]) ? $parts[1] : '';
		if (strlen($hex) !== 24) {
			// Not enough hex data
			$this->mnc = null;
			$this->bytes_used = null;
			$this->cellid = null;
			$this->ip = null;
			$this->dmcc = null;
			return;
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

