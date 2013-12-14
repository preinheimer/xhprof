<?php

// Set the configured locale for number formatting
setlocale(LC_NUMERIC, (isset($_xhprof['locale'])) ? $_xhprof['locale'] : 'en_US');

/**
 * Display a list of runs
 *
 * @param        $resultSet
 * @param string $title
 *
 * @return void
 */
function displayRuns($resultSet, $title = "") {
	echo "<div class=\"runTitle\">$title</div>\n";
	echo "<table id=\"box-table-a\" class=\"tablesorter\" summary=\"Stats\"><thead><tr><th>Timestamp</th><th>Domain</th><th>Cpu</th><th>Wall Time</th><th>Peak Memory Usage</th><th>URL</th><th>Simplified URL</th></tr></thead>";
	echo "<tbody>\n";
	while ($row = XHProfRuns_Default::getNextAssoc($resultSet)) {
		$c_url = urlencode($row['c_url']);
		$url = urlencode($row['url']);
		$html['url'] = htmlentities($row['url'], ENT_QUOTES, 'UTF-8');
		$html['c_url'] = htmlentities($row['c_url'], ENT_QUOTES, 'UTF-8');
		$date = strtotime($row['timestamp']);
		$date = date('M d H:i:s', $date);
		echo '   <tr><td><a href="?run=' . $row['id'] . '">' . $date . '</a><br /><span class="runid">' . $row['id'] . '</span></td><td>' .
			$row['server name'] . '</td><td data-sort-value="' . $row['cpu'] . '">' .
			printSeconds($row['cpu']) . '</td><td data-sort-value="' . $row['wt'] . '">' .
			printSeconds($row['wt']) . '</td><td data-sort-value="' . $row['pmu'] . '">' .
			printBytes($row['pmu']) . '</td><td><a href="?geturl=' . $url . '">' . $html['url'] . '</a></td><td><a href="?getcurl=' . $c_url . '">' . $html['c_url'] . '</a></td></tr>' . PHP_EOL;
	}
	echo "</tbody>\n";
	echo "</table>\n";
}

/**
 * Print a value as Bytes and add a span around the unit
 *
 * @param integer $size The size to format.
 * @param array   $sizes The list of size units.
 *
 * @return string
 */
function printBytes($size, $sizes = array(' B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB')) {
	if ($size == 0) {
		return ('<span class="unit">n/a</span>');
	}
	return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' <span class="unit">' . $sizes[$i] . '</span>');
}

/**
 * Print a value as Bytes without a span around the unit
 *
 * @param integer $size The size to format.
 * @param array   $sizes The list of size units.
 *
 * @return string
 */
function printBytesPlain($size, $sizes = array(' B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB')) {
	if ($size == 0) {
		return ('n/a');
	}
	return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
}

/**
 * Format an integer as a time value with a span around the unit
 *
 * @param integer $time The value to format
 *
 * @return string
 */
function printSeconds($time) {
	$suffix = 'μs';

	if ($time > 1000) {
		$time = $time / 1000;
		$suffix = 'ms';
	}

	if ($time > 1000) {
		$time = $time / 1000;
		$suffix = ' s';
	}

	if ($time > 60 && $suffix == ' s') {
		$time = $time / 60;
		$suffix = 'min!';
	}

	return sprintf("%.2f <span class=\"unit\">{$suffix}</span>", $time);
}

/**
 * Format an integer as a time value without a span around the unit
 *
 * @param integer $time The value to format
 *
 * @return string
 */
function printSecondsPlain($time) {
	$suffix = 'μs';

	if ($time > 1000) {
		$time = $time / 1000;
		$suffix = 'ms';
	}

	if ($time > 1000) {
		$time = $time / 1000;
		$suffix = ' s';
	}

	if ($time > 60 && $suffix == ' s') {
		$time = $time / 60;
		$suffix = 'min!';
	}

	return sprintf("%.2f {$suffix}", $time);
}


/**
 * @param      $rs
 * @param bool $flip
 *
 * @return array
 */
function showChart($rs, $flip = false) {
	// Used in chart.pthml
	global $_xhprof;

	$arCPU = array();
	$arWT = array();
	$arPEAK = array();
	$arIDS = array();
	$arDateIDs = array();
	$arDomains = array();
	$date = array();

	while ($row = XHProfRuns_Default::getNextAssoc($rs)) {
		$date[] = "'" . date("Y-m-d", $row['timestamp']) . "'";
		$arCPU[] = $row['cpu'];
		$arWT[] = $row['wt'];
		$arPEAK[] = $row['pmu'];
		$arIDS[] = $row['id'];
		$arDomains[] = $row['server name'];
		$arDateIDs[] = "'" . date("Y-m-d", $row['timestamp']) . " <br/> " . $row['id'] . "'";
	}

	$date = $flip ? array_reverse($date) : $date;
	$arCPU = $flip ? array_reverse($arCPU) : $arCPU;
	$arWT = $flip ? array_reverse($arWT) : $arWT;
	$arPEAK = $flip ? array_reverse($arPEAK) : $arPEAK;
	$arIDS = $flip ? array_reverse($arIDS) : $arIDS;
	$arDateIDs = $flip ? array_reverse($arDateIDs) : $arDateIDs;
	$arDomains = $flip ? array_reverse($arDomains) : $arDomains;

	$dateJS = implode(", ", $date);
	$cpuJS = implode(", ", $arCPU);
	$wtJS = implode(", ", $arWT);
	$pmuJS = implode(", ", $arPEAK);
	$idsJS = "'" . implode("', '", $arIDS) . "'";
	$dateidsJS = implode(", ", $arDateIDs);
	$domainsJS = "'" . implode("', '", $arDomains) . "'";

	ob_start();
	require("../xhprof_lib/templates/chart.phtml");
	$stuff = ob_get_contents();
	ob_end_clean();
	return array($stuff, "<div id=\"container\" style=\"width: 1000px; height: 500px; margin: 0 auto\"></div>");
}


/**
 * @param $filterName
 *
 * @return null
 */
function getFilter($filterName) {
	if (isset($_GET[$filterName])) {
		if ($_GET[$filterName] == "None") {
			$serverFilter = null;
			setcookie($filterName, null, 0);
		} else {
			setcookie($filterName, $_GET[$filterName], (time() + 60 * 60));
			$serverFilter = $_GET[$filterName];
		}
	} elseif (isset($_COOKIE[$filterName])) {
		$serverFilter = $_COOKIE[$filterName];
	} else {
		$serverFilter = null;
	}
	return $serverFilter;
}