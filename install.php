<?php
function output_pre_installation(){
	?>
 <div class="standard_box" style="width:800px; margin-left:200px; margin-top:50px;">
        <div>
        <h1>ZeBase</h1>
        </div>
        <div>
        <h2>Pre-Installation Notes</h2> 
        Welcome to ZeBase.  Before we begin please ensure that you have the below information:
        <ul>
        <li>Database name</li>
        <li>Database username</li>
        <li>Database password</li>
        <li>Database host</li> 
        </ul><br />
        If you have all of this information ready and have updated the configuration files below, then you are ready to go.  Hit next to configure the database.
      <br /> <br /><span style="font-size:.9em">*note these are located in the root of this system.</span>
       <br />config.main.php
       <br /> config.db.php
        </div>
        <br /> 
        <div style="margin-left:700px;">
        <a href="#" onClick="location.href='install.php?next=1';" class="jq_buttons" style=" font-size:20px;">Next</a>
        </div>
    </div>
<?php
}
function run_db_connect(){  
		include 'config.db.php'; 
		$link = mysql_connect($_SESSION["hostname"], $_SESSION["db_username"],$_SESSION["db_password"]); 
		if (!$link) { 
		  $error .= 'Not connected : ' . mysql_error();
		}else{ 
			$sql = "use " . $_SESSION["database"];
			if (mysql_query($sql, $link)) {
				$error = $_SESSION["database"] . " database already in use.<br>"; 
				return $error; 
			}  
			$db_selected = mysql_select_db($_SESSION["database"], $link); 
			if (!$db_selected) {
				$sql = "CREATE DATABASE " .  $_SESSION["database"] . ";";  
				if (mysql_query($sql, $link)) { 
				} else {		 
					$error .= 'Error: ' . mysql_error();		
				} 
				$db_selected = mysql_select_db($_SESSION["database"], $link);
			}else{
				$error .= 'Cannot use ' . $_SESSION["database"];						
			}
		} 
			$error .= create_tables($link);
	return $error;
}
function create_tables($link){  
	$dir = getcwd();
	$file = $dir . "/create_tables.sql";	
	$error = ""; 
	$f= @fopen($file,'r'); 
	$sqldata=''; 
	$data=''; 
	$error = "";
	$statement = "";
	while(!feof($f)){
		$data = fgets($f,1800); 
		if (strstr($data,'^')){
			$sqldata[] = $statement;
			$statement = "";
		}else{
			$statement .= $data; 
		}
	}
	if (is_array($sqldata)){
		foreach ($sqldata as $sql_statement){
			if (mysql_query($sql_statement, $link)) {  
			} else {		 
				$error = 'Database configuration: <br>: ' . mysql_error();		
			}  
		}
	}
	//insert the admin account
	//username: admin
	//password: user
	require_once('phpass-0.1/PasswordHash.php');
	define('PHPASS_HASH_STRENGTH', 8);
	define('PHPASS_HASH_PORTABLE', false);
	$user_pass = "user";
	$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
	$user_pass_hashed = $hasher->HashPassword($user_pass);
	$sql = "insert into labs values('','templab');";
	if (mysql_query($sql, $link)) {   
	} else {	  
		$error = 'Error: ' . mysql_error();		
	}   
	$lab_ID = mysql_insert_id();
	//$temppass = '$2a$08$7F9wlzclDJq8Hc0UhvtSqOMmUYV06CG0TClQoGgjNjH8NSt7jkiTa';
	$sql = "insert into users values('','" . $user_pass_hashed . "','','','','','" . $lab_ID . "','','','','','','admin','on','admin','','');";
	if (mysql_query($sql, $link)) {   
	} else {	  
		$error = 'Error: ' . mysql_error();		
	}
	
	return $error; 
}
function output_finish(){
	?>
 <div class="standard_box" style="width:800px; margin-left:200px; margin-top:50px;">
        <div>
        <h1>ZeBase</h1>
        </div>
        <div> 
        You are finished.<br /><br />
        <strong>Username:</strong> admin<br />
        <strong>Password:</strong> user<br />
        </div>
        <br />
         <div style=" float:left">
        <a href="#" onClick="location.href='install.php';" class="jq_buttons" style=" font-size:20px;">Back</a>
        </div>
        <div style="margin-left:650px;">Go to your site
        <a href="#" target="_new" onClick="window.open('index.php/fish/login','form','width=1200,height=1200,left=10,top=163,location=no, menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');" class="jq_buttons" style=" font-size:20px;">Go</a>
        </div>
    </div>
<?php
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ZeBase Installation</title>
<style>
.standard_box { -webkit-border-radius: 23px; 
-moz-border-radius: 23px; 
border-radius: 23px; 
-webkit-box-shadow: 2px 2px 21px #808080; 
-moz-box-shadow: 2px 2px 21px #808080; 
box-shadow: 2px 2px 21px #808080; 
 border: 0px solid #90EE90; 
background-color: #FFF; 
padding: 10px; 
font-family: Verdana, Geneva, sans-serif; 
font-size: 1em; 
color: #888888; 
text-align: left;}
.container {  
height:800px;
background-image: -moz-linear-gradient(top, #FFFFFF, #E6E6FA); 
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0.0, #FFFFFF), color-stop(1.0, #E6E6FA)); 
 }
 .error_msg{
background-color: #F08080;
padding: 10px;
font-family: Verdana, Geneva, sans-serif;
font-weight: bold;
font-size: 12pt;
color: #FFFFFF;
text-align: left;
outline: 6px solid #DC143C; 
 }
  .complete_msg{
background-color: #390;
padding: 10px;
font-family: Verdana, Geneva, sans-serif;
font-weight: bold;
font-size: 12pt;
color: #FFFFFF;
text-align: left;
outline: 6px solid #3C0; 
 }
</style> 
<link rel="stylesheet" href="assets/functions/jquery/jquery-ui-1.8.16.custom/css/redmond/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" />

<script src="assets/functions/jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.core.js" type="text/javascript"></script>
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.effects.core.js"></script> 	
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.datepicker.js" type="text/javascript"></script>
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.tabs.js" type="text/javascript"></script>
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.button.js" type="text/javascript"></script>
<script src="assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordion.js" type="text/javascript"></script>

<script language="javascript">
$(function() {
	$(  "a.jq_buttons").button(); 
	$(  "a.jq_buttons").click(function() { return false; }); 
});
</script>
</head>

<body >
<div  class="container">
<?php 
if ($_GET['next'] == ""){
	output_pre_installation();
}elseif ($_GET['next'] == "1"){ 
	  $error = ""; 
	  $error = run_db_connect();	
			
	  if ($error != ""){			 
		echo '<div class="error_msg" style=" width:900px; margin-left:180px"><h2>Error:</h2>' . $error . '<br>Fix the configuration files and hit next again.</div>';                   
		output_pre_installation(); 
	  }else{
		  echo '<script language="javascript">
		  location.href="install.php?next=3";
		  </script>';
	  }
}elseif ($_GET['next'] == "3"){ 
	echo '<div class="complete_msg" style=" width:900px; margin-left:180px"><h2>Database Configuration Finished</h2></div>';
	output_finish();	
}
?>
</div>
</body>
</html>
