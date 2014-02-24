<?php
if (!defined('XHPROF_LIB_ROOT')) {
  define('XHPROF_LIB_ROOT', dirname(dirname(__FILE__)) . '/xhprof_lib');
}

if ($_xhprof['doprofile'] === true && extension_loaded('xhprof')) {
	$profiler_namespace = $_xhprof['namespace'];  // namespace for your application
	$xhprof_data = xhprof_disable();
	$xhprof_runs = new XHProfRuns_Default();
	$run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace, null, $_xhprof);
	if (PHP_SAPI !== 'cli' && $_xhprof['display'] === true) {
		// url to the XHProf UI libraries (change the host name and path)
		$profiler_url = sprintf($_xhprof['url'].'/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
		echo '<a href="'. $profiler_url .'" target="_blank">Profiler output</a>';
	}
}
