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
			"id" => 7294,
			"mnc" => 182,
			"bytes_used" => 293451,
			"dmcc" => null,
			"cellid" => 31194,
			"ip" => "192.168.0.1"
		];
	}
}

