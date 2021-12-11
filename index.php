<?php

/** Define APPLICATION constact to be able to load required files. */
define('APPLICATION', TRUE);

/** Require dependencies */
require_once 'Config.class.php';
require_once 'DDNSProvider.class.php';

/** Read request parameters */
$domain = $_GET['domain'] ?? die('ERROR: Missing required parameter: domain');
$host = $_GET['host'] ?? die('ERROR: Missing required parameter: host');
$user = $_GET['user'] ?? die('ERROR: Missing required parameter: user');
$token = $_GET['token'] ?? die('ERROR: Missing required parameter: token');
$ip = $_GET['ip'] ?? die('ERROR: Missing required parameter: ip');

/** Read configuration */
$config = new Config('config.inc.php');

/** Initialize new DDNS Provider */
$ddns = new DDNSProvider($config);

/** Tell the DNS provider the zone to use and then update the IP address */
if ($ddns->use($user, $token, $domain) === false) {
	die('ERROR: Authentication failed. This might be due to invalid credentials or you\'re not allowed to update the specified domain.');
}

$ddns->updateIp($host, $ip);

die('OK');
