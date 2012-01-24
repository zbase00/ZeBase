<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config["base_url"] = "http://zebase.bio.purdue.edu/dev/";
$config["index_page"] = "index.php";
$config["uri_protocol"] = "AUTO";
$config["email_protocol"] = "sendmail";
$config["mailpath"] = "/usr/bin/sendmail";
$config["smtp_host"] = "";
$config["smtp_user"] = "";
$config["smtp_pass"] = "";
$config["smtp_port"] = "25";
$config["smtp_timeout"] = "5";
$config['log_threshold'] = 4;

$_SESSION['base_url'] = $config["base_url"];
?>