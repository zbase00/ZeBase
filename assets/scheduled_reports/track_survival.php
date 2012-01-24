<?php
$hostname_local = $_SESSION["hostname"];
$username_local = $_SESSION["db_username"];
$password_local = $_SESSION["db_password"];
$database_local = $_SESSION["database"]; 
	$local = mysql_connect($hostname_local, $username_local, $password_local) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_local, $local);
//survival rate track (takes a snapshot of the data at a given time)
	$query_Recordset = "select * from fish";
	$Recordset = mysql_query($query_Recordset, $local) or die(mysql_error()); 	 
	while($temp_object =  mysql_fetch_assoc($Recordset)){	  
		$survival_percent = floor($temp_object['current_adults'] / $temp_object['starting_nursery'] * 100);
		$sql = "insert into stat_survival_track values('','" . $temp_object['batch_ID'] . "','" . $temp_object['starting_adults'] . "','" . 
		$temp_object['current_adults'] . "','" . $temp_object['status'] . "','" . $survival_percent . "','" . $temp_object['birthday'] . "','" . time() . "','" . $temp_object['starting_nursery'] . "');";
		if (mysql_query($sql, $local)) {
			$insertVar = "0";
		} else {
			//echo $sql . ' <br>';					 		 
			die("Reason: " . mysql_error());		
		} 	 
	}
	echo 'Report ran successfully!';
?>