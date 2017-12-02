<?php
$XHPROF_ROOT = realpath(dirname(__FILE__) .'/');
include_once $XHPROF_ROOT . "/xhprof_lib/config.php";
        
class XHProf {

    public static function enable() {
        register_shutdown_function('XHProf::finish');
        xhprof_enable();
    }

    public static function finish() {
        // stop profiler
        $xhprof_data = xhprof_disable();
        $XHPROF_ROOT = realpath(dirname(__FILE__) .'/');
        include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
        include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
       
        // save raw data for this profiler run using default
        // implementation of iXHProfRuns.
        $xhprof_runs = new XHProfRuns_Default();

        // save the run under a namespace "xhprof_foo"
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
        var_dump($run_id);exit;
    }
}
