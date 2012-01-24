<?php 
$hostname_local = $_SESSION["hostname"];
$username_local = $_SESSION["db_username"];
$password_local = $_SESSION["db_password"];
$database_local = $_SESSION["database"];
$web_url = $_SESSION["base_url"];

$admin_email = ""; 
	$local = mysql_connect($hostname_local, $username_local, $password_local) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_local, $local);
	//move the current working directory up to use the main temp folder
	chdir ('../');
function create_attatchment($local,$html,$lab){
		if (file_exists(getcwd() . "/scheduled_reports/tmp/" . $lab . "_Monthly_Report.doc")){
			unlink(getcwd() . "/scheduled_reports/tmp/" . $lab . "_Monthly_Report.doc");
		}
		$fp = fopen(getcwd() . "/scheduled_reports/tmp/" . $lab . "_Monthly_Report.doc", 'w+');		
		$str = str_replace('$0','',$html);		
		fwrite($fp, $str);		
		fclose($fp); 
}
function sortByField($multArray,$sortField,$desc=true){
			//array_walk_recursive($multArray,'remove_tags');
            $tmpKey='';
            $ResArray=array();			
            $maIndex=array_keys($multArray);
            $maSize=count($multArray)-1;			 
            for($i=0; $i < $maSize ; $i++) {
               $minElement=$i;
               $tempMin=$multArray[$maIndex[$i]][$sortField];
               $tmpKey=$maIndex[$i];
                for($j=$i+1; $j <= $maSize; $j++)
                  if($multArray[$maIndex[$j]][$sortField] < $tempMin ) {
                     $minElement=$j;
                     $tmpKey=$maIndex[$j];
                     $tempMin=$multArray[$maIndex[$j]][$sortField];

                  }
                  $maIndex[$minElement]=$maIndex[$i];
                  $maIndex[$i]=$tmpKey;
            }

           if($desc)
               for($j=0;$j<=$maSize;$j++)
                  $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];
           else
              for($j=$maSize;$j>=0;$j--)
                  $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];

           return $ResArray;
}
function output_titles($local,$doctype, $column_array){ 	  
	if ($doctype =="l_summary"){
		list($column_array) = get_defualt_columns($local); 
	}elseif ($doctype =="q_summary"){		 
		if ($column_array == "mutant" || $column_array == "strain" || $column_array == "transgene"){
			$type = $column_array;
			$column_array = "";
			$column_array[0]['db_name'] = $type;
			$column_array[1]['db_name'] = "current_adults";
			$column_array[2]['db_name'] = "starting_nursery";
			$column_array[3]['db_name'] = "current_adults";
			$column_array[4]['db_name'] = "total_batches";
			$column_array[0]['col_title'] = $type;
			$column_array[1]['col_title'] = "Incoming Adults";
			$column_array[2]['col_title'] = "Nursery Quantity";
			$column_array[3]['col_title'] = "Current Adults (Alive,Sick)";
			$column_array[4]['col_title'] = "Total Batches";	
		}else{
			$column_array = "";
			$column_array[0]['db_name'] = "current_adults";
			$column_array[1]['db_name'] = "starting_nursery";
			$column_array[2]['db_name'] = "current_adults";
			$column_array[3]['db_name'] = "total_batches";		
			$column_array[0]['col_title'] = "Incoming Adults";
			$column_array[1]['col_title'] = "Nursery Quantity";
			$column_array[2]['col_title'] = "Current Adults (Alive,Sick)";
			$column_array[3]['col_title'] = "Total Batches";	
		}   	 
	 }else{	 	
	 	$column_array = "";		 
		$index = "0";
		if (is_array($_POST['columns_var'])){
			foreach ($_POST['columns_var'] as $value){
				$table_field = explode(":",$value);
				if ($table_field[1] == "batch_ID"){
					$column_array[$index]['db_name'] =  'batch_ID';	
					$column_array[$index]['col_title'] = 'Batch Number';
				}else{
					$column_array[$index]['db_name'] =  $table_field[1];	
					$column_array[$index]['col_title'] =$table_field[1];
				}	
				$index++;
			}	
		}else{
			list($column_array) = get_defualt_columns($local); 
		}	 
	}	
	$html = "";	
	if (is_array($column_array)){	 
		foreach($column_array as $key=> $value){		 
			if ($_POST['column_sort'] == $column_array[$key]['db_name']){ 
  					$html .= '<th style="padding-right:20px">';
	 					$html .= $column_array[$key]['col_title'];  
		 				$html .='</th>';
	 		}else{
				$html .= '<th style="padding-right:20px">';
 				$html .= $column_array[$key]['col_title'] ; 
 				$html .= '</th>';
			}
		}
	}else{
		echo 'No columns selected<br><br>';
	} 
	return $html;	
} 
function sort_tables($ID){
	$html = '<script type="text/javascript">
	$(document).ready(function() 
    { 
        $("#'. $ID. '").tablesorter({}); 
    } 
);      
	</script>'; 
}
function checkbackground($backgroundindex) {
		if ($backgroundindex == "3"){
			$backgroundindex = "1";
		}		
		return $backgroundindex;
}
function get_defualt_columns($local){
	$query_RecordsetDR = "desc fish";	 
	$RecordsetDR = mysql_query($query_RecordsetDR, $local);	
	$index = 0;
	if ($_POST['search_by'] =="tank"){				
			$column_array[$index]['db_name'] =  'location';	
			$column_array[$index]['col_title']=  'location';	
			$index++;
	}
	while($temp_object =  mysql_fetch_assoc($RecordsetDR)){
		if ($temp_object['Field'] == "batch_ID"){
			$column_array[$index]['db_name'] =  "batch_ID";	
			$column_array[$index]['col_title']=  "Batch Number";
		}elseif ($temp_object['Field'] == "user_ID"){
			$column_array[$index]['db_name'] =  "user_ID";	
			$column_array[$index]['col_title']=  "User";
		}elseif ($temp_object['Field'] == "strain_ID"){
			$column_array[$index]['db_name'] =  "strain_ID";	
			$column_array[$index]['col_title']=  "Strain";
		}elseif ($temp_object['Field'] == "mutant_ID"){
			$column_array[$index]['db_name'] =  "mutant_ID";	
			$column_array[$index]['col_title']=  "Mutant";
		}elseif ($temp_object['Field'] == "transgene_ID"){
			$column_array[$index]['db_name'] = "transgene_ID";	
			$column_array[$index]['col_title']= "Transgene";
		}elseif ($temp_object['Field'] == "mother_ID" || $temp_object['Field'] == "comments" || $temp_object['Field'] == "tank_ID" || $temp_object['Field'] == "father_ID" || $temp_object['Field'] == "mother_other" || $temp_object['Field'] == "father_other"){	
	 	}else{
			$column_array[$index]['db_name'] =  $temp_object['Field'];	
			$column_array[$index]['col_title']=  $temp_object['Field'];
		}
		$index++;
	}	
	return array($column_array);
}
function output_custom_fields($temp_object, $doctype, $location, $local, $index){	
	if ($temp_object["starting_nursery"] == "" || $temp_object["starting_nursery"] == "0"){ 
		$temp_object["starting_nursery"] = '<a href="#" style=" visibility:hidden">$' . 0 . '</a>';				 								 
	}	
	if ($temp_object["current_adults"] == "" || $temp_object["current_adults"] == "0"){ 
		$temp_object["current_adults"] = '<a href="#" style=" visibility:hidden">$' . 0 . '</a>';					 								 
	}	
	if ($temp_object["current_adults"] == "" || $temp_object["current_adults"] == "0"){ 
		$temp_object["current_adults"] = '<a href="#" style=" visibility:hidden">$' . 0 . '</a>';					 								 
	} 
	if ($doctype =="l_summary"){			 
		 	list($column_array) = get_defualt_columns($local); 
			$html = "";
			foreach ($column_array as $value){ 
				$field_name = $value['db_name'];
				if ($field_name == "batch_ID"){
					$html .= '<td><a href="fish_db.php?batch_ID=' . $temp_object[$field_name] . 
					'">' . $temp_object[$field_name] . '</a></td>';
				}elseif ($value['db_name'] == "batch_ID"){
					$html .= '<td>';
					$html .= '<a href="#" onmouseover="javascript:show_tooltip(event,\'' . $temp_object[$field_name] . '\',\'' . $index . '\', \'-20\')"  onmouseout="javascript:hide_tooltip(\'' . $index . '\')">view</a>'; 
					$html .= tooltip_output($index);
					$html .= '</td>';
				}elseif ($value['db_name'] == "user_ID"){
					$user_array = "";
					list($user_array) = get_user($local, $temp_object[$field_name]);
					$html .= '<td>' . $user_array['name'] . '</td>';	
				}elseif ($value['db_name'] == "strain_ID"){
					$strain_array = "";
					list($strain_array) = get_strain($local, $temp_object[$field_name]);
					$html .= '<td>' . $strain_array['strain'] . '</td>';
				}elseif ($value['db_name'] == "mutant_ID"){
					$mutant_array = "";
					list($mutant_array) = get_mutant($local, $temp_object[$field_name]);
					$html .= '<td>' . $mutant_array['mutant'] . '</td>';
				}elseif ($value['db_name'] == "transgene_ID"){
					$transgene_array = "";
					list($transgene_array) = get_transgene($local, $temp_object[$field_name]);
					$html .= '<td>' . $transgene_array['transgene'] . '</td>';
				}else{
					$html .= '<td>' . $temp_object[$field_name] .  '</td>';
				}
			}
						
			
	}elseif ($doctype =="q_summary"){			 	 
			$html = "";				
			if ($index == "mutant"){
				$html .= '<td>' . $temp_object['mutant'] . '</td>';	
			}elseif ($index == "strain"){
				$html .= '<td>' . $temp_object['strain'] . '</td>';	
			}elseif ($index == "transgene"){
				$html .= '<td>' . $temp_object['transgene'] . '</td>';	
			}else{				
			}
			$html .= '<td>' . $temp_object['current_adults'] . '</td>';
			$html .= '<td>' . $temp_object['starting_nursery'] . '</td>';
			$html .= '<td>' . $temp_object['current_adults'] . '</td>';
			$html .= '<td>' . $temp_object['total_batches'] . '</td>';		 		
			return $html;		
	} 	 
}	
function output_qsummary_rows($local,$listby,$type){ 
  	$ID = 'qsummary_' . $type;
	sort_tables($ID);
	$html = "<br>";
		if ($type == "user"){
			$html .=	'<strong>' . $listby . ' Quantity Summary</strong><br>';
		}else{
			$html .=	'<strong>' . $type . ' Quantity Summary</strong><br>';
		}	 
		$html .= '<div style=""><table id="qsummary_' . $type . '" class="tablesorter"><thead> ';
		$html .= '<tr style="font-size:13px" onMouseOver="this.bgColor = \'#FFFFFF\'" onMouseOut ="this.bgColor = \'#CCCCCC\'" bgcolor="#CCCCCC">';	 
	 	$lab = str_replace("_lab","",$listby);		 
		if(strstr($listby,'_lab')){
			$criteria = " lab_name like '" . $lab . "' ";
		}else{
			$criteria = "users.last_name like '" . $listby . "'";
		}
		
		if($type == "user"){		 			
			$sql = "select count(batch_ID) as total_batches, sum(current_adults) as current_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery from fish, users,labs where labs.lab_ID = users.lab and fish.user_ID = users.user_ID and " . $criteria;			 
		}elseif ($type == "mutant"){			 
			$sql = "select distinct mutant.mutant_ID,mutant.*, count(fish.batch_ID) as total_batches, sum(starting_adults) as starting_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery from fish, users,mutant,mutant_assoc,labs where mutant_assoc.batch_ID = fish.batch_ID  and mutant.mutant_ID = mutant_assoc.mutant_ID and fish.user_ID = users.user_ID and " . $criteria . " group by mutant_assoc.mutant_ID";
 		}elseif ($type == "strain"){
			$sql = "select distinct fish.strain_ID,strain.*, count(batch_ID) as total_batches, sum(current_adults) as current_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery from fish, users,strain,labs where labs.lab_ID = users.lab and strain.strain_ID = fish.strain_ID and fish.user_ID = users.user_ID and " . $criteria . " group by fish.strain_ID";
		}elseif ($type == "transgene"){
			$sql = "select distinct transgene_assoc.transgene_ID,transgene.*, count(fish.batch_ID) as total_batches, sum(current_adults) as current_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery from fish, users,transgene,transgene_assoc,labs where transgene_assoc.batch_ID = fish.batch_ID and labs.lab_ID = users.lab and transgene.transgene_ID = transgene_assoc.transgene_ID and fish.user_ID = users.user_ID and " . $criteria . " group by transgene_assoc.transgene_ID";
		}  
		$Recordset = mysql_query($sql, $local) or die(mysql_error()); 	 
		while($object =  mysql_fetch_assoc($Recordset)){
			$all_array[] = $object;						  
		}			
		if (is_array($all_array)){	 
			$html .= output_titles($local,$_GET['type'], $type);
			$html .= '</tr></thead><tbody> ';
	 		if ($_POST['column_sort'] != ""){
				if ($_POST['sort_order'] == "desc"){
					$desc=true;
				}else{
					$desc=false;
				}		 			 
				$final_object = sortByField($all_array,$_POST['column_sort'],$desc);					 				 					 				 
			}else{					 
				$final_object = sortByField($all_array,'batch_ID',$desc=false);					 
			}			
			foreach ($final_object as $final_output){					 
				$backgroundindex = checkbackground($backgroundindex);
				if ($backgroundindex == "1") {
					$html .= '<tr style="font-size:13px" onMouseOver="this.bgColor = \'#FFFFFF\'" onMouseOut ="this.bgColor = \'#CCCCCC\'" bgcolor="#CCCCCC">';  
				} else {
					$html .= '<tr style="font-size:13px" onMouseOver="this.bgColor = \'#FFFFFF\'" onMouseOut ="this.bgColor = \'#EBEBEB\'" bgcolor="#EBEBEB">';
				}				 
	 			$html .= output_custom_fields($final_output, $_GET['type'], $location, $local, $type);
		 		$html .= '</tr>';
	 			$backgroundindex++;
			}	
			$html .= '</tbody> </table></div><br><br>'; 			
 		}else{
			$html .= 'No results found!'; 
 			$html .= '</tr></thead><tbody></tbody> </table></div>'; 
 		} 
				
		return $html;
}

$sql = "select * from labs";
$outer_Recordset = mysql_query($sql, $local) or die(mysql_error()); 	 
while($lab_object =  mysql_fetch_assoc($outer_Recordset)){	
	$query_Recordset = "select * from users,labs where users.lab = labs.lab_ID and lab_ID like '" . $lab_object['lab_ID'] . "'";
 	$Recordset = mysql_query($query_Recordset, $local) or die(mysql_error()); 	
	$html = "Monthly Quantity Report " . date("m/d/y",time());
	while($temp_object =  mysql_fetch_assoc($Recordset)){
		if ($temp_object['last_name'] != ""){	
			$_GET['type'] = "q_summary";
			$_GET['listby'] = $temp_object['last_name'];
			$html .= "<br><hr><strong>Batches by User</strong>: " . $temp_object['last_name'];
			$html .= "<br><br>";
			$html .= output_qsummary_rows($local,$_GET['listby'],'user');		
			$html .= output_qsummary_rows($local,$_GET['listby'],'mutant');
			$html .= output_qsummary_rows($local,$_GET['listby'],'strain');
			$html .= output_qsummary_rows($local,$_GET['listby'],'transgene');
			$html .= "<br><br>";
		}
	} 
	$_GET['type'] = "q_summary";
	$lab = $lab_object['lab_name'] . "_lab";
	$html .= output_qsummary_rows($local,$lab,'user');		
	$html .= output_qsummary_rows($local,$lab,'mutant');
	$html .= output_qsummary_rows($local,$lab,'strain');
	$html .= output_qsummary_rows($local,$lab,'transgene');	  
	create_attatchment($local,$html,$lab_object['lab_name']);
}	
	$sql = "select * from report_recipients RP join users USR on (USR.user_ID = RP.user_ID)  join labs LBS on (USR.lab = LBS.lab_ID)";
	$Recordset = mysql_query($sql, $local) or die(mysql_error()); 	 
	while($lab_object =  mysql_fetch_assoc($Recordset)){  
		$fileatt = getcwd() . "/scheduled_reports/tmp/"; // Path to the file
		$fileatt_type = "application/octet-stream"; // File Type
		$fileatt_name = $lab_object['lab_name'] . "_Monthly_Report.doc"; // Filename that will be used for the file as the attachment 
		$email_from = $admin_email; // Who the email is from
		$email_subject =  "Monthly Zebra Database Quantity Report";
		$email_txt = "Monthly Quantity Report " . date("m/d/y",time());
		$email_txt .= '<br><br><a href="' . $web_url . 'tmp/' . $lab . '_Monthly_Report.doc">Click Here</a> to view your report.';
	 
	 	$headers = "From: ".$email_from;  
		$filedata = file_get_contents($fileatt . $fileatt_name); //Get file contents
		$data = chunk_split(base64_encode($filedata)); //Encode data into text form
		 $semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" . 
		" boundary=\"{$mime_boundary}\"";
		$email_message .= "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" .
		$email_message . "\n\n";
		$email_message .= "--{$mime_boundary}\n" .
		"Content-Type: {$fileatt_type};\n" .
		" name=\"{$fileatt_name}\"\n" . 
		"Content-Transfer-Encoding: base64\n\n" .
		$data . "\n\n" .
		"--{$mime_boundary}--\n";  
		$email_to = $lab_object['email']; // Who the email is too
	 		 
		$ok = @mail($email_to, $email_subject, $email_message, $headers);  
	}
	echo 'Email messages were sent successfully!';
?>