<?php 
/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 , 'starting_nursery', 'current_adults', 'starting_adults', 'lab','status','starting_nursery','starting_adults','current_adults','birthday','death_date','date_taken' 
	 */  
 
	$select_fields = explode(',',$_POST['datatables_select']);  
	$aColumns = explode(',',$_POST['datatables_fields']);  
	/* Indexed column (used for fast and accurate table cardinality) */ 
	$sIndexColumn = $_POST['datatables_index_col'];
	/* DB table to use */
	$sTable = $_POST['datatables_from']; 
	/* Database connection information */
function get_mutant_transgene_data($link,$batch_ID){
	$sQuery = "select * from mutant_assoc join mutant on (mutant.mutant_ID = mutant_assoc.mutant_ID) where batch_ID like '" . $batch_ID . "'";
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error()); 
	$output = "";
	$output =  '<table><tr><td colspan="2"><strong>Mutant</strong></td></tr><tr>';
	 
	while ( $aRow = mysql_fetch_array( $rResult ) ){ 
		$output .= '<td><table><tr><td colspan="2"><strong>' . $aRow['mutant'] . '</strong></td></tr>';
		$output .= '<tr><td>Allele:</td><td>' . $aRow['allele'] . '</td></tr>';
		$output .= '<tr><td>Strain:</td><td>' . $aRow['strain'] . '</td></tr></table></td>'; 
	}
	$sQuery = "select * from transgene_assoc join transgene on (transgene.transgene_ID = transgene_assoc.transgene_ID) where batch_ID like '" . $batch_ID . "'";
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error()); 
	$output .= '</tr><tr><td colspan="2"><strong>Transgene</strong></td></tr><tr>';
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$output .= '<td ><table><tr><td colspan="2"><strong>' . $aRow['transgene'] . '</strong></td></tr>';
		$output .= '<tr><td>Allele:</td><td>' . $aRow['allele'] . '</td></tr>';
		$output .= '<tr><td>Strain:</td><td>' . $aRow['strain'] . '</td></tr></table></td>'; 
	}
	$output .=  '</table>';
	return $output;
} 
	$gaSql['user']       = $_SESSION["db_username"];
	$gaSql['password']   = $_SESSION["db_password"];
	$gaSql['db']         = $_SESSION["database"];
	$gaSql['server']     = $_SESSION["hostname"];
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_POST['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_POST['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_POST['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	$database_fields = explode(',',$_POST['datatables_field_wtables']);
	if ( $_POST['sSearch'] != "" )
	{
		$sWhere = " AND ("; 
		for ( $i=0 ; $i<count($database_fields) ; $i++ )
		{
			if ($database_fields[$i] == "survival"){
				$sWhere .= 'concat(convert(
CAST(IF(starting_nursery >= current_adults,round(current_adults / starting_nursery,4)*100 ,
\'\') as UNSIGNED) USING latin1),\'%\') like \'%'.mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
			}else{
				$sWhere .= $database_fields[$i]." LIKE '%".mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	
	for ( $i=0 ; $i<count($database_fields) ; $i++ )
	{
		if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = " AND ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $database_fields[$i]." LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
		}
	}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 SQL_CALC_FOUND_ROWS
	 */ 
	$sQuery = "
		SELECT  SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $select_fields))."
		FROM   $sTable
		where 1=1 ";
		if ($_POST['datatables_where']){
			$sQuery .= " and " . $_POST['datatables_where'];
		}
		if ($sWhere){
			$sQuery .= " " . $sWhere; 
		} 
	$sQuery .= "  
		$sOrder
		$sLimit
	";   
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error()); 
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	 
	//dynamic column names don't have to match exactly to the select area of the sql statement
	if ($_POST['datatables_buttons'] == "no buttons"){
		array_shift($aColumns);
	}
while ( $aRow = mysql_fetch_array( $rResult ) )
	{ 
		$first_column = 1; 
		$index = 0;
		$row = array();   
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{ 	 
			if ($aColumns[$i] == "('empty')"){
				if ($_POST['datatables_index_col'] == "tank_ID"){
					$buttons = '<div style=" width:40px"><input align="bottom" type="image" width="12" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:550,width:550, title:\'Confirm\', content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_tank/r_' . $aRow['tank_ID'] . '\'}); return false" />  
							 <input align="bottom" type="image" width="16" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_tank/u_' . $aRow['tank_ID'] . '\'}); return false" /> </div>';
			 	}else{ 
					if (strstr($_POST['datatables_buttons'],"_user_access")){
						$url = str_replace('_user_access','',$_POST['datatables_buttons']);
						$buttons = '<div style=" width:40px"><input align="bottom" type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:800,width:800, content:\'' . $url . 'index.php/fish/modify_line/u_' . $aRow['batch_ID'] .'/showall\'}); return false" /> </div>';
					}else{
						$buttons = $html . '<div style=" width:68px; ">
						<input align="bottom" type="image" width="12" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_line/r_' . $aRow['batch_ID'] . '/showall\'}); return false" />
						<input align="bottom" type="image" width="16" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:800,width:800, content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_line/u_' . $aRow['batch_ID'] . '/showall\'}); return false" /> 
				 		 <a href="#"><img id="' . $aRow['batch_ID'] . '_genotypes" border=0 style="margin-bottom:-6px; padding:0px" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Magnifying-glass-32.png"></a></div>';
		 			}
				} 
				$row[$index] = $buttons;
				$index++;
				continue;  
			} 
			if ($aColumns[$i] == "entry_ID"){
				$buttons = $html . '<div style=" width:68px; ">
				<input align="bottom" type="image" width="12" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_line_wq/r_' . $aRow['entry_ID'] . '/showall\'}); return false" />
				<input align="bottom" type="image" width="16" src="' . $_POST['datatables_buttons'] . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $_POST['datatables_buttons'] . 'index.php/fish/modify_line_wq/u_' . $aRow['entry_ID'] . '/showall\'}); return false" /> 
				</div>';
				$row[$index] = $buttons;
				$index++;
				continue;  
			}
			if ($aColumns[$i] == "tank_ID" && $_POST['datatables_index_col'] == "location"){ 
				$buttons = '<a href="#"  onclick="displayVals(\'' . $aRow['tank_ID'] . '\',\'' . $aRow['location'] . '\',\'add_tank\');"><img border=0 src="' . $_POST['datatables_buttons'] . 'assets/Pics/Symbol-Add_48.png" width="16" ></a>';
				$row[$index] = $buttons;
				$index++;
				continue;
			}
			if ($aColumns[$i] == "birthday" || $aColumns[$i] == "death_date" || $aColumns[$i] == "date_taken" || $aColumns[$i] == "record_date"){
				 if ($aRow[ $aColumns[$i] ]){ 
					$row[$index] =  date('m/d/Y', $aRow[ $aColumns[$i] ]);
					$index++;	 
				 }	else{
					$row[$index] = "0/0/0";
					$index++;
				 }
				continue;
			}
			if ($aColumns[$i] == "survival_percent"){
				 if ($aRow[ $aColumns[$i] ]){ 
					$row[$index] =   $aRow[ $aColumns[$i] ] . "%";
					$index++;	 
				 }else{
					$row[$index] =  "0%";
					$index++;
				 }
				continue;
			}
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[$index] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[$index] = $aRow[ $aColumns[$i] ];
			}
			$index++;
			$mut_trans = get_mutant_transgene_data($gaSql['link'],$aRow['batch_ID']);
			$row['extra'] = $mut_trans;
		} 
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );


?>