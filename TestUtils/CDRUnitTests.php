<?php
require_once __DIR__ . '/../Classes/CDR.php';

function htmlEscape($str) {
	return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$tests = [
	["9991,2935", ["id"=>9991, "mnc"=>null, "bytes_used"=>2935, "dmcc"=>null, "cellid"=>null, "ip"=>null], "Basic parsing (2 fields)"],
	["7291,293451", ["id"=>7291, "mnc"=>null, "bytes_used"=>293451, "dmcc"=>null, "cellid"=>null, "ip"=>null], "Basic parsing (large bytes)"],
	["4,0d39f,0,495594,214", ["id"=>4, "mnc"=>0, "bytes_used"=>495594, "dmcc"=>"0d39f", "cellid"=>214, "ip"=>null], "Extended parsing (5 fields)"],
	["7194,b33,394,495593,192", ["id"=>7194, "mnc"=>394, "bytes_used"=>495593, "dmcc"=>"b33", "cellid"=>192, "ip"=>null], "Extended parsing (all fields)"],
	["16,be833279000000c063e5e63d", [
		"id"=>16,
		"mnc"=>hexdec("be83"),
		"bytes_used"=>hexdec("3279"),
		"cellid"=>hexdec("000000c0"),
		"ip"=>"99.229.230.61",
		"dmcc"=>null
	], "Hex parsing (24 hex chars)"],
	["316,0e893279227712cac0014aff", [
		"id"=>316,
		"mnc"=>null,
		"bytes_used"=>null,
		"cellid"=>null,
		"ip"=>null,
		"dmcc"=>null
	], "Hex parsing (invalid hex length)"],
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
		$result["pass"] = ($actual == $expected);
		$allPassed = $allPassed && $result["pass"];
	} catch (Exception $e) {
		$result["exception"] = $e->getMessage();
		$allPassed = false;
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
