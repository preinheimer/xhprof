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
	echo "<table id=\"box-table-a\" class=\"tablesorter\" summary=\"Stats\"><thead><tr><th>Run</th><th>Timestamp</th><th>Domain</th><th>Cpu</th><th>Wall Time</th><th>Peak Mem.</th><th>URL</th><th>Simplified URL</th></tr></thead>";
	echo "<tbody>\n";
	while ($row = XHProfRuns_Default::getNextAssoc($resultSet)) {
		$c_url = urlencode($row['c_url']);
		$url = urlencode($row['url']);
		$html['url'] = htmlentities($row['url'], ENT_QUOTES, 'UTF-8');
		$html['c_url'] = htmlentities($row['c_url'], ENT_QUOTES, 'UTF-8');
		$date = strtotime($row['timestamp']);
		$date = date('M d H:i:s', $date);
		echo '   <tr>' .
			'<td class="id"><a href="?run=' . $row['id'] . '">' . $row['id'] . '</a></td>' .
			'<td class="date">' . $date . '</td>' .
			'<td class="serverName"><a class="filterByDomain" href="#" title="Show only runs for ' . $row['server name'] . '">' . $row['server name'] . '</a></td>' .
			'<td class="cpu" title="' . $row['cpu'] . '">' . printSeconds($row['cpu']) . '</td>' .
			'<td class="wt" title="' . $row['wt'] . '">' . printSeconds($row['wt']) . '</td>' .
			'<td class="pmu" title="' . $row['pmu'] . '">' . printBytes($row['pmu']) . '</td>' .
			'<td class="url"><a href="?geturl=' . $url . '" title="' . $html['url'] . ' ">' . $html['url'] . '</a></td>' .
			'<td class="c_url"><a href="?getcurl=' . $c_url . '" title="' . $html['c_url'] . '">' . $html['c_url'] . '</a></td></tr>' . PHP_EOL;
	}
	echo "</tbody>\n";
	echo "</table>\n";
}

/**
 * Print a value as Bytes
 *
 * @param integer $size The size to format.
 * @param array   $sizes The list of size units.
 *
 * @return string
 */
function printBytes($size, $sizes = array(' B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB')) {
	$prefix = '';
	if ($size == 0) {
		return ('n/a');
	}
	if ($size < 0) {
		$size = abs($size);
		$prefix = '-';
	}
	return $prefix . round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i];
}

/**
 * Format an integer as a time value
 *
 * @param integer $time The value to format
 *
 * @return string
 */
function printSeconds($time) {
	$prefix = '';
	$suffix = 'μs';
	if ($time < 0) {
		$time = abs($time);
		$prefix = '-';
	}

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

	return $prefix . sprintf("%.2f {$suffix}", $time);
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