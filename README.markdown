XHProf UI
=========

This is a graphical front end designed to store and present the profiling information provided by the Facebook created XHProf profiling tool.


Related Tools
-------------

* [XHGui](https://github.com/perftools/xhgui) - Uses MongoDB as a backend (rather than MySQL)


Project Includes
----------------

* It includes a header.php document you can use with PHP's 
  auto\_prepend\_file directive. It sets up profiling by initilizing a few variables, and settting register_shutdown_function with the footer. Once started profiles are done 
  when requested (?\_profile=1), or randomly. Profiled pages display a link to 
  their profile results at the bottom of the page (this can be disabled on a 
  blacklist based for specific documents. e.g. pages generating XML, images, 
  etc.).
* For tips on including header.php on an nginx + php-fpm install take a look at: http://www.justincarmony.com/blog/2012/04/23/php-fpm-nginx-php_value-and-multiple-values/
* The GUI is a bit prettier (Thanks to Graham Slater)
* It uses a MySQL backend, the database schema is stored in xhprof\_runs.php 
* There's a frontend to view different runs, compare runs to the same url, etc.

Key Features
-------------

* Listing 25, 50 most recent runs
* Display most expensive (cpu), longest running, or highest memory usage runs 
  for the day
* It introduces the concept of "Similar" URLs. Consider:
  * http://news.example.com/?story=23
  * http://news.example.com/?story=25
  While the URLs are different, the PHP code execution path is likely identical,
  by tweaking the method in xhprof\_runs.php you can help the frontend be aware
  that these urls are identical.
* Highcharts is used to graph stats over requests for an 
  easy heads up display.

Requirements
------------

Besides a simple PHP running on your favourite web server you will also need following packages:

* php5-xhprof
* php5-mysql
* graphviz (uses `dot` to generate callgraphs)

Installation
-------------

* Install your favourite mix of PHP and web server
* Install MySQL server
* Clone the project to some folder of your choice.
* Map the sub folder `xhprof_html` to be accessible over HTTP
  * You can do it with an Alias directive or (if symlinks are enabled) by just symlinking `xhprof_html` to any location within your document root.
* Copy `xhprof_lib/config.sample.php` to `xhprof_lib/config.php`
  * Alternatively, you can copy the config.php to any place you like, and then specify the location of the config file in your ENV, in a PHP constant, or via Apache / Nginx Env variable (see below)
* Edit `xhprof_lib/config.php`
  * Update the SQL server configuration
  * Update the URL of the service (should point to `xhprof_html` over HTTP)
  * Update the `dot_binary` configuration - otherwise no call graphs!
  * Update the `controlIPs` variable to enable access.
  * For a development machine you can set this to `false` to disable IP checks.
* Import the DB schema (it is just 1 table)
 * See the SQL at [xhprof_runs.php](https://github.com/toomasr/xhprof/blob/master/xhprof_lib/utils/xhprof_runs.php#L109)
* Add a PHP configuration to enable the profiling
  * If using Apache you can edit your virtual host configuration
  * Add `php_admin_value auto_prepend_file "/path/to/xhprof/external/header.php"`
  * (optional) Add `SetEnv XHPROF_CONFIG /absolute/path/to/config.php` to your apache config to set location of config file for that host
  * (optional) If you include the header.php manually, you can define the location of the config file via define('XHPROF_CONFIG','/absolute/path/to/config.php');
  * (optional) Within a shell script, you can export `XHPROF_CONFIG=/absolute/path/to/config.php` to specify location of config file
* Visit http://your-server/xhprof/xhprof_html/ and be amazed!
 * To get profiler information showing up there visit your page with a `GET` variable `_profile=1`. 
 This will enable it (via cookie) until you disable it by adding the parameter `_profile=0` to any url (or removing the _profile cookie manually)
 * For example `http://localhost/?_profile=1`

We Are Working On
-----------------

* The aggregation functionality is ignored completely
* The code is... a mess. Deadlines do that to you, we're working on it
* The default table schema isn't indexed all the places it needs to be
* Easier ways to diff URLs
