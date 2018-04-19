<?php
if (PHP_SAPI == 'cli') {
  $_SERVER['REMOTE_ADDR'] = null;
  $_SERVER['HTTP_HOST'] = null;
  $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
}

// Search for config in different places - adding constant and env
if(defined('XHPROF_CONFIG') && is_file(XHPROF_CONFIG)) {
	require_once XHPROF_CONFIG;
}
else {
	$XHPROF_CONFIG = getenv('XHPROF_CONFIG');
	if ( ! empty($XHPROF_CONFIG) && is_file($XHPROF_CONFIG)) {
		require_once $XHPROF_CONFIG;
	} elseif ( ! empty($_SERVER['XHPROF_CONFIG']) && is_file($_SERVER['XHPROF_CONFIG'])) {
		require_once $_SERVER['XHPROF_CONFIG'];
	} else {
		require_once(XHPROF_LIB_ROOT . "/config.php");
	}
}

function getExtensionName()
{
    if (extension_loaded('tideways_xhprof'))
    {
        return 'tideways_xhprof';
    }
    if (extension_loaded('tideways'))
    {
        return 'tideways';
    }
    elseif(extension_loaded('xhprof')) {
        return 'xhprof';
    }
    return false;
}
$_xhprof['ext_name'] = getExtensionName();
if($_xhprof['ext_name'])
{
    $flagsCpu = constant(strtoupper($_xhprof['ext_name']).'_FLAGS_CPU');
    $flagsMemory = constant(strtoupper($_xhprof['ext_name']).'_FLAGS_MEMORY');
    $envVarName = strtoupper($_xhprof['ext_name']).'_PROFILE';
}


//I'm Magic :)
class visibilitator
{
	public static function __callstatic($name, $arguments)
	{
		$func_name = array_shift($arguments);
		//var_dump($name);
		//var_dump("arguments" ,$arguments);
		//var_dump($func_name);
		if (is_array($func_name))
		{
			list($a, $b) = $func_name;
			if (count($arguments) == 0)
			{
				$arguments = $arguments[0];
			}
			return call_user_func_array(array($a, $b), $arguments);
			//echo "array call  -> $b ($arguments)";
		}else {
			call_user_func_array($func_name, $arguments);
		}
	}
}

// Only users from authorized IP addresses may control Profiling
if ($controlIPs === false || in_array($_SERVER['REMOTE_ADDR'], $controlIPs) || PHP_SAPI == 'cli') {

	/* Backwards Compatibility getparam check*/
	if ( ! isset($_xhprof['getparam'])) {
		$_xhprof['getparam'] = '_profile';
	}

	if ( ! isset($_xhprof['displayparam'])) {
		$_xhprof['displayparam'] = '_display';
	}

	$handleRuntimeToggle = function($key, $uri, $cookieName = null) {
		if (isset($_GET[ $key ])) {
			if (null === $cookieName) {
				$cookieName = $key;
			}
			// Give them a cookie to hold status, and redirect back to the same page
			if ($_GET[ $key ] === "1") {
				setcookie($cookieName, $_GET[ $key ]);
			} elseif ($_GET[ $key ] === "0") {
				setcookie($cookieName, null, - 1);
				unset($_COOKIE[ $cookieName ]);
			}

			$cleanURI = str_replace(array(
				'&' . $key . '=1',
				'&' . $key . '=0',
				'?' . $key . '=1',
				'?' . $key . '=0',
			), '', $uri);

			return [ true, $cleanURI ];
		} else {
			return [ false, $uri ];
		}
	};

	$toggleParams = [ $_xhprof['getparam'], $_xhprof['displayparam'] ];

	$currentURI = $_SERVER['REQUEST_URI'];
	$changes    = false;
	foreach($toggleParams as $toggleParam) {
		list($changed, $currentURI) = $handleRuntimeToggle($toggleParam, $currentURI);
		$changes = ($changes or $changed);
	}

	if (isset($_COOKIE[ $_xhprof['getparam'] ]) && $_COOKIE[ $_xhprof['displayparam'] ]
	    || PHP_SAPI == 'cli' && ((isset($_SERVER[ $envVarName ]) && $_SERVER[ $envVarName ])
	                             || (isset($_ENV[ $envVarName ]) && $_ENV[ $envVarName ]))) {
		$_xhprof['doprofile'] = true;
		$_xhprof['type']      = 1;
	}

	if (isset($_COOKIE[ $_xhprof['displayparam'] ]) && $_COOKIE[ $_xhprof['displayparam'] ]
	    || PHP_SAPI == 'cli' && ((isset($_SERVER[ $envVarName ]) && $_SERVER[ $envVarName ])
	                             || (isset($_ENV[ $envVarName ]) && $_ENV[ $envVarName ]))) {
		$_xhprof['display'] = true;
	}

	if (true === $changes) {
		header('HTTP/1.1 302 Found');
		header("Location: $currentURI");
		die("Redirecting you to " . $currentURI);
	}

	unset($envVarName);
}


//Certain URLs should never have a link displayed. Think images, xml, etc. 
foreach($exceptionURLs as $url)
{
    if (stripos($_SERVER['REQUEST_URI'], $url) !== FALSE)
    {
        $_xhprof['display'] = false;
        header('X-XHProf-No-Display: Trueness');
        break;
    }    
}
unset($exceptionURLs);

//Certain urls should have their POST data omitted. Think login forms, other privlidged info
$_xhprof['savepost'] = true;
foreach ($exceptionPostURLs as $url)
{
    if (stripos($_SERVER['REQUEST_URI'], $url) !== FALSE)
    {
        $_xhprof['savepost'] = false;
        break;
    }    
}
unset($exceptionPostURLs);

//Determine wether or not to profile this URL randomly
if ($_xhprof['doprofile'] === false)
{
    //Profile weighting, one in one hundred requests will be profiled without being specifically requested
    if (rand(1, $weight) == 1)
    {
        $_xhprof['doprofile'] = true;
        $_xhprof['type'] = 0;
    } 
}
unset($weight);

// Certain URLS should never be profiled.
foreach($ignoreURLs as $url){
    if (stripos($_SERVER['REQUEST_URI'], $url) !== FALSE)
    {
        $_xhprof['doprofile'] = false;
        break;
    }
}
unset($ignoreURLs);

unset($url);

// Certain domains should never be profiled.
foreach($ignoreDomains as $domain){
    if (stripos($_SERVER['HTTP_HOST'], $domain) !== FALSE)
    {
        $_xhprof['doprofile'] = false;
        break;
    }
}
unset($ignoreDomains);
unset($domain);

//Display warning if extension not available
if ($_xhprof['ext_name'] && $_xhprof['doprofile'] === true) {
    include_once dirname(__FILE__) . '/../xhprof_lib/utils/xhprof_lib.php';
    include_once dirname(__FILE__) . '/../xhprof_lib/utils/xhprof_runs.php';
    if (isset($ignoredFunctions) && is_array($ignoredFunctions) && !empty($ignoredFunctions)) {   
        call_user_func($_xhprof['ext_name'].'_enable', $flagsCpu + $flagsMemory, array('ignored_functions' => $ignoredFunctions));
    } else {
        call_user_func($_xhprof['ext_name'].'_enable', $flagsCpu + $flagsMemory);
    }
    unset($flagsCpu);
    unset($flagsMemory);
    
}elseif(false === $_xhprof['ext_name'] && $_xhprof['display'] === true)
{
    $message = 'Warning! Unable to profile run, tideways or xhprof extension not loaded';
    trigger_error($message, E_USER_WARNING);
}
unset($flagsCpu);
    unset($flagsMemory);
function xhprof_shutdown_function() {
    global $_xhprof;
    require dirname(__FILE__).'/footer.php';
}

register_shutdown_function('xhprof_shutdown_function');
