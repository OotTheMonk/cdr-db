<?php


class CDR {
	private $rawString;
	private $uniqueId;
	private $id;
	private $mnc;
	private $bytes_used;
	private $dmcc;
	private $cellid;
	private $ip;

	public function __construct(string $rawString) {
		$this->rawString = $rawString;
	}

	// Private parsing methods
	private function BasicParsing() {
		// ...implementation...
	}

	private function ExtendedParsing() {
		// ...implementation...
	}

	private function HexParsing() {
		// ...implementation...
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

