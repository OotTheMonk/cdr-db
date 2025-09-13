<?php

//Note: In addition to unit tests, this file also contains regression tests. They are included together for simplicity.

require_once __DIR__ . '/../Classes/CDR.php';

function htmlEscape($str) {
	return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$tests = [
   //ID Parsing unit tests
   ["", "exception", "Null input (expect exception)"],
   ["fred", "exception", "String id (expect exception)"],
   ["fred1,123", "exception", "String id (expect exception)"],
   // Basic parsing unit tests
   ["1111", "exception", "Basic parsing (id only, expect exception)"],
   ["1111,", "exception", "Basic parsing (id and comma, expect exception)",],
   ["1111,fred", "exception", "Basic parsing (id and string bytes, expect exception)",],
   ["1111,123,456", "exception", "Basic parsing (must be exactly two numbers, expect exception)",],
   ["9991,2935", ["id"=>9991, "mnc"=>null, "bytes_used"=>2935, "dmcc"=>null, "cellid"=>null, "ip"=>null], "Basic parsing (2 fields)"],
   ["7291,293451", ["id"=>7291, "mnc"=>null, "bytes_used"=>293451, "dmcc"=>null, "cellid"=>null, "ip"=>null], "Basic parsing (large bytes)"],
   // Extended parsing unit tests
   ["4,0d39f,0,495594,214", ["id"=>4, "mnc"=>0, "bytes_used"=>495594, "dmcc"=>"0d39f", "cellid"=>214, "ip"=>null], "Extended parsing (5 fields)"],
   ["7194,b33,394,495593,192", ["id"=>7194, "mnc"=>394, "bytes_used"=>495593, "dmcc"=>"b33", "cellid"=>192, "ip"=>null], "Extended parsing (all fields)"],
   // Hex parsing unit tests
   ["316,0e893279227712cac0014af", [
	   "id"=>316,
	   "mnc"=>null,
	   "bytes_used"=>null,
	   "cellid"=>null,
	   "ip"=>null,
	   "dmcc"=>null
   ], "Hex parsing (Too few hex chars)"],
   ["316,0e893279227712cac0014afff", [
	   "id"=>316,
	   "mnc"=>null,
	   "bytes_used"=>null,
	   "cellid"=>null,
	   "ip"=>null,
	   "dmcc"=>null
   ], "Hex parsing (Too many hex chars)"],
   ["16,be833279000000c063e5e63d", [
	   "id"=>16,
	   "mnc"=>hexdec("be83"),
	   "bytes_used"=>hexdec("3279"),
	   "cellid"=>hexdec("000000c0"),
	   "ip"=>"99.229.230.61",
	   "dmcc"=>null
   ], "Hex parsing (Example 1)"],
   ["316,0e893279227712cac0014aff", [
	   "id"=>316,
	   "mnc"=>3721,
	   "bytes_used"=>12921,
	   "cellid"=>578228938,
	   "ip"=>"192.1.74.255",
	   "dmcc"=>null
   ], "Hex parsing (Example 2)"],
   //Additional samples sourced from ChatGPT
   ["1006,20d2a225eb0ca1c43e198ce5", [
	   "id"=>1006,
	   "mnc"=>0x20d2,
	   "bytes_used"=>0xa225,
	   "cellid"=>0xeb0ca1c4,
	   "ip"=>"62.25.140.229",
	   "dmcc"=>null
   ], "Hex parsing (Example 3)"],
   ["1006,1003be786b0af0d530175326", [
	   "id"=>1006,
	   "mnc"=>0x1003,
	   "bytes_used"=>0xbe78,
	   "cellid"=>0x6b0af0d5,
	   "ip"=>"48.23.83.38",
	   "dmcc"=>null
   ], "Hex parsing (Example 4)"],
   ["1006,05a37f5aa55d537dc66c07b8", [
	   "id"=>1006,
	   "mnc"=>0x05a3,
	   "bytes_used"=>0x7f5a,
	   "cellid"=>0xa55d537d,
	   "ip"=>"198.108.7.184",
	   "dmcc"=>null
   ], "Hex parsing (Example 5)"],
   ["1006,166d3700af12361c57d06d48", [
	   "id"=>1006,
	   "mnc"=>0x166d,
	   "bytes_used"=>0x3700,
	   "cellid"=>0xaf12361c,
	   "ip"=>"87.208.109.72",
	   "dmcc"=>null
   ], "Hex parsing (Example 6)"],
   ["1006,07b1087118403e710616f9e2", [
	   "id"=>1006,
	   "mnc"=>0x07b1,
	   "bytes_used"=>0x0871,
	   "cellid"=>0x18403e71,
	   "ip"=>"6.22.249.226",
	   "dmcc"=>null
   ], "Hex parsing (Example 7)"],
   ["1006,082b9c0a506cb94fefad1760", [
	   "id"=>1006,
	   "mnc"=>0x082b,
	   "bytes_used"=>0x9c0a,
	   "cellid"=>0x506cb94f,
	   "ip"=>"239.173.23.96",
	   "dmcc"=>null
   ], "Hex parsing (Example 8)"],
   ["1006,1a9358b5dadff84ce0d75701", [
	   "id"=>1006,
	   "mnc"=>0x1a93,
	   "bytes_used"=>0x58b5,
	   "cellid"=>0xdadff84c,
	   "ip"=>"224.215.87.1",
	   "dmcc"=>null
   ], "Hex parsing (Example 9)"],
   ["1006,009db6d1552985948a0da3c0", [
	   "id"=>1006,
	   "mnc"=>0x009d,
	   "bytes_used"=>0xb6d1,
	   "cellid"=>0x55298594,
	   "ip"=>"138.13.163.192",
	   "dmcc"=>null
   ], "Hex parsing (Example 10)"],
   ["1006,1794bcb38836413866736354", [
	   "id"=>1006,
	   "mnc"=>0x1794,
	   "bytes_used"=>0xbcb3,
	   "cellid"=>0x88364138,
	   "ip"=>"102.115.99.84",
	   "dmcc"=>null
   ], "Hex parsing (Example 11)"],
   ["1006,022917fcefe97fad15f3f44e", [
	   "id"=>1006,
	   "mnc"=>0x0229,
	   "bytes_used"=>0x17fc,
	   "cellid"=>0xefe97fad,
	   "ip"=>"21.243.244.78",
	   "dmcc"=>null
   ], "Hex parsing (Example 12)"],
];

$results = [];
$allPassed = true;
foreach ($tests as $test) {
   list($input, $expected, $desc) = $test;
   $result = ["desc" => $desc, "pass" => false, "input" => $input, "expected" => $expected, "actual" => null, "exception" => null];
   try {
	   $cdr = new CDR($input);
	   $actual = $cdr->getNormalizedUsage();
	   $result["actual"] = $actual;
	   if ($expected === "exception") {
		   $result["pass"] = false;
	   } else {
		   $result["pass"] = ($actual == $expected);
		   $allPassed = $allPassed && $result["pass"];
	   }
   } catch (Exception $e) {
	   $result["exception"] = $e->getMessage();
	   if ($expected === "exception") {
		   $result["pass"] = true;
		   $allPassed = $allPassed && true;
	   } else {
		   $result["pass"] = false;
		   $allPassed = false;
	   }
   }
   $results[] = $result;
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CDR Unit Tests</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f8f8f8; color: #222; }
		table { border-collapse: collapse; margin: 2em auto; background: #fff; box-shadow: 0 2px 8px #ccc; }
		th, td { padding: 0.7em 1.2em; border: 1px solid #ddd; }
		th { background: #f0f0f0; }
		.pass { color: #2e7d32; font-weight: bold; }
		.fail { color: #c62828; font-weight: bold; }
		.icon { font-size: 1.3em; }
		.details { font-size: 0.95em; color: #444; }
		.summary { text-align: center; font-size: 1.2em; margin-top: 2em; }
	</style>
</head>
<body>
	<h2 style="text-align:center">CDR Unit Tests</h2>
	<table>
		<tr>
			<th>Test</th>
			<th>Status</th>
			<th>Input</th>
			<th>Expected</th>
			<th>Actual</th>
		</tr>
		<?php foreach ($results as $r): ?>
		<tr>
			<td><?= htmlEscape($r["desc"]) ?></td>
			<td class="<?= $r["pass"] ? 'pass' : 'fail' ?> icon">
				<?= $r["pass"] ? '✔' : '✘' ?>
				<?php if ($r["exception"]): ?>
					<div class="details">Exception: <?= htmlEscape($r["exception"]) ?></div>
				<?php endif; ?>
			</td>
			<td><code><?= htmlEscape($r["input"]) ?></code></td>
			<td><code><?= htmlEscape(json_encode($r["expected"])) ?></code></td>
			<td><code><?= htmlEscape(json_encode($r["actual"])) ?></code></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<div class="summary">
		<?php if ($allPassed): ?>
			<span class="pass">All tests passed!</span>
		<?php else: ?>
			<span class="fail">Some tests failed.</span>
		<?php endif; ?>
	</div>
</body>
</html>
