<?php 
/* 
  ZeBase 
  Copyright 2010 Purdue University

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 
   @version    1.0.3, 2011-10-01
 */
session_start();
//make sure the user is logged in
$this->load->library('SimpleLoginSecure');  
if($this->session->userdata('logged_in')) { 	
}else{ 
	$html = '<script language="javascript">
	if (self.parent.Shadowbox != undefined) {
		alert("You are no longer logged in.  Please refresh your browser window and log in again.");
		self.parent.Shadowbox.close(); 
	}
	</script>
	You are not logged in.  Please click <a href="' . base_url() . 'index.php/fish/login">here</a> to login.'; 
	die($html);
}  
$_SESSION['base_url'] = base_url(); 
function sanitize($data){  
 // remove whitespaces (not a must though)  
 $data = trim($data);  
 // apply stripslashes if magic_quotes_gpc is enabled  
 if(get_magic_quotes_gpc()){  
 	$data = stripslashes($data);  
 }    
 // a mySQL connection is required before using this function  
 $data = mysql_real_escape_string($data);    
 return $data;
}
function create_qtip($id,$content){
	$html = '<script language="javascript">
	$(document).ready(function() 
{
   // Match all link elements with href attributes within the content div
   $(\'#' . $id . ' a[href]\').qtip(
   {
      content: \'' . $content . '\' ,
	  style:\'cream\' 
   });
});
 </script>';	
return $html;
} 	
function table_format($ID, $nopage,$title,$server,$datatables_fields,$datatables_select,$datatables_buttons,$datatables_from,$datatables_field_wtables,$datatables_where,$datatables_index_col){
	if ($server != ""){
		$server_process = '"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "' . $server . '",
			"fnServerData": function ( sSource, aoData, fnCallback ) { 
			aoData.push( { "name": "datatables_select", "value": "' . $datatables_select . '"} ); 
			aoData.push( { "name": "datatables_fields", "value": "' . $datatables_fields . '"} );
			aoData.push( { "name": "datatables_buttons", "value": "' . $datatables_buttons . '"   } );
			aoData.push( { "name": "datatables_from", "value": "' . $datatables_from . '"   } );
			aoData.push( { "name": "datatables_field_wtables", "value": "' . $datatables_field_wtables . '"   } ); 
			aoData.push( { "name": "datatables_where", "value": "' . $datatables_where . '"   } );
			aoData.push( { "name": "datatables_index_col", "value": "' . $datatables_index_col . '"   } );
			$.ajax( {
				"dataType": \'json\', 
				"type": "POST", 
				"url": sSource, 
				"data": aoData, 
				"success": fnCallback
			} );
			},';	
	}  
	if ($nopage == "1" && $title == ""){
		$html = '<script type="text/javascript"> 
		$(document).ready(function() {
			oTable = $(\'#' . $ID . '\').dataTable({
				' . $server_process .  '
				
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"aaSorting": [[1,\'asc\']], 
				"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] }],
				"bPaginate": false,
				"bFilter": false 
				});
		} );   
		</script>';
	}elseif ($nopage == "1"){
		$html = '<script type="text/javascript"> 
		$(document).ready(function() {
			oTable = $(\'#' . $ID . '\').dataTable({
				' . $server_process .  '
				
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"aaSorting": [[1,\'asc\']], 
				"bPaginate": false,
				"bFilter": false,
				"sDom": \'<"ui-widget-header">frtip\' 
			}); 
			$("div.ui-widget-header").html(\'<div style=" padding-left:400px; font-size:1.1em;">' . $title . '</div>\');
		});  
		</script>';
	}elseif ($title != ""){
		$html = '<script type="text/javascript"> 
		$(document).ready(function() {
			oTable = $(\'#' . $ID . '\').dataTable({
				' . $server_process .  '
				
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"aaSorting": [[1,\'asc\']], 
				"bPaginate": true,
				"bFilter": false,
				"sDom": \'<"' . $ID . '">frtip\' 
			}); 
			$("div.' . $ID . '").html(\'<div class="ui-widget-header"><div style=" padding-left:400px; font-size:1.1em;">' . $title . '</div></div>\');
		} );  
		</script>'; 
	}else{
		 $html = '<script type="text/javascript"> '; 
		 $html .= ' function fnFormatDetails (aData){
			 var sOut = aData[\'extra\'];
 
			return sOut;
		}';		
 
		$html .= '  $(document).ready(function() {
			oTable_' . $ID . ' = $(\'#' . $ID . '\').dataTable({
				' . $server_process .  '
				
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bPaginate": true,
				"aaSorting": [[1,\'asc\']],
				"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] }] } );';
		
		$html .= ' $(\'#' . $ID . ' tbody td div a img\').live( \'click\', function () {  
        var nTr = this.parentNode.parentNode.parentNode.parentNode;  
		var aData = oTable_' . $ID . '.fnGetData( nTr ); 
		//oTable_' . $ID . '.fnOpen(nTr, fnFormatDetails(nTr), \'details\' ); 
        if ( this.src.match(\'details_close\') ){ 
            this.src = "' . $datatables_buttons . 'assets/Pics/Magnifying-glass-32.png";
            oTable_' . $ID . '.fnClose( nTr );
        }else{ 
            this.src = "' . $datatables_buttons . 'assets/Pics/Magnifying-glass-details_close-32.png";
            oTable_' . $ID . '.fnOpen(nTr, fnFormatDetails(aData), \'details\' );
        } ';
	 $html .= '}); 
		 } ); 
		</script>';   
	} 
	return $html;		
}
function footer(){ 
	echo ' <div id="footer">
  	<div id="footerd1">
		<p><a href="mailto:webman&#97;ger@bio&#46;purdue&#46;edu">webmanager@bio.purdue.edu</a><br />
		Maintained by BIO-IT  </p>
	</div>
 	 <div id="footerd2">
			<p>Department of Biological Sciences, Purdue University <br />
		
			915 W. State Street,
		West Lafayette, IN 47907
		ph. (765) 494-4408 
		Fax (765) 494-0876<br />
				&copy; 2009 Purdue University. An equal
			access/equal opportunity university.  </p>
 	 </div>
  <!-- end #footerr --></div>  
';
}
function libraries($url){
?> 
 
<link rel="stylesheet" href="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/css/redmond/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" />

<script src="<?php echo $url ?>assets/functions/jquery/jquery-1.5.1.min.js" type="text/javascript"></script>

<!--multiple select box--> 
 	<link type="text/css" href="<?php echo $url ?>assets/functions/jquery/multiselect3.1/css/common.css" rel="stylesheet" />  
	 <link type="text/css" href="<?php echo $url ?>assets/functions/jquery/multiselect3.1/css/ui.multiselect.css" rel="stylesheet" /> 
	<script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/multiselect3.1/jquery-ui.min.js"></script> 
	<script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/multiselect3.1/js/ui.multiselect.js"></script>
    <script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/multiselect3.1/js/plugins/scrollTo/jquery.scrollTo-min.js"></script>


<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.core.js" type="text/javascript"></script>
<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.effects.core.js"></script> 	
<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.datepicker.js" type="text/javascript"></script>
<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.tabs.js" type="text/javascript"></script>
<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.button.js" type="text/javascript"></script>
<script src="<?php echo $url ?>assets/functions/jquery/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordion.js" type="text/javascript"></script>
<link href="<?php echo $url ?>assets/functions/jquery/shadowbox-3.0.3/shadowbox.css" rel="stylesheet" type="text/css" /> 
<script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/shadowbox-3.0.3/shadowbox.js"></script> 

 <!--adobe spry-->
<link rel="stylesheet" href="<?php echo $url ?>assets/functions/SpryAssets/SpryTabbedPanels.css" type="text/css" media="screen" />
<script src="<?php echo $url ?>assets/functions/SpryAssets/SpryTabbedPanels.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo $url ?>assets/functions/SpryAssets/SprySlidingPanels.css" type="text/css" media="screen" />
<script src="<?php echo $url ?>assets/functions/SpryAssets/SprySlidingPanels.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $url ?>assets/functions/shadedborder.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $url ?>assets/functions/jquery/DataTables-1.8.1/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $url ?>assets/functions/jquery/qtip2/jquery.qtip.css" />
<script type="text/javascript" src="<?php echo $url ?>assets/functions/jquery/qtip2/jquery.qtip.js"></script> 
 

<!-- Highslide code -->
<script type="text/javascript" src="<?php echo $url ?>assets/functions/Highcharts-2.1.9/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo $url ?>assets/functions/Highcharts-2.1.9/js/modules/exporting.js"></script> 
<script type="text/javascript" src="<?php echo $url ?>assets/functions/Highcharts-2.1.9/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="<?php echo $url ?>assets/functions/Highcharts-2.1.9/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $url ?>assets/functions/Highcharts-2.1.9/highslide/highslide.css" />   
<script type="text/javascript" src="<?php echo $url ?>assets/functions/Highcharts-2.1.9/js/themes/gray.js"></script> 
<script language="javascript">
 

function selectAllOptions(selStr){
		  var selObj = document.getElementById(selStr);
		  for (var i=0; i<selObj.options.length; i++) {
			selObj.options[i].selected = true;
		  }		   
} 
	$(function() {
		$(  "a.jq_buttons").button(); 
		  
	});
function moveCloseLink(){ 
    var cb=document.getElementById('sb-nav-close'); 
    var tb=document.getElementById('sb-title'); 
    if(tb) tb.appendChild(cb); 
} 	 
Shadowbox.init({ 
    players:    ["iframe"], 
	onOpen: moveCloseLink,
	animate: false	 
}); 
$(function(){ 
	 $(".multiselect").multiselect(); 
});  
  
//jquery iphone switch start
jQuery.fn.iphoneSwitch = function(start_state, switched_on_callback, switched_off_callback, options) {
	 var state = start_state == 'on' ? start_state : 'off';
	
	// define default settings
	var settings = {
		mouse_over: 'pointer',
		mouse_out:  'default',
		switch_on_container_path: '<?php echo $url ?>assets/switch/iphone_switch_container_on.png',
		switch_off_container_path: '<?php echo $url ?>assets/switch/iphone_switch_container_off.png',
		switch_path: '<?php echo $url ?>assets/switch/iphone_switch.png',
		switch_height: 27,
		switch_width: 94
	};

	if(options) {
		jQuery.extend(settings, options);
	}

	// create the switch
	return this.each(function() {

		var container;
		var image;
		
		// make the container
		container = jQuery('<div class="iphone_switch_container" style="height:'+settings.switch_height+'px; width:'+settings.switch_width+'px; position: relative; overflow: hidden"></div>');
		
		// make the switch image based on starting state
		image = jQuery('<img class="iphone_switch" style="height:'+settings.switch_height+'px; width:'+settings.switch_width+'px; background-image:url('+settings.switch_path+'); background-repeat:none; background-position:'+(state == 'on' ? 0 : -53)+'px" src="'+(state == 'on' ? settings.switch_on_container_path : settings.switch_off_container_path)+'" /></div>');

		// insert into placeholder
		jQuery(this).html(jQuery(container).html(jQuery(image)));

		jQuery(this).mouseover(function(){
			jQuery(this).css("cursor", settings.mouse_over);
		});

		jQuery(this).mouseout(function(){
			jQuery(this).css("background", settings.mouse_out);
		});

		// click handling
		jQuery(this).click(function() {
			if(state == 'on') {
				jQuery(this).find('.iphone_switch').animate({backgroundPosition: -53}, "slow", function() {
					jQuery(this).attr('src', settings.switch_off_container_path);
					switched_off_callback();
				});
				state = 'off';
			}
			else {
				jQuery(this).find('.iphone_switch').animate({backgroundPosition: 0}, "slow", function() {
					switched_on_callback();
				});
				jQuery(this).find('.iphone_switch').attr('src', settings.switch_on_container_path);
				state = 'on';
			}
		});		

	});
	
};
//jquery iphone switch end
	</script>
<style>
@import "<?php echo $url ?>assets/functions/jquery/DataTables-1.8.1/media/css/demo_table_jui.css";



 
.SlidingPanels {
	float: left;
	height:600px;
}
.SlidingPanelsContentGroup {
	float: left;
	width: 10000px; 
}
.SlidingPanelsContent {
	float: left;
	width: 1200px; 
	
	height:600px;
} 
.SlidingPanelsAnimating * {
	overflow: hidden !important;
} 
.SlidingPanelsCurrentPanel {
}
 
.SlidingPanelsFocused {
}
 

#batch { margin: 30px 0;
box-shadow: -5px -5px 5px  #000;
-o-box-shadow: -5px -5px 5px #000;
-icab-box-shadow: -5px -5px 5px #000;
-khtml-box-shadow: -5px -5px 5px #000;
-moz-box-shadow: -5px -5px 5px #000;
-webkit-box-shadow: -5px -5px 5px #000;
border-radius: 5px;
-o-border-radius: 5px;
-icab-border-radius: 5px;
-khtml-border-radius: 5px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
padding: 5px 5px 5px 15px;
background-color: #eee;
width: 90%;} 
 
 
 .ui-tabs-vertical { width: 55em;  }
.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em;background:#999; height:550px;  }
.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
.ui-tabs-vertical .ui-tabs-panel { padding: 1em; padding-left:180px; width: 40em;}
</style>   
<![if IE]>
<style>
#standard_box { 
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#E6E6FA');
}
#accented_box { 
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#e6f3fa');
}
#plain_box { 
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#D3D3D3');
border: 0px solid #FFFFFF;
background-color: #FFFFFF;
padding: 10px;
font-family: Verdana, Geneva, sans-serif;
font-size: 12pt;
color: #000000;
box-shadow: 6px 6px 9px #000000;
behavior: url(<?php echo $url ?>assets/ie-css3.htc);
}
</style>
<![endif]>

<![if !IE]>
<style>
#standard_box { -webkit-border-radius: 23px; 
-moz-border-radius: 23px; 
border-radius: 23px; 
-webkit-box-shadow: 2px 2px 21px #808080; 
-moz-box-shadow: 2px 2px 21px #808080; 
box-shadow: 2px 2px 21px #808080; 
background-image: -moz-linear-gradient(top, #FFFFFF, #D3D3D3); 
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0.0, #FFFFFF), color-stop(1.0, #E6E6FA)); 
border: 0px solid #90EE90; 
background-color: #FFFF00; 
padding: 10px; 
font-family: Verdana, Geneva, sans-serif; 
font-size: 1em; 
color: #888888; 
text-align: left;}
#accented_box { -webkit-border-radius: 23px; 
-moz-border-radius: 23px; 
border-radius: 23px; 
-webkit-box-shadow: 2px 2px 21px #808080; 
-moz-box-shadow: 2px 2px 21px #808080; 
box-shadow: 2px 2px 21px #808080; 
background-image: -moz-linear-gradient(top, #FFFFFF, #e6f3fa); 
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0.0, #FFFFFF), color-stop(1.0, #e6f3fa)); 
border: 0px solid #90EE90; 
background-color: #FFFF00; 
padding: 10px; 
font-family: Verdana, Geneva, sans-serif; 
font-size: 1em; 
color: #888888; 
text-align: left;}
#plain_box{-webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px;
-webkit-box-shadow: 6px 6px 9px #000000;
-moz-box-shadow: 6px 6px 9px #000000;
box-shadow: 6px 6px 9px #000000;
background-image: -moz-linear-gradient(top, #FFFFFF, #E6E6FA);
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0.0, #FFFFFF), color-stop(1.0, #D3D3D3));
border: 0px solid #FFFFFF;
background-color: #FFFFFF;
padding: 10px;
font-family: Verdana, Geneva, sans-serif;
font-size: 12pt;
color: #000000;
behavior: url(<?php echo $url ?>assets/ie-css3.htc);
}

#wq_vtabs ul li a{
	width:100%;
	text-align:left;
}
#vtabs ul li a{
	width:100%;
	text-align:left;
}
</style>
<![endif]>


  	<?php	
}
function output_cal_func($field_name, $curval,$ID){
	if (trim($curval) == ""){
		$curval = "";
	}elseif ($curval == "empty"){
		$curval = "";
	}else{
		$curval = date('m/d/Y',$curval);
	} 
		 
	$html .= '<script type="text/javascript">
    $(function() {
		$(\'#' . $ID . '\').datepicker({
			yearRange: "1999:2012",
			changeMonth: true,
			changeYear: true
		});
	});
	
	</script>'; 
 
	 $html .= '<input id="' . $ID . '" name="' . $field_name . '"   type="text"    value="'. $curval . '"/>';	   
	 return $html;
} 
function excel_search_results($query,$url){
			$i = "0";
			set_include_path(getcwd() . '/assets/functions');
			include 'PHPExcel/PHPExcel.php'; 
			include 'PHPExcel/Writer/Excel2007.php';
			 
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "xls";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;
			
			$objPHPExcel = new PHPExcel();			 
			$objPHPExcel->setActiveSheetIndex(0);			 
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A1','Batch #');				 
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);			
			$objPHPExcel->getActiveSheet()->SetCellValue('B1','Gender');				 
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('C1','Name');				 
			$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D1','Status');				 
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('E1','Birthday');				 
			$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('F1','User');				 
			$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('G1','Lab');				 
			$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H1','Strain');				 
			$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I1','Mutant Name');				 
			$objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('J1','Mutant Allele');				 
			$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('K1','Transgene Allele');				 
			$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('L1','Generation');				 
			$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('M1','Current Adults');				 
			$objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('N1','Start Adults');				 
			$objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('O1','Cur Nursery');				 
			$objPHPExcel->getActiveSheet()->getStyle('O1')->getFont()->setBold(true); 
			$objPHPExcel->getActiveSheet()->SetCellValue('P1','Start Nursery');				 
			$objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true); 
			
			$i="2"; 
		 	$numfields = count($query);	 
			foreach ($query as $row){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'. $i,$row['batch_ID']);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'. $i,$row['gender']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'. $i,$row['name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'. $i,$row['status']);
				if ($row['birthday']){
					$objPHPExcel->getActiveSheet()->SetCellValue('E'. $i,date('m/d/Y',$row['birthday']));
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('F'. $i,$row['username']);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'. $i,$row['lab_name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'. $i,$row['strain']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'. $i,$row['mutant']);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'. $i,$row['mutant_allele']);
				$objPHPExcel->getActiveSheet()->SetCellValue('K'. $i,$row['transgene_allele']);
				$objPHPExcel->getActiveSheet()->SetCellValue('L'. $i,$row['generation']);
				$objPHPExcel->getActiveSheet()->SetCellValue('M'. $i,$row['current_adults']);
				$objPHPExcel->getActiveSheet()->SetCellValue('N'. $i,$row['starting_adults']);
				$objPHPExcel->getActiveSheet()->SetCellValue('O'. $i,$row['current_nursery']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('P'. $i,$row['starting_nursery']);   
				$i++; 			
			}  
			 
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save($tempname);			 
 
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
}
function excel_data($objPHPExcel,$query,$category_title){
	$i = $_SESSION['record_index'];
	//$numfields = count($query);			
	foreach ($query as $row){
		 $letter_index = 0;
		 //switch the letter value after z to zz
		 $letter_switch = -1;
		 $titles = "";
		 foreach ($row as $key => $value){
			if ($key == "strain_ID" || $key == "mutant_ID" || $key == "transgene_ID"){ 
			}else{
					 $titles[] = $key;
					 $letter = "A";
					 if ($letter_switch != -1){
						 $letter_value = chr(ord($letter)+ $letter_index);
						 $letter_value =  "A" . $letter_value;
					 }else{
						$letter_value = chr(ord($letter)+ $letter_index); 
					 }
					  if ($key == "birthday"){ 
						$objPHPExcel->getActiveSheet()->SetCellValue($letter_value . $i,date('m/d/Y',$value));
					 
					  }else{
						 $objPHPExcel->getActiveSheet()->SetCellValue($letter_value . $i,$value);	
					  }
					if ($letter_value == "Z"){
						$letter_switch = 1;
						$letter_index = -1;
					}				 
					$letter_index++;
			}
		}	
		 $i++; 			
	} 
		$letter_index = 0;	
		$letter_switch = 0;
		$record_index = $_SESSION['record_index'] -2;		
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $record_index,$category_title);				 
		$objPHPExcel->getActiveSheet()->getStyle('C' . $record_index)->getFont()->setBold(true);	
  	    $record_index++;
		foreach ($titles as $value){
			if ($value == "strain_ID" || $value == "mutant_ID" || $value == "transgene_ID"){
			}else{
				 $letter = "A";				 
				 switch ($value) {
					case  "batch_ID":
						 $value = "Batch Number";
						 break;
					case  "starting_adults":
						 $value = "Starting Adults";
						 break;	
					case  "current_adults":
						 $value = "Current Adults(Alive,Sick)";
						 break;
					case  "starting_nursery":
						 $value = "Nursery Quantity";
						 break;
				 }
				 if ($letter_switch == 1){
					 $letter_value = chr(ord($letter)+ $letter_index);
					 $letter_value =  "A" . $letter_value;
				 }else{
					$letter_value = chr(ord($letter)+ $letter_index); 
				 }			 
				 $objPHPExcel->getActiveSheet()->SetCellValue($letter_value . $record_index,$value);				 
				 $objPHPExcel->getActiveSheet()->getStyle($letter_value . $record_index)->getFont()->setBold(true);				  
				if ($letter_value == "Z"){
					$letter_switch = 1;
					$letter_index = -1;
				}				
				$letter_index++;
			}
		}
		$_SESSION['record_index'] = $i + 4;
	return $objPHPExcel;
}
function excel_survival_stat($query,$url){
			$i = "0"; 
			set_include_path(getcwd() . '/assets/functions');
			include 'PHPExcel/PHPExcel.php'; 
			include 'PHPExcel/Writer/Excel2007.php';
			 
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "xls";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			
			$objPHPExcel = new PHPExcel();			 
			$objPHPExcel->setActiveSheetIndex(0);			 
			$i="2"; 
			$objPHPExcel->getActiveSheet()->SetCellValue('A1','Batch #');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1','Start Nursery');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1','Cur Adults');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1','Start Adults');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1','Lab');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1','Status');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1','Survival Rate');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1','Birthday');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1','Date of Death');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1','Report Date'); 
			foreach ($query as $key_outer => $row){
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i,$row['batch_ID']);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i,$row['starting_nursery']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i,$row['current_adults']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i,$row['starting_adults']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i,$row['lab_name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i,$row['status']);
			 if ($row['starting_nursery'] == "" || $row['starting_nursery'] == "0"){
				   if ($row['starting_adults'] == "" || $row['starting_adults'] == "0"){
						$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i,'0%'); 
				   }else{							 
						$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i,round($row['current_adults'] /  $row['starting_adults'],4) * 100 . '%');
				   }
			   }else{ 
				  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i,round($row['current_adults'] / $row['starting_nursery'],4) * 100 . '%');
			   }			
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i,date('m/d/Y',$row['birthday']));
				if ($row['death_date']){
					$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i,date('m/d/Y',$row['death_date']));
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i,date('m/d/Y',$row['date_taken']));
				$i++; 
			} 
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save($tempname);			 
 
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
} 
 
function excel_output_lab_all($query,$url){  
			$title_array = array('Batch #','Name','Birthday','User','Lab','Strain','Mutant Name','Transgene Name','Generation','Cur Adults','Start Nursery');
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){
				$i = 0;  
				foreach ($query->result_array() as $row){  
					fputcsv($fp, $row); 
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype); 
}  
function excel_quantity_output($query,$url){
			$i = "0";
			set_include_path(getcwd() . '/assets/functions');
			include 'PHPExcel.php';  
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "xls";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype; 
			$objPHPExcel = new PHPExcel();			 
			$objPHPExcel->setActiveSheetIndex(0);
			$_SESSION['record_index'] = "3"; 
			$objPHPExcel = excel_data($objPHPExcel,$query['user_quant'], 'Quantity Summary');
			$objPHPExcel = excel_data($objPHPExcel,$query['mutant_quant'], 'Mutant Summary');
			$objPHPExcel = excel_data($objPHPExcel,$query['strain_quant'], 'Strain Summary');
			$objPHPExcel = excel_data($objPHPExcel,$query['transgene_quant'], 'Transgene Summary'); 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($tempname); 
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
}
function excel_output($query,$url){
			$i = "0";
			set_include_path(getcwd() . '/assets/functions');
			include 'PHPExcel/PHPExcel.php'; 
			include 'PHPExcel/Writer/Excel2007.php';
			 
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "xls";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;			
			$objPHPExcel = new PHPExcel();			 
			$objPHPExcel->setActiveSheetIndex(0);			 
			$i="2";
		 	$numfields = $query->num_fields(); 
			foreach ($query->result_array() as $row){
				 $letter_index = 0;
				 //switch the letter value after z to zz
				 $letter_switch = -1;
				 $titles = "";
				 foreach ($row as $key => $value){
					 $titles[] = $key;
					 $letter = "A";
					 if ($letter_switch != -1){
						 $letter_value = chr(ord($letter)+ $letter_index);
						 $letter_value =  "A" . $letter_value;
					 }else{
						$letter_value = chr(ord($letter)+ $letter_index); 
					 }
					   if ($key == "birthday"){ 
						$objPHPExcel->getActiveSheet()->SetCellValue($letter_value . $i,date('m/d/Y',$value)); 
					  }else{
					 	$objPHPExcel->getActiveSheet()->SetCellValue($letter_value . $i,$value);	
					  }
					if ($letter_value == "Z"){
						$letter_switch = 1;
						$letter_index = -1;
					}				 
					$letter_index++;
				}	
				 $i++; 			
			}  
			$letter_index = 0;	
			$letter_switch = 0;
			foreach ($titles as $value){
				 $letter = "A";				 
				 if ($value == "batch_ID"){
					 $value = "Batch Number";
				 }
				 if ($letter_switch == 1){
					 $letter_value = chr(ord($letter)+ $letter_index);
					 $letter_value =  "A" . $letter_value;
				 }else{
					$letter_value = chr(ord($letter)+ $letter_index); 
				 }
				 $objPHPExcel->getActiveSheet()->SetCellValue($letter_value . '1',$value);				 
				 $objPHPExcel->getActiveSheet()->getStyle($letter_value . '1')->getFont()->setBold(true);				  
				if ($letter_value == "Z"){
				 	$letter_switch = 1;
					$letter_index = -1;
				}				
				$letter_index++;
			}
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save($tempname);			 
 
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
}
function all_lines_prev($url,$data){ 
			  $tableID = "fish_table";
			  $html .= table_format($tableID,'1','All Zebrafish',$url . 'assets/server_processing.php',addslashes($data['search_options']['datatables_fields']),addslashes($data['search_options']['datatables_select']),'no buttons',addslashes($data['search_options']['datatables_from']),addslashes($data['search_options']['datatables_field_wtables']),addslashes($data['search_options']['datatables_where']),'batch_ID');
	 		  $html .=  '<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
									<thead>';	
				$html .= '<tr > 
					   <th>Batch&nbsp;#</th>
					   <th>Name</th>
					   <th>Birthday</th>
					   <th>User</th>
					   <th>Lab</th>
					   <th>Strain</th> 
					   <th>Generation</th>
					   <th>Cur Adults</th>
					   <th>Start Nursery</th>			   
				</tr></thead><tbody>	
				</tbody></table> '; 
				echo $html; 
}
function all_wq_prev($url,$data){ 
			  $tableID = "wq";
			  $html .= table_format($tableID,'1','Water Quality',$url . 'assets/server_processing.php',addslashes($data['datatables_fields']),addslashes($data['datatables_select']),'no buttons',addslashes($data['datatables_from']),addslashes($data['datatables_field_wtables']),addslashes($data['datatables_where']),'record_date');
	 		  $html .=  '<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
									<thead>';	
				$html .= '<tr > 
					    <th>System Name</th>
				   <th>Location</th>
				   <th>Nitrate</th>
				   <th>Nitrite</th>
				   <th>pH</th> 
				   <th>Conductivity</th> 
				   <th>D.O.</th>
				   <th>Temperature</th>
				   <th>Record Date</th>			  		   
				</tr></thead><tbody>	
				</tbody></table> '; 
				echo $html; 
}
function all_lab_prev($url,$data){  
			   $tableID = "fish_table";
		 	   $html .= table_format($tableID,'1','My Lab Fish',$url . 'assets/server_processing.php',addslashes($data['search_options']['datatables_fields']),addslashes($data['search_options']['datatables_select']),'no buttons',addslashes($data['search_options']['datatables_from']),addslashes($data['search_options']['datatables_field_wtables']),addslashes($data['search_options']['datatables_where']),'batch_ID');
	 		   $html .=  '<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
									<thead>';	
				$html .= '<tr > 
				   <th>Batch&nbsp;#</th>
				   <th>Name</th>
				   <th>Birthday</th>
				   <th>User</th> 
				   <th>Strain</th> 
				   <th>Generation</th>
				   <th>Cur Adults</th>
				   <th>Start Nursery</th>			   
				</tr></thead><tbody>	
				</tbody></table> '; 
				echo $html; 
}
function quantity_summary_prev($url,$query){ 
			   $tableID = "user_sum";
			   $html .= table_format($tableID,'1','','','','','','','','',''); 			 			 	 
			   $html .=  '<div style="height:150px;"><h2>Batch Summary</h2>
			   <table style="font-size:.8em;" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
			   $html .= '<tr ><th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
				   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';					    
			  foreach ($query['user_quant'] as $key => $row){
				   $html .= '<tr>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
			   } 				 
			   $html .= '</tbody> </table></div>';
			   echo $html; 	 
			   $tableID = "mutant_quant";
			   $html = table_format($tableID,'1','','','','','','','','',''); 			 			 	 
			   $html .=  '<div style="height:250px;"><h2>Mutant Summary</h2>
			   <table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';   
			   $html .= '<tr ><th >Mutant</th>
			   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
			   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';			   
			   foreach ($query['mutant_quant'] as $key => $row){
			       $html .= '<tr>';
				   $html .= '<td>' .$row->mutant. '</td>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
			   } 				 
				$html .= '</tbody> </table></div>';
			   echo $html; 
			   $tableID = "strain_quant";
			   $html = table_format($tableID,'1','','','','','','','','',''); 			 			 	 
			   $html .=  '<div style="height:250px;"><h2>Strain Summary</h2>
			   <table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
			   $html .= '<tr ><th >Strain</th>
			   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
			   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';					    
			   foreach ($query['strain_quant'] as $key => $row){
				   $html .= '<tr>';
				   $html .= '<td>' .$row->strain. '</td>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
			   } 				 
			   $html .= '</tbody> </table></div>';
			   echo $html; 
			   $tableID = "transgene_quant";
			   $html = table_format($tableID,'1','','','','','','','','',''); 			 			 	 
			   $html .=  '<div style="height:150px;"><h2>Transgene Summary</h2>
			   <table style="font-size:.8em; " class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
			   $html .= '<tr ><th >Promoter</th>
			   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
				   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';					    
			   foreach ($query['transgene_quant'] as $key => $row){
				   $html .= '<tr>';
				   $html .= '<td>' .$row->promoter. '</td>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
			   } 				 
				$html .= '</tbody> </table></div>';
				echo $html; 
}
function batch_summary_prev($url,$search_options){   
			$tableID = "fish_table";  
			$search_options = $_SESSION['search_options'];
			$search_options['datatables_select'] = str_replace("('empty'),",'',$search_options['datatables_select']);
			$search_options['datatables_field_wtables'] = str_replace("('empty'),",'',$search_options['datatables_field_wtables']);
			$search_options['datatables_fields'] = str_replace("('empty'),",'',$search_options['datatables_fields']); 
			//$_SESSION['datatables_fields'] = array('batch_ID','gender','name', 'status','birthday','death_date', 'username', 'strain','mutant', 'transgene','generation', 'current_adults', 'starting_nursery');
			$html .= table_format($tableID,'1','Batch Summary',$url . 'assets/server_processing.php',addslashes($search_options['datatables_fields']),addslashes($search_options['datatables_select']),addslashes($search_options['datatables_buttons']),addslashes($search_options['datatables_from']),addslashes($search_options['datatables_field_wtables']),addslashes($search_options['datatables_where']),'batch_ID');  
			$html .=  '	<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>'; 
			$html .= '<tr ><th style=" width:5%">Batch&nbsp;#</th>
				  <th>Name</th>
				  <th>Status</th><th  >Birthday</th>
				  <th  >Date of Death</th>
				  <th  >User</th><th>Strain</th> 
				  <th  >Generation</th>
				  <th  >Cur Adults</th> 
				  <th  >Start Nursery</th>';
			$html .= '</tr></thead><tbody> 	
						</tbody></table> ';	 
			echo $html; 
}
function search_prev($url,$search){  
			$tableID = "fish_table";
		 	$html .= table_format($tableID,'1','Search Results',$url . 'assets/server_processing.php',addslashes($search['datatables_fields']),addslashes($search['datatables_select']),addslashes($search['datatables_buttons']),addslashes($search['datatables_from']),addslashes($search['datatables_field_wtables']),addslashes($search['datatables_where']),'fish.batch_ID'); 
			$html .=  '	<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>'; 
			$html .= '<tr ><th >Batch&nbsp;#</th>
			 <th>Name</th>
			  <th>Status</th><th  >Birthday</th>
			  <th  >User</th><th  >Lab</th>
			  <th>Strain</th> 
			  <th  >Generation</th>
			  <th  >Cur Adults</th><th  >Start Nursery</th></tr>';
			$html .= '</tr></thead><tbody> 	
						</tbody></table> ';	 
			echo $html;  
}
function survivalstat_prev($url,$search_options_track){   
			  $tableID = "survivalstat_prev"; 
			  $_SESSION['track_datatables_buttons']  = '';
			  $_SESSION['track_datatables_fields'] = array('batch_ID','starting_nursery','current_adults','starting_adults', 'lab_name', 'status','survival_percent','birthday', 'death_date','date_taken');
		 	  $html .= table_format($tableID,'1','Track Survival Percentage',$url . 'assets/server_processing.php',addslashes($search_options_track['track_datatables_fields']),addslashes($search_options_track['track_datatables_select']),addslashes($search_options_track['track_datatables_buttons']),addslashes($search_options_track['track_datatables_from']),addslashes($search_options_track['track_datatables_field_wtables']),addslashes($search_options_track['track_datatables_where']),'STAT.batch_ID'); 
			  $html .=  '	<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
			   $html .= '<tr ><th style=" width:2%">Batch&nbsp;#</th>
			   <th>Start Nursery</th>
				  <th>Cur Adults</th><th>Start Adults</th>
				  <th>Lab</th>	<th>Status</th>					  
				  <th>Survival Rate</th><th  >Birthday</th>
				  <th>Date of Death</th>	<th  >Report Date</th> ';			  		 
			   $html .= '</tr></thead><tbody></tbody> </table>';	 
			   echo $html; 				 			     
}
function survivalcurrent_prev($url,$search_options_survival){  
			  $tableID = "survivalcurrent_prev";
			   $html .= table_format($tableID,'1','Current Survival',$url . 'assets/server_processing.php',addslashes($search_options_survival['survival_datatables_fields']),addslashes($search_options_survival['survival_datatables_select']),addslashes($search_options_survival['survival_datatables_buttons']),addslashes($search_options_survival['survival_datatables_from']),addslashes($search_options_survival['survival_datatables_field_wtables']),addslashes($search_options_survival['survival_datatables_where']),'batch_ID'); 
			   $html .=  '	<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
				$html .= '<tr >  
				<th>Batch&nbsp;#</th>
				<th>User</th> 
				<th>Lab</th>
				<th>Cur Adults</th><th>Start Adults</th><th>Start Nursery</th>
				<th>Cur Nursery</th>
				<th>Birthday</th>
				<th>Survival Rate</th>
				</thead><tbody>	
				</tbody></table> ';	
	 
			   echo $html; 				 			     
}
function show_all_fish($url,$allfish,$admin_access,$data){  
				$_SESSION['preview_show_all_fish']="";
				$tableID = "all_fish_table";
				 if ($admin_access != "on"){
					 $data['search_options']['datatables_buttons']  .= "_user_access";
				 }
				 $html .= table_format($tableID,'2','',$url . 'assets/server_processing.php',addslashes($data['search_options']['datatables_fields']),addslashes($data['search_options']['datatables_select']),addslashes($data['search_options']['datatables_buttons']),addslashes($data['search_options']['datatables_from']),addslashes($data['search_options']['datatables_field_wtables']),addslashes($data['search_options']['datatables_where']),'batch_ID'); 
	 			echo '<table ><tr><td> 
			   <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_fish_48.png" name="doit" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:680,width:650, content:\'' . $url . 'index.php/fish/modify_line/n_/showall\'}); return false" />
		 	  <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/all\'"/>
			  <a  href="' . $url . 'index.php/fish/print_prev_all" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
		  	  </td></tr></table>'; 
				   $html .= ' <div style="padding:20px;"><h2>All Zebrafish</h2> '; 
				   $html .=  '<table style="font-size:.8em; " class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
									<thead>';
				   $html .= '<tr ><th></th>
							   <th>Batch&nbsp;#</th>
							   <th>Name</th>
							   <th>Birthday</th>
							   <th>User</th>
							   <th>Lab</th>
							   <th>Strain</th> 
							   <th>Generation</th>
							   <th>Cur Adults</th>
							   <th>Start Nursery</th>			   
				</tr></thead><tbody><tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>	
				</tbody></table></div>
				 '; 
				echo $html; 
}

function show_lab_fish($url,$allfish,$loggedin_user,$admin_access,$search_options){ 
	  			$_SESSION['preview_show_lab_fish']="";
				$tableID = "lab_fish_table"; 
				 if ($admin_access != "on"){
					 $search_options['datatables_buttons']  .= "_user_access";
		 		 }
				$html .= table_format($tableID,'2','',$url . 'assets/server_processing.php',addslashes($search_options['datatables_fields']),addslashes($search_options['datatables_select']),addslashes($search_options['datatables_buttons']),addslashes($search_options['datatables_from']),addslashes($search_options['datatables_field_wtables']),addslashes($search_options['datatables_where']),'batch_ID');  
			 	$html .= '<table><tr><td> 
			   <input alt="add fish" title="add fish" type="image"  src="' . $url . 'assets/Pics/Symbol-Add_fish_48.png" name="doit"  value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:680,width:650, content:\'' . $url . 'index.php/fish/modify_line/n_\'}); return false" />
		 	  <input alt="all fish" title="all fish" type="image"  src="' . $url . 'assets/Pics/Fish-bowl-64.png" name="doit" style="padding-bottom:8px;" onClick="Shadowbox.open({player:\'iframe\', title:\'All Fish\',height:800,width:1000, content:\'' . $url . 'index.php/fish/show_all\'}); return false"> 
		   	  </td><td >
			  <input alt="excel export" title="excel export" type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/lab\'"/>
			  <a  alt="print view" title="print view" href="' . $url . 'index.php/fish/print_prev_lab" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
			</td><td style=" padding-left:80px">
					<table><tr><td>Scan Mode
					<div id="ajax"></div>
					<div id="1"></div> </td><td>
					<div id="scan_mode"><form name="scanning_batch"><table><tr><td><img border=0 width="44" src="' . $url . 'assets/Pics/scanner_icon.png"> 
					</td><td></td></tr></table>
					</form></div></td></tr></table>
			  </td></tr></table>';
			  $html .=  '<div style="overflow-x: auto; overflow-y: hidden;">	<table class="display" cellpadding="0" cellspacing="0" border="0" style="font-size:.8em" class="display" id="' . $tableID . '">
									<thead>';
				$html .= '<tr ><th></th>
				   <th>Batch&nbsp;#</th>
				   <th>Name</th>
				   <th>Birthday</th>
				   <th>User</th> 
				   <th>Strain</th> 
				   <th>Generation</th>
				   <th>Cur Adults</th>
				   <th>Start Nursery</th>			   
				</tr></thead><tbody><tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>	
				</tbody></table></div>'; 
				echo $html;  
} 
function output_fields_new($refresh, $batch_ID,$data){ 
	$html .=  ' 
	<div id="standard_box" style="width:550px;margin-top:30px; margin-left:30px;">';
	$attributes = array('id' => 'fish_form_ID','name' => 'fish_form');
	echo form_open('fish/db_update/i', $attributes); ?>   
	<?php 	  
	$html .= '<a href="#" onclick="fish_form.submit();" class="jq_buttons" style=" font-size:12px;">Insert</a>';	 
	$html .=  '<table cellpadding="9" style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td colspan="4">
	<table><tr><td>';
	$html .=  'Name: <br><input name="name" value="' . $refresh['name'] . '">';
	$html .= '</td> <td>'; 
 	$html .= 'Status:<br><select name="status">';
	$status_array = "";
	$status_array[0] = "Alive";
	$status_array[1] = "Dead";
	$status_array[2] = "Sick";	 
	foreach($status_array as $value){
		if ($value == $refresh['status']){
			$html .= '<option selected>' . $value . '</option>';
		}else{
			$html .= '<option>' . $value . '</option>';
		}
	}
	$html .= '</select>';
	$html .= '</td><td>';
	$html .= 'Gender:<br><select name="gender"><option></option>';
	$gender_array = "";
	$gender_array[0] = "M";
	$gender_array[1] = "F";	
	$gender_array[2] = "Mixed"; 	 
	foreach($gender_array as $value){
		if ($value == $refresh['gender']){
			$html .= '<option selected>' . $value . '</option>';
		}else{
			$html .= '<option>' . $value . '</option>';
		}
	}
	$html .= '</select>';
	$html .= '</td><td>';
	$html .=  'Strain: <br><select name="strain_ID"><option></option>';
 	foreach ($data['all_strains']->result() as $row){
		if ($row->strain_ID == $refresh['strain_ID']){
			$index="1";
			$html .=  '<option value="' . $row->strain_ID  . '" selected>' . $row->strain  . '</option>';
		}else{
			$html .=  '<option value="' . $row->strain_ID  . '">' . $row->strain . '</option>';
		}	
	 }  
	$html .=  '</select>';
	$html .= '</td><td>';
	$html .=  'User: <br><select name="user_ID"><option></option>';
 	foreach ($data['all_users']->result() as $row){
		if ($row->user_ID == $data['loggedin_user_ID']){
			$index="1";
			$html .=  '<option value="' . $row->user_ID  . '" selected>'   . $row->username  . '</option>';
		}else{
			$html .=  '<option value="' . $row->user_ID  . '">' . $row->username . '</option>';
		}	
	 }  
	$html .=  '</select></td></tr></table>'; 	
	$html .= '</td></tr><tr><td colspan="7"> 
	<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td>';
		$html .=  'Mother: <br><input name="mother_ID" value="' . $refresh['mother_ID'] . '" type="text"></td><td>';
		$html .=  'Room: <br><select name="room"><option></option>';
		foreach ($data['all_rooms']->result() as $row){
			if ($row->room == $refresh['room']){
				$index="1";
				$html .=  '<option selected>'   . $row->room  . '</option>';
			}else{
				$html .=  '<option>' . $row->room . '</option>';
			}	
		 }  
		$html .=  '</select>'; 
		$html .=  '</td></tr></table>';  
	$html .= '</td></tr><tr><td>';	 
	$html .=  'Father: <br><input name="father_ID" value="' . $refresh['father_ID'] . '" type="text">'; 
	$html .= '</td></tr><tr><td>';
	$gen_array = "";
	$gen_array[0] = "outcross/F0";
	$gen_array[1] = "F1";
	$gen_array[2] = "F2";
	$gen_array[3] = "F3";
	$gen_array[4] = "F4";
	$gen_array[5] = "F5";
	$html .= 'Generation: <br><select name="generation"><option></option>';
	foreach($gen_array as $value){
		if ($value == $refresh['generation']){
			$html .= '<option selected>' . $value . '</option>';
		}else{
			$html .= '<option>' . $value . '</option>';
		}
	}
	$html .= '</select>';
	$html .= '</td><td>';
	$html .= 'Birthday: <br>';
	$birthday =  $refresh['birthday'];
	$html .= output_cal_func('birthday', $birthday,'birthday');		 
	$html .= '</td><td>';
	$html .= 'Date of Death: <br><div  style=" position:static;">';
	$death_date =  $refresh['death_date'];
	$html .= output_cal_func('death_date', $death_date,'death_date');	
	$html .= '</div>'; 
	$html .= '</td></tr><tr><td colspan="4">'; 
		$html .= '<table><tr><td></td><td>Qty</td></tr>
		<tr><td>
		Current Adults: </td><td><input size="5" type="text" name="current_adults" id="current_adults" value="' . $refresh['current_adults'] . '">';
		$html .= '</td></tr><tr><td>';
		$html .= 'Starting Adults: </td><td><input size="5" type="text" name="starting_adults" id="starting_adults" value="' . $refresh['starting_adults'] . '">';
		$html .= '</td></tr><tr><td>';
		$html .= 'Current Nursery: </td><td><input size="5" type="text" id="current_nursery" name="current_nursery" value="' . $refresh['current_nursery'] . '">';	
 		$html .= '</td></tr><tr><td>';
		$html .= 'Starting Nursery: </td><td><input size="5" type="text" id="starting_nursery" name="starting_nursery" value="' . $refresh['starting_nursery'] . '">';	
		$html .= '</td></tr></table>'; 		
	$html .= '</td></tr><tr><td colspan=4>';
	$html .= 'Comment:<br><textarea name="comments" cols="60" rows="5">' . $refresh['comments'] . '</textarea>';
	$html .= '</td></tr></table>';
	$html .= '</form></td></tr></table>';
	$html .= '</div> ';
	echo $html;
} 
function output_wq_fields_new(){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'wq_form_ID','name' => 'wq_form');
	echo form_open('fish/db_update_wq/n', $attributes); ?>                            
	 <?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="submit_doc()">Insert</a>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>System Name:</td><td><input type="text" name="system_name" value=""></td></tr>';
	$html .= '<tr><td>Location:</td><td><input type="text" name="location" value=""></td></tr>';
	$html .= '<tr><td>Nitrate:</td><td><input type="text" name="nitrate" value=""></td></tr>';
	$html .= '<tr><td>Nitrite:</td><td><input type="text" name="nitrite" value=""></td></tr>';
 	$html .= '<tr><td>pH:</td><td><input type="text" name="ph" value=""></td></tr>';
	$html .= '<tr><td>Conductivity:</td><td><input type="text" name="conductivity" value=""></td></tr>';
	$html .= '<tr><td>D.O.</td><td><input type="text" name="do" value=""></td></tr>';
	$html .= '<tr><td>Temperature:</td><td><input type="text" name="temperature" value=""></td></tr>'; 
	$html .= '</table></form> '; 
	$html .= '</div></div>
	<script language="javascript">
	function submit_doc(){ 
			document.wq_form.submit(); 
	}
	</script>'; 
	echo $html;
}
function output_wq_fields($selected_entry,$url){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'wq_form_ID','name' => 'wq_form');
	$html .= form_open('fish/db_update_wq/u', $attributes);  
	$html .= form_hidden('entry_ID',$selected_entry['entry_ID']); 
	$html .= '<a href="#" class="jq_buttons" onclick="submit_doc()">Update</a>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>System Name:</td><td><input type="text" name="system_name" value="' .$selected_entry['system_name'] . '"></td></tr>';
	$html .= '<tr><td>Location:</td><td><input type="text" name="location" value="' .$selected_entry['location'] . '"></td></tr>';
	$html .= '<tr><td>Nitrate:</td><td><input type="text" name="nitrate" value="' .$selected_entry['nitrate'] . '"></td></tr>';
	$html .= '<tr><td>Nitrite:</td><td><input type="text" name="nitrite" value="' .$selected_entry['nitrite'] . '"></td></tr>';
 	$html .= '<tr><td>pH:</td><td><input type="text" name="ph" value="' .$selected_entry['ph'] . '"></td></tr>';
	$html .= '<tr><td>Conductivity:</td><td><input type="text" name="conductivity" value="' .$selected_entry['conductivity'] . '"></td></tr>';
	$html .= '<tr><td>D.O.</td><td><input type="text" name="do" value="' .$selected_entry['do'] . '"></td></tr>';
	$html .= '<tr><td>Temperature:</td><td><input type="text" name="temperature" value="' .$selected_entry['temperature'] . '"></td></tr>'; 
	$html .= '<tr><td>Record Date:</td><td>'; 	
	$record_date =  $selected_entry['record_date'];
	$html .= output_cal_func('record_date', $record_date,'record_date');	
	$html .= '</td></tr></table></form> '; 
	$html .= '</div></div>
	<script language="javascript">
	function submit_doc(){ 
			document.wq_form.submit(); 
	}
	</script>'; 
	echo $html;
} 
function output_genotype($name, $value){
	$genotype = ""; 
	switch(true){
		case strstr($name,"genotype_wildtype"): 
		if($value != ""){
			$genotype = " +/+, ";
		}
		break;
		case strstr($name,"genotype_heterzygous"):
		if($value != ""){
			$genotype = " +/-, ";
		}
		break;	
		case strstr($name,"genotype_homozygous"): 
		if($value != ""){
			$genotype = " -/-, ";
		}
		break;		
	} 
	
	return $genotype;
}
function output_fields($refresh, $batch_ID,$data,$url){ 
	//barcode search layer
	$html = '<div style="position:absolute;"><input type="hidden" name="batch_number" id="batch_num" ></div>';
	$html .=  '
	<script language="javascript">
	$(function() {  
		$("#update_tabs").tabs({selected:0});  
	});
	</script>';
	$html .= '<div id="update_tabs" style="height:565px; background-color:#F5EEDE" > 
           <ul>
           <li><a href="#tabs-0">Update Batch</a></li>
           <li><a href="#tabs-1">Batch Tanks</a></li> 
            </ul> 
            <div id="tabs-0" style=" background-color:#F5EEDE">';
	if ($data['batch_found'] != 1){
		echo '<h2>No Batch was found for number ' . $batch_ID . '</h2><a href="#"  id="nobatchfound"></a>
		<script language="javascript"> 
			setTimeout("document.getElementById(\'nobatchfound\').focus()",0); 
		</script>'; 
	}else{ 
		$html .=  '<a href="#" id="setfocus_var"></a>
		<div id="standard_box" style=" margin-left:-15px"><div style="width:685px; height:600px; overflow:auto">';  
		$attributes = array('id' => 'fish_form_ID','name' => 'fish_form','style' => 'display:inherit;padding-left:0px; margin-left:0px;');
		echo form_open('fish/db_update/u', $attributes); ?>                            
		<?=form_hidden('batch_ID',$batch_ID); ?>	
		<?php  
		$html .= '<input type="hidden" name="batch_ID" value="' . $refresh['batch_ID'] . '">';
		$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td>
		<table><tr><td>';
		$html .= '<h2 style=" font-size:1.3em">Batch Number: ' . $refresh['batch_ID'] . '</h2><h2 style=" font-size:1.3em">Name: ' . $refresh['name'] . '</h2>';  
		$html .=  '</td><td style=" padding-left:60px;">';
		$html .= '<a href="#" onclick="fish_form.submit();" class="jq_buttons" style=" font-size:1.2em;">Update</a>
		<a onClick="Shadowbox.open({player:\'iframe\', title:\'Label\',height:400,width:400, content:\'' . $url . 'index.php/fish/print_prev_label/' . $batch_ID . '\'}); return false"
		 href="#" target="_blank">Print Label</a></td></tr></table>';
		$html .= '</td></tr><tr><td colspan=8>
		 <table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td>';
		$html .=  'Name: <br><input name="name" value="' . $refresh['name'] . '">';
		$html .= '</td><td>'; 
		$html .= 'Status:<br><select name="status">';
		$status_array = "";
		$status_array[0] = "Alive";
		$status_array[1] = "Dead";
		$status_array[2] = "Sick";	 
		foreach($status_array as $value){
			if ($value == $refresh['status']){
				$html .= '<option selected>' . $value . '</option>';
			}else{
				$html .= '<option>' . $value . '</option>';
			}
		}
		$html .= '</select>'; 
		$html .= '</td><td>';
		$html .= 'Gender:<br><select name="gender"><option></option>';
		$gender_array = "";
		$gender_array[0] = "M";
		$gender_array[1] = "F";	
		$gender_array[2] = "Mixed"; 	 
		foreach($gender_array as $value){
			if ($value == $refresh['gender']){
				$html .= '<option selected>' . $value . '</option>';
			}else{
				$html .= '<option>' . $value . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '</td><td>';
		$html .=  'Strain: <br><select name="strain_ID"><option></option>';
		foreach ($data['all_strains']->result() as $row){
			if ($row->strain_ID == $refresh['strain_ID']){
				$index="1";
				$html .=  '<option value="' . $row->strain_ID  . '" selected>' . $row->strain  . '</option>';
			}else{
				$html .=  '<option value="' . $row->strain_ID  . '">' . $row->strain . '</option>';
			}	
		 }  
		$html .=  '</select>';
		$html .= '</td><td>';
		$html .=  'User: <br><select name="user_ID"><option></option>';
		foreach ($data['all_users']->result() as $row){
			if ($row->user_ID == $refresh['user_ID']){
				$index="1";
				$html .=  '<option value="' . $row->user_ID  . '" selected>'   . $row->username  . '</option>';
			}else{
				$html .=  '<option value="' . $row->user_ID  . '">' . $row->username . '</option>';
			}	
		 }  
		$html .=  '</select>'; 	
		$html .= '</tr></td></table>
		</td></tr><tr><td colspan=7>
			<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td>';
		$html .=  'Mother: <br><input name="mother_ID" value="' . $refresh['mother_ID'] . '" type="text"></td><td>';
		$html .=  'Room: <br><select name="room"><option></option>';
		foreach ($data['all_rooms']->result() as $row){
			if ($row->room == $refresh['room']){
				$index="1";
				$html .=  '<option selected>'   . $row->room  . '</option>';
			}else{
				$html .=  '<option>' . $row->room . '</option>';
			}	
		 }  
		$html .=  '</select>'; 
		$html .=  '</td></tr></table>'; 
		$html .= '</td></tr><tr><td colspan=7>';	 
		$html .=  'Father: <br><input name="father_ID" value="' . $refresh['father_ID'] . '" type="text">'; 
		$html .= '</td></tr><tr><td colspan=3>';
		$gen_array = "";
		$gen_array[0] = "outcross/F0";
		$gen_array[1] = "F1";
		$gen_array[2] = "F2";
		$gen_array[3] = "F3";
		$gen_array[4] = "F4";
		$gen_array[5] = "F5";
		$html .= '<div style="float:left; padding-right:40px;">Generation: <br><div  style=" position:static;"><select name="generation"><option></option>';
		foreach($gen_array as $value){
			if ($value == $refresh['generation']){
				$html .= '<option selected>' . $value . '</option>';
			}else{
				$html .= '<option>' . $value . '</option>';
			}
		}
		$html .= '</select></div></div>'; 
		$html .= '<div style="float:left; padding-right:40px;">Birthday: <br><div  style=" position:static;">';
		$birthday =  $refresh['birthday'];
		$html .= output_cal_func('birthday', $birthday,'birthday');	
		$html .= '</div></div>'; 
		$html .= '<div>Date of Death: <br><div  style=" position:static;">';
		$death_date =  $refresh['death_date'];
		$html .= output_cal_func('death_date', $death_date,'death_date');	
		$html .= '</div></div>';	 
		$html .= '</td></tr><tr><td colspan="4">';
		$html .= '<table><tr><td>';
			$html .= '<table><tr><td></td><td>Qty</td></tr>';
			$html .= '<tr><td>Current Adults: </td><td><input size="5" type="text" name="current_adults" id="current_adults" value="' . $refresh['current_adults'] . '">';
			$html .= '</td></tr><tr><td>';
			$html .= 'Starting Adults: </td><td><input size="5" type="text" name="starting_adults" id="starting_adults" value="' . $refresh['starting_adults'] . '">';
			$html .= '</td></tr></table>';
		$html .= '</td><td>';
			$html .= '<table><tr><td></td><td>Qty</td></tr>';
			$html .= '<tr><td>Current Nursery: </td><td><input size="5" type="text" id="current_nursery" name="current_nursery" value="' . $refresh['current_nursery'] . '">';	
			$html .= '</td></tr><tr><td>';
			$html .= 'Starting Nursery: </td><td><input size="5" type="text" id="starting_nursery" name="starting_nursery" value="' . $refresh['starting_nursery'] . '">';	
			$html .= '</td></tr></table>';
		$html .= '</td></tr></table>'; 
		$html .= '</td></tr><tr><td colspan=8>';
		$html .= 'Comment:<br><textarea name="comments" cols="60" rows="3">' . $refresh['comments'] . '</textarea>';
		$html .= '</td></tr><tr><td colspan=8>';
		$html .= '<a href="#" style="font-size:1.4em" onclick="Shadowbox.open({player:\'iframe\', title:\'\',height:700,width:700, content:\'' .  $url . 'index.php/fish/edit_mut_trans/' . $batch_ID . '\'}); return false" class="jq_buttons" style=" font-size:12px;">Edit Genotypes</a>';
		$html .= '</td> </tr><tr><td colspan=4 >
		<div id="plain_box" style="margin:5px;"><h3>Mutants</h3>
		<table ><tr><td valign="top" width="100">
				<table style="font-size:.9em;">';
				if ($data['selected_mutants']->num_rows() > 0){ 
						foreach ($data['selected_mutants']->result() as $selected_mutant){	 
							if ($selected_mutant->mutant != ""){
								$content = '<table><tr><td align=right>Mutant:</td><td>' . $selected_mutant->mutant . '</td></tr><tr><td align=right>Allele:</td><td>' . $selected_mutant->allele. '</td></tr><tr><td align=right>Strain:</td><td>' . $selected_mutant->strain . '</td></tr></table>';
								echo '<script language="javascript">
								$(document).ready(function(){ 
								   $(\'#' . $selected_mutant->mutant_ID . '_mutant\').qtip({
									content: "' . $content . '"
								   });
								});
								</script>';
								$genotype =  output_genotype("mutant_genotype_wildtype", $selected_mutant->mutant_genotype_wildtype);
								$genotype .= output_genotype("mutant_genotype_heterzygous", $selected_mutant->mutant_genotype_heterzygous);
								$genotype .= output_genotype("mutant_genotype_homozygous", $selected_mutant->mutant_genotype_homozygous);
								$html .= '<tr><td><a href="#"  id="' . $selected_mutant->mutant_ID . '_mutant" style=" font-size:.8em">';
								$html .= $selected_mutant->mutant . ' <br> ' . $genotype . '</a><br><br>';
								$html .= '</td></tr>';
							}
						}
					}
					$html .= '</table>'; 
			$html .= '</td><td width="100">';
					$html .= '<select   id="mutant_ID"  class="multiselect" multiple="multiple" name="mutants[]">';
					if ($data['all_mutants']->num_rows() > 0){
						foreach ($data['all_mutants']->result() as $row){
							$selected = "";
							if ($data['selected_mutants']->num_rows() > 0){
								foreach ($data['selected_mutants']->result() as $selected_mutant){				 
									if ($row->mutant_ID == $selected_mutant->mutant_ID){  
										$selected = 1;  
									 } 
								}
							}
							//leave out records 
							if($selected == ""){
								$html .= '<option value="' . $row->mutant_ID . '">' . $row->mutant . '</option>';		 
							}else{ 
								
								$html .= '<option value="' .  $row->mutant_ID . '" selected="selected">' .  $row->mutant. '</option>';		
							}
						}  
					}
					$html .= '</select>';
			$html .= '</td></tr></table></div>'; //plain_box 
			$html .= '<div id="plain_box" style="margin:5px;"><h3>Transgenes</h3>';
			$html .= '<table ><tr><td valign="top" width="100">';
						$html .= '	<table style="font-size:.9em;">'; 
							if ($data['selected_transgenes']->num_rows() > 0){ 
								foreach ($data['selected_transgenes']->result() as $selected_transgene){	 
									if ($selected_transgene->transgene != ""){
										$content = '<table><tr><td align=right>Transgene:</td><td>' . $selected_transgene->transgene . '</td></tr><tr><td align=right>Allele:</td><td>' . $selected_transgene->allele. '</td></tr><tr><td align=right>Strain:</td><td>' . $selected_transgene->strain . '</td></tr></table>';
										echo '<script language="javascript">
										$(document).ready(function(){ 
										   $(\'#' . $selected_transgene->transgene_ID . '_transgene\').qtip({
											content: "' . $content . '"
										   });
										});
										</script>';
										$genotype =  output_genotype("transgene_genotype_wildtype", $selected_transgene->transgene_genotype_wildtype);
										$genotype .= output_genotype("transgene_genotype_heterzygous", $selected_transgene->transgene_genotype_heterzygous);
										$genotype .= output_genotype("transgene_genotype_homozygous", $selected_transgene->transgene_genotype_homozygous);
										$html .= '<tr><td><a href="#"  id="' . $selected_transgene->transgene_ID . '_transgene" style=" font-size:.8em">';
										$html .= $selected_transgene->transgene .  ' <br> ' . $genotype . '</a><br><br>';
										$html .= '</td></tr>';
									}
								}
							}
							$html .= '</table>';
			$html .= '</td><td width="100">';
							$html .= '<select style=" width:430px;"  id="transgene_ID"  class="multiselect" multiple="multiple" name="transgenes[]">';
						 if ($data['all_transgenes']->num_rows() > 0){
							 foreach ($data['all_transgenes']->result() as $row){
								$selected = "";
								 if ($data['selected_transgenes']->num_rows() > 0){
									foreach ($data['selected_transgenes']->result() as $selected_transgene){
										if ($row->transgene_ID == $selected_transgene->transgene_ID){ 
												$selected = 1;  
									 	} 
									}
								 }
								//leave out records 
								if($selected == ""){
									$html .= '<option value="' . $row->transgene_ID . '">' . $row->transgene . '</option>';		 
								}else{
									$html .= '<option value="' .  $row->transgene_ID . '" selected="selected">' .  $row->transgene   . '</option>';		
								}
							}  
						 }
					$html .= '</select>';
			$html .= '</td></tr></table></div>'; //plain_box	 
		$html .= '</td></tr></table>';
		$html .= '</form> </div></div>'; 
		$html .= '</div><div id="tabs-1" style=" background-color:#F5EEDE">';
		$html .= '<div id="standard_box" style="width:535px; height:535px; padding-bottom:50px;">	
		 <div id="tanks_sliding" class="SlidingPanels" tabindex="0" style=" position:absolute; width:530px; height:610px;" >                     
							<div class="SlidingPanelsContentGroup"   >                       
								<div id="ex1_p0" class="SlidingPanelsContent p2">
								<div style=" padding-left:50px; ">
								<h2>Tanks <a href="#" onclick="sp2.showPanel(\'ex1_p1\'); return false;"><img border=0 src="' . $url . 'assets/Pics/Symbol-Add_48.png"  /></a></h2></div>';
		$html .= output_current_tanks($url,$data['current_tanks'],$refresh);		
		$html .= '					</div> <!--ex1_p0-->  
								 <div id="ex1_p1" class="SlidingPanelsContent p2"> 
								<div style=" padding-left:10px;"><div style=" float:left; padding-right:20px;"><a href="#"  onclick="sp2.showPanel(\'ex1_p0\'); return false;" class="jq_buttons" style=" font-size:12px;">back</a></div><div><h2>Add Tanks</h2></div></div> ';
		$html .= output_all_tanks($url,$data['all_tanks'],$refresh);						 
		$html .= '</div> <!--ex1_p1-->  
				</div> <!--SlidingPanelsContentGroup-->                          
				</div> <!--summary_sliding--> 
				</div></div> 
				</div></div>';  
		$html .= '  <script language="javascript"> ';
		$html .= '	var sp2 = new Spry.Widget.SlidingPanels(\'tanks_sliding\');';
		$html .= '
		function set_focus_shadowbox(){
			document.getElementById(\'setfocus_var\').focus();  
		}
		setTimeout("set_focus_shadowbox()",500);  
			</script> '; 
	}
	if ($_SESSION['scanning'] == "enabled"){
		$html .= '  <script language="javascript"> ';
		$html .= '
		    document.onkeyup = KeyCheck; 
			function KeyCheck(event){ 
			   var KeyID = event.keyCode; 
			    if (KeyID == 120){
					  var scan_box = document.getElementById("batch_num");
					  scan_box.type = "text"; 
					  scan_box.value = "";  	 
					  scan_box.focus(); 
				}else if (KeyID == 119){  
				    var scan_box = document.getElementById("batch_num"); 					 
					var url_link = "' . $_SESSION['base_url'] . 'index.php/fish/modify_line/u_" + scan_box.value; 
		 			scan_box.type = "hidden";
					document.location.href =  url_link; 
				}
			}';	
		$html .= '  
		 </script> ';					
	} 
	echo $html;
}
function output_user_fields($selected_user,$url,$labs){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'user_form_ID','name' => 'user_form');
	echo form_open('fish/db_update_user/u', $attributes); ?>                            
	<?=form_hidden('user_ID',$selected_user['user_ID']); ?>	
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.user_form.submit();">Update</a>'; 
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td colspan=2><h2>User ID: ' . $selected_user['user_ID'] . '</h2>
	</td></tr><tr><td>Admin:';
	if ($selected_user['admin_access'] == "on"){
		$html .= '<input type="checkbox" name="admin_access" checked>';
	}else{
		$html .= '<input type="checkbox" name="admin_access">';
	}
	$html .= '</td></tr>';
	$html .= '<tr><td>Username:</td><td><input type="text" name="username" value="' . $selected_user['username'] . '"></td></tr>';
 	$html .= '<tr><td>First Name:</td><td><input type="text" name="first_name" value="' . $selected_user['first_name'] . '"></td></tr>';
	$html .= '<tr><td>Middle Name:</td><td><input type="text" name="middle_name" value="' . $selected_user['middle_name'] . '"></td></tr>';
	$html .= '<tr><td>Last Name:</td><td><input type="text" name="last_name" value="' . $selected_user['last_name'] . '"></td></tr>';
	$html .= '<tr><td>Email:</td><td><input type="text" name="email" value="' . $selected_user['email'] . '"></td></tr>'; 
	$html .= '<tr><td>Lab:</td><td><select name="lab">';
 	foreach ($labs->result_array() as $row){
		if ($selected_user['lab'] == $row['lab_ID']){
			$html .= '<option selected value="' . $row['lab_ID'] . '">' . $row['lab_name'] . '</option>';
		}else{
			$html .= '<option value="' . $row['lab_ID'] . '">' . $row['lab_name'] . '</option>';
		}
	}
	$html .= '</select></td></tr>';
	$html .= '<tr><td>Office:</td><td><input type="text" name="office_location" value="' . $selected_user['office_location'] . '"></td></tr>';
	$html .= '<tr><td>Lab Location:</td><td><input type="text" name="lab_location" value="' . $selected_user['lab_location'] . '"></td></tr>';
	$html .= '<tr><td>Lab Phone:</td><td><input type="text" name="lab_phone" value="' . $selected_user['lab_phone'] . '"></td></tr>';
	$html .= '<tr><td>Emergency Phone:</td><td><input type="text" name="emergency_phone" value="' . $selected_user['emergency_phone'] . '"></td></tr>';
 	$html .= '
	<tr><td colspan=2>Change Password: <input type="checkbox" name="passcheck" id="passcheck" onclick="alt_contact_toggle();">
			<div id="alt_contact2" align="left" style=" font-size:10px; visibility:hidden;position:absolute; "> 
			<table><tr><td style=" font-size:12px;">Password:</td><td><input type="text" name="user_pass" value=""></td></tr>';
	$html .= '<tr><td style=" font-size:12px;">Confirm Password:</td><td><input type="text" name="user_pass2" value=""></td></tr></table></div> 
	</td></tr>';
	$html .= '</table></form> '; 
	$html .= '</div></div>
	
	<script language="javascript">
		  function alt_contact_toggle(type_var){			 		
			   		if (document.getElementById("passcheck").checked == false){
						document.getElementById("alt_contact2").style.visibility = "hidden";
						document.getElementById("alt_contact2").style.position = "absolute";
						 
					}else  {
						document.getElementById("alt_contact2").style.visibility = "visible";
						document.getElementById("alt_contact2").style.position = "static";
						 
					}
			  }
	</script>'; 
	echo $html;
} 
function output_user_fields_new($url,$labs){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'user_form_ID','name' => 'user_form');
	echo form_open('fish/db_update_user/i', $attributes); ?>                            
	<?=form_hidden('user_ID',$selected_user['user_ID']); ?>	
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="submit_doc()">Insert</a>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Username:</td><td><input type="text" name="username" value=""></td></tr>';
	$html .= '<tr><td>Password:</td><td><input type="text" name="user_pass" value=""></td></tr>';
	$html .= '<tr><td>Confirm Password:</td><td><input type="text" name="user_pass2" value=""></td></tr>';
 	$html .= '<tr><td>First Name:</td><td><input type="text" name="first_name" value=""></td></tr>';
	$html .= '<tr><td>Middle Name:</td><td><input type="text" name="middle_name" value=""></td></tr>';
	$html .= '<tr><td>Last Name:</td><td><input type="text" name="last_name" value=""></td></tr>';
	$html .= '<tr><td>Email:</td><td><input type="text" name="email" value=""></td></tr>';
	$html .= '<tr><td>Lab:</td><td><select name="lab">';
 	foreach ($labs->result_array() as $row){
  		$html .= '<option value="' . $row['lab_ID'] . '">' . $row['lab_name'] . '</option>'; 
	}
	$html .= '</select></td></tr>';
	$html .= '<tr><td>Office:</td><td><input type="text" name="office_location" value=""></td></tr>';
	$html .= '<tr><td>Lab Location:</td><td><input type="text" name="lab_location" value=""></td></tr>';
	$html .= '<tr><td>Lab Phone:</td><td><input type="text" name="lab_phone" value=""></td></tr>';
	$html .= '<tr><td>Emergency Phone:</td><td><input type="text" name="emergency_phone" value=""></td></tr>'; 
	$html .= '<tr><td>Admin:</td><td>';
  	$html .= '<input type="checkbox" name="admin_access">';	 
	$html .= '</td></tr>';
	$html .= '</table></form> '; 
	$html .= '</div></div>
	<script language="javascript">
	function submit_doc(){
		if (document.user_form.user_pass.value != document.user_form.user_pass2.value){
			alert("Confirm password is incorrect!");
		}else{
			document.user_form.submit();
		}
	}
	</script>'; 
	echo $html;
} 
function output_mutant_fields($selected,$url){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'mutant_form_ID','name' => 'mutant_form');
	echo form_open('fish/db_update_mutant/u', $attributes); ?>                            
	<?=form_hidden('mutant_ID',$selected['mutant_ID']); ?>	
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.mutant_form.submit();">Update</a>';
	$html .= '<h2>Mutant ID: ' . $selected['mutant_ID'] . '</h2>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Mutant:</td><td><input type="text" name="mutant" value="' . $selected['mutant'] . '"></td></tr>';
	$html .= '<tr><td>Mutant Allele:</td><td><input type="text" name="allele" value="' . $selected['allele'] . '"></td></tr>'; 
	$html .= '<tr><td>Mutant Gene:</td><td><input type="text" name="gene" value="' . $selected['gene'] . '"></td></tr>';
	$html .= '<tr><td valign="top">Reference:</td><td><textarea cols="27" rows="4" name="reference" >' . $selected['reference'] . '</textarea></td></tr>';
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value="' . $selected['strain'] . '"></td></tr>';
	$html .= '<tr><td valign="top">Comment:</td><td><textarea  cols="27" rows="4" name="cross_ref" >' . $selected['cross_ref'] . '</textarea></td></tr>';
 	$html .= '</table></form> '; 
	$html .= '</div></div>'; 
	echo $html;
} 
function output_mutant_fields_new($url){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'mutant_form_ID','name' => 'mutant_form');
	echo form_open('fish/db_update_mutant/i', $attributes); ?>                            
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.mutant_form.submit();">Insert</a>';
 	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Mutant:</td><td><input type="text" name="mutant" value=""></td></tr>';
 	$html .= '<tr><td>Allele:</td><td><input type="text" name="allele" value=""></td></tr>';
	$html .= '<tr><td>Mutant Gene:</td><td><input type="text" name="gene" value=""></td></tr>'; 
	$html .= '<tr><td valign="top">Reference:</td><td><textarea cols="27" rows="4" name="reference" ></textarea></td></tr>';
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value=""></td></tr>';
	$html .= '<tr><td valign="top">Comment:</td><td><textarea  cols="27" rows="4" name="cross_ref" ></textarea></td></tr>';
 	$html .= '</table></form> '; 
	$html .= '</div></div>'; 
	echo $html;
}
function output_strain_fields($selected,$url){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'strain_form_ID','name' => 'strain_form');
	echo form_open('fish/db_update_strain/u', $attributes); ?>                            
	<?=form_hidden('strain_ID',$selected['strain_ID']); ?>	
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.strain_form.submit();">Update</a>';
	$html .= '<h2>Strain ID: ' . $selected['strain_ID'] . '</h2>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value="' . $selected['strain'] . '"></td></tr>';
	$html .= '<tr><td>Source:</td><td><input type="text" name="source" value="' . $selected['source'] . '"></td></tr>'; 
	$html .= '<tr><td>Source Contact Information:</td><td><input type="text" name="source_contact_info" value="' . $selected['source_contact_info'] . '"></td></tr>'; 	
 	$html .= '<tr><td valign="top">Comments:</td><td><textarea cols="27" rows="4" name="comments" >' . $selected['comments'] . '</textarea></td></tr>';
	$html .= '</table></form> '; 
	$html .= '</div></div>'; 
	echo $html;
} 
function output_strain_fields_new($url){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'strain_form_ID','name' => 'strain_form');
	echo form_open('fish/db_update_strain/i', $attributes); ?>                            
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.strain_form.submit();">Insert</a>';
 	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value=""></td></tr>';
	$html .= '<tr><td>Source:</td><td><input type="text" name="source" value=""></td></tr>'; 
	$html .= '<tr><td>Source Contact Information:</td><td><input type="text" name="source_contact_info" value=""></td></tr>'; 	
 	$html .= '<tr><td valign="top">Comments:</td><td><textarea cols="27" rows="4" name="comments" ></textarea></td></tr>';
	$html .= '</table></form> ';  
	$html .= '</div></div>'; 
	echo $html;
}
function output_transgene_fields($selected,$url){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'transgene_form_ID','name' => 'transgene_form');
	echo form_open('fish/db_update_transgene/u', $attributes); ?>                            
	<?=form_hidden('transgene_ID',$selected['transgene_ID']); ?>	
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.transgene_form.submit();">Update</a>';
	$html .= '<h2>Transgene ID: ' . $selected['transgene_ID'] . '</h2>';
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Transgene:</td><td><input type="text" name="transgene" value="' . $selected['transgene'] . '"></td></tr>';
	$html .= '<tr><td>Promoter:</td><td><input type="text" name="promoter" value="' . $selected['promoter'] . '"></td></tr>';
	$html .= '<tr><td>Gene:</td><td><input type="text" name="gene" value="' . $selected['gene'] . '"></td></tr>';
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value="' . $selected['strain'] . '"></td></tr>';
	$html .= '<tr><td>Allele:</td><td><input type="text" name="allele" value="' . $selected['allele'] . '"></td></tr>';
	$html .= '<tr><td valign="top">Reference:</td><td><textarea cols="27" rows="4" name="reference" >' . $selected['reference'] . '</textarea></td></tr>';
	$html .= '<tr><td valign="top">Comment:</td><td><textarea  cols="27" rows="4" name="comment" >' . $selected['comment'] . '</textarea></td></tr>';
 	$html .= '</table></form> '; 
	$html .= '</div></div>'; 
	echo $html;
} 
function output_transgene_fields_new($url){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'transgene_form_ID','name' => 'transgene_form');
	echo form_open('fish/db_update_transgene/i', $attributes); ?>                            
	<?php 	  
	$html .= '<a href="#" class="jq_buttons" onclick="document.transgene_form.submit();">Insert</a>';
 	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';
	$html .= '<tr><td>Transgene:</td><td><input type="text" name="transgene" value=""></td></tr>';
	$html .= '<tr><td>Promoter:</td><td><input type="text" name="promoter" value=""></td></tr>';
	$html .= '<tr><td>Gene:</td><td><input type="text" name="gene" value=""></td></tr>';
	$html .= '<tr><td>Strain:</td><td><input type="text" name="strain" value=""></td></tr>';
	$html .= '<tr><td>Allele:</td><td><input type="text" name="allele" value=""></td></tr>';
	$html .= '<tr><td valign="top">Reference:</td><td><textarea cols="27" rows="4" name="reference" ></textarea></td></tr>';
	$html .= '<tr><td valign="top">Comment:</td><td><textarea  cols="27" rows="4" name="comment" ></textarea></td></tr>';
 	$html .= '</table></form> ';  
	$html .= '</div></div>'; 
	echo $html;
}
function output_lab_fields($selected,$url){
	$html .=  ' 
	<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'lab_form_ID','name' => 'lab_form');
	echo form_open('fish/db_update_lab/u', $attributes); ?>                            
	<?=form_hidden('lab',$selected['lab']); ?>	
	<?php  
	$html .=  '<h2>Update Lab</h2>
	<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Lab:</td><td><input type="hidden" name="lab_ID" value="' . $selected['lab_ID'] . '"><input type="text" name="lab" id="lab_name" value="' . $selected['lab_name'] . '"></td></tr>';
	$html .= '<tr><td colspan=2 align="right"><br><br><a href="#" class="jq_buttons" onclick="check_char();">Update</a></td></tr>';
	$html .= '</table></form> '; 
	$html .= '';
	$html .= '</div></div>
		<script language="javascript">
	function  check_char(){ 
		var string1 = document.getElementById("lab_name").value; 
		if(string1.search(\'_\') != -1){	 
			alert("You can not use an underscore in the lab name. (_)");	
		}else{
			document.lab_form.submit();
		}
	}
	</script>'; 
	echo $html;
} 
function output_lab_fields_new($url){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'lab_form_ID','name' => 'lab_form');
	echo form_open('fish/db_update_lab/i', $attributes); ?>                            
	<?php 	   
 	$html .=  '<h2>Insert Lab</h2>
	<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Lab:</td><td><input type="text" name="lab" value=""></td></tr>';
	$html .= '<tr><td colspan=2 align="right"><br><br><a href="#" class="jq_buttons" onclick="document.lab_form.submit();">Insert</a></td></tr>';
 	$html .= '</table></form> ';  
	$html .= '</div></div>'; 
	echo $html;
}
function output_tank_fields($selected,$url){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'tank_form_ID','name' => 'tank_form');
	echo form_open('fish/db_update_tank/u', $attributes); ?>                            
	<?=form_hidden('tank_ID',$selected['tank_ID']); ?>	
	<?php 
	$html .= '<a href="#" class="jq_buttons" onclick="document.tank_form.submit();">Update</a>
	<h2>Tank ID: ' . $selected['tank_ID'] . '</h2>'; 
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Size:</td><td><input type="text" name="size" value="' . $selected['size'] . '"></td></tr>'; 
	$html .= '<tr><td>Location:</td><td><input type="text" name="location" value="' . $selected['location'] . '"></td></tr>';
	$html .= '<tr><td>Room:</td><td><input type="text" name="room" value="' . $selected['room'] . '"></td></tr>';
	$html .= '<tr><td valign="top">Comments:</td><td><textarea type="text" cols="25" rows="6" name="comments">' . $selected['comments'] . '</textarea></td></tr>'; 
  	$html .= '</table></form> ';  
	$html .= '</div></div>'; 
	echo $html;
} 
function output_tank_fields_new($url){ 
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'tank_form_ID','name' => 'tank_form');
	echo form_open('fish/db_update_tank/i', $attributes); ?>                            
	<?php 	   
 	$html .=  '<a href="#" class="jq_buttons" onclick="document.tank_form.submit();">Insert</a>
	<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;">';	
	$html .= '<tr><td>Size:</td><td><input type="text" name="size" ></td></tr>'; 
	$html .= '<tr><td>Location:</td><td><input type="text" name="location"></td></tr>';
	$html .= '<tr><td>Room:</td><td><input type="text" name="room" value=""></td></tr>';
	$html .= '<tr><td valign="top">Comments:</td><td><textarea type="text" cols="25" rows="6" name="comments"></textarea></td></tr>'; 
 	$html .= '</table></form> ';  
	$html .= '</div></div>'; 
	echo $html;
}
function output_users($url,$users){ 
			 	$tableID = "all_users";
				$html .= table_format($tableID,'0', '','','','','','','','');  
			 	$html .= '<h2>Users <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:550,width:500, content:\'' . $url . 'index.php/fish/modify_users/n\'}); return false" /></h2>';
				$html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';  
				$html .= '<tr > <th ></th> 
				  <th  >User&nbsp;ID</th><th  >Full Name</th>				  
				  <th  >User</th><th  >Lab</th>
				 <th  >Office</th> <th  >Lab&nbsp;Phone</th>
				 <th  >Emergency&nbsp;Phone</th> <th  >Email</th>
				   <th  >Admin</th>';			  		 
				   $html .= '</tr></thead><tbody>';	
				   foreach ($users->result_array() as $row):
				   	   $html .= '<tr>';
					   $html .= '<td><div style=" width:40px">';
					   if ($row['username'] != $_SESSION['username']){
					  	 $html .= ' <input type="image" width="12" src="' . $url . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'index.php/fish/modify_users/r_' . $row['user_ID'] . '\'}); return false" />';  
					   }
					   $html .=  ' <input type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:550,width:500, content:\'' . $url . 'index.php/fish/modify_users/u_' . $row['user_ID'] .'\'}); return false" /> </div></td>';
  					   $html .= '<td>' .$row['user_ID']. '</td>';
					   $html .= '<td>' . $row['last_name'] . ', ' . $row['first_name'] . '</td>'; 
					    $html .= '<td>' . $row['username'] . '</td>';
						$html .= '<td>' . $row['lab_name'] . '</td>';	
						$html .= '<td>' . $row['office_location'] . '</td>';	
						$html .= '<td>' . $row['lab_phone'] . '</td>'; 
						$html .= '<td>' . $row['emergency_phone'] . '</td>'; 
						$html .= '<td>' . $row['email'] . '</td>';  
						if ($row['admin_access'] == "on"){							
							$html .= '<td>Admin</td></tr>';
						}else{
							$html .= '<td></td></tr>';
						}  
                  	endforeach; 
		   	 		$html .= '</tbody> </table>';
 		echo $html;
}
function output_labs($url,$users){
			 	$tableID = "all_labs";
				$html .= table_format($tableID,'0', '','','','','','','','');  
			 	$html .= ' <h2>Labs <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_lab/n\'}); return false" /></h2>';
		 	 	$html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';   
				$html .= '<tr > <th ><div class="sort"></div></th> 
				  <th  ><div class="sort">Lab</div></th>';			  		 
				   $html .= '</tr></thead><tbody>';	
				   foreach ($users->result_array() as $row):
				   	   $html .= '<tr>';
					   $html .= '<td><div style=" width:40px"><input type="image" width="12" src="' . $url . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'index.php/fish/modify_lab/r_' . $row['lab_ID'] . '\'}); return false" />  
                  	  	 <input type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_lab/u_' . $row['lab_ID'] .'\'}); return false" /> </div></td>';
  					   $html .= '<td>' .$row['lab_name']. '</td></tr>';  
                  	endforeach; 
		   	 		$html .= '</tbody> </table>';
 		echo $html;
}
function output_tanks($url,$tanks){
			 	$tableID = "all_tanks";
				$tank['datatables_select'] = '(\'empty\'),tank_ID,size,location,room,comments'; 
				$tank['datatables_field_wtables'] =  '(\'empty\'),tank_ID,size,location,room,comments';
				$tank['datatables_from'] =  'tank'; 
				$tank['datatables_fields'] = '(\'empty\'),tank_ID,size,location,room,comments';
				$tank['datatables_buttons'] = $url;
		  		$html .= table_format($tableID,'0','',$url . 'assets/server_processing.php',addslashes($tank['datatables_fields']),addslashes($tank['datatables_select']),addslashes($tank['datatables_buttons']),addslashes($tank['datatables_from']),addslashes($tank['datatables_field_wtables']),addslashes($tank['datatables_where']),'tank_ID'); 
			 	$html .= '<h2>Tanks <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_tank/n\'}); return false" /></h2>';
		 	 	$html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';    
				$html .= '<tr > <th ></th> 
				  <th  >Tank ID</th><th  >Size</th>
				  <th  >Location</th>
				  <th  >Room</th>
				  <th  >Comments</th></tr>';
				 $html .= '</thead><tbody> 
				</tbody></table> ';  	  		  
 		echo $html;
}
function output_mutants($url,$all_records){
			 	$tableID = "all_mutants";
				$number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','','');  
			 	$html .= '<h2>Mutants <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_mutant/n\'}); return false" /></h2>';
		 		 if ($number_count > 0){
						  $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';
						   $html .= '<tr > <th ></th> 
						  <th  >Mutant&nbsp;ID</th><th  >Mutant Name</th>
						  <th>Mutant Allele</th><th>Mutant Gene</th>   
						  <th  >Strain</th> ';			  		 
						   $html .= '</tr></thead><tbody>';	 
						   foreach ($all_records->result_array() as $row):
							   $html .= '<tr>';
							   $html .= '<td><div style=" width:40px"><input type="image" width="12" src="' . $url . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'index.php/fish/modify_mutant/r_' . $row['mutant_ID'] . '\'}); return false" />  
								 <input type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_mutant/u_' . $row['mutant_ID'] .'\'}); return false" /> </div></td>';
							   $html .= '<td>' .$row['mutant_ID']. '</td>';
							   $html .= '<td>' . $row['mutant'] . '</td>'; 
							   $html .= '<td>' . $row['allele'] . '</td>'; 
							   $html .= '<td>' . $row['gene'] . '</td>'; 
							   $html .= '<td>' . $row['strain'] . '</td></tr>';	  
							endforeach;
							$html .= '</tbody> </table>';
				}else{
					$html .= 'No mutants!';	
				} 
 		echo $html;
}
function output_strains($url,$all_records){
			 	$tableID = "all_strains";
				$number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','','');
			 	$html .= ' <h2>Strains <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_strain/n\'}); return false" /></h2>';
		  		if ($number_count > 0){
						   $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';
						   $html .= '<tr > <th ><div class="sort"></div></th> 
						  <th  ><div class="sort">Strain&nbsp;ID</div></th><th  ><div class="sort">Strain</div></th>
						  <th  ><div class="sort">Source</div></th>  ';			  		 
						   $html .= '</tr></thead><tbody>';	
						   if ($number_count > 0){
							   foreach ($all_records->result_array() as $row):
								   $html .= '<tr>';
								   $html .= '<td><div style=" width:40px"><input type="image" width="12" src="' . $url . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'index.php/fish/modify_strain/r_' . $row['strain_ID'] . '\'}); return false" />  
									 <input type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_strain/u_' . $row['strain_ID'] .'\'}); return false" /> </div></td>';
								   $html .= '<td>' .$row['strain_ID']. '</td>';
								   $html .= '<td>' . $row['strain'] . '</td>';
								   $html .= '<td>' . $row['source'] . '</td>'; 
								   $html .= '</tr>';	  
								endforeach; 
						   }
		   	 				$html .= '</tbody> </table>';
				}else{
					$html .= 'No strains!';	
				} 
 		echo $html;
}
function output_transgenes($url,$all_records){
			 	$tableID = "all_transgene";
				$number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','','');
			 	$html .= '<h2>Transgenes <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_transgene/n\'}); return false" /></h2>';
		 	 	if ($number_count > 0){
					$html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';
					$html .= '<tr > <th ></th> 
					<th  >Transgene&nbsp;ID</th>
					<th  >Transgene Name</th><th  >Promoter</th>
					<th  >Transgene Gene</th><th  >Strain</th><th  >Transgene Allele</th>  ';			  		 
					$html .= '</tr></thead><tbody>';	 
					foreach ($all_records->result_array() as $row):
					   $html .= '<tr>';
					   $html .= '<td><div style=" width:40px"><input type="image" width="12" src="' . $url . 'assets/Pics/Red_x.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'index.php/fish/modify_transgene/r_' . $row['transgene_ID'] . '\'}); return false" />  
						 <input type="image" width="16" src="' . $url . 'assets/Pics/Edit-32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\', title:\'Update\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_transgene/u_' . $row['transgene_ID'] .'\'}); return false" /> </div></td>';
					   $html .= '<td>' .$row['transgene_ID']. '</td>';
					   $html .= '<td>' . $row['transgene'] . '</td>';
					   $html .= '<td>' . $row['promoter'] . '</td>';
					   $html .= '<td>' . $row['gene'] . '</td>'; 
					   $html .= '<td>' . $row['strain'] . '</td>';
					    $html .= '<td>' . $row['allele'] . '</td>';
					   $html .= '</tr>';	  
					endforeach; 				   
					$html .= '</tbody> </table>';
				}else{
					$html .= 'No transgenes!';	
				} 
 		echo $html;
}
function output_mutants_user($url,$all_records){
			 	$tableID = "all_mutants_users";
				 $number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','','');
			 	$html .= '<h2>Mutants <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_mutant/n\'}); return false" /></h2>';
		    	if ($number_count > 0){
						  $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
							<thead>';
						  $html .= '<tr > 
						  <th  ><div class="sort">Mutant&nbsp;ID</div></th><th  ><div class="sort">Mutant</div></th>
						  <th  ><div class="sort">Allele</div></th>  
						  <th  ><div class="sort">Strain</div></th> ';			  		 
						   $html .= '</tr></thead><tbody>';	 
						   foreach ($all_records->result_array() as $row):
							   $html .= '<tr>';
							    $html .= '<td>' .$row['mutant_ID']. '</td>';
							   $html .= '<td>' . $row['mutant'] . '</td>'; 
							   $html .= '<td>' . $row['allele'] . '</td>'; 
							   $html .= '<td>' . $row['strain'] . '</td></tr>';	  
							endforeach;
							$html .= '</tbody> </table>';
				}else{
					$html .= 'No mutants!';	
				} 
 		echo $html;
}
function output_strains_user($url,$all_records){
			 	$tableID = "all_strains_users";
				$number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','',''); 
			 	$html .= '<h2>Strain <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_strain/n\'}); return false" /></h2>';
		 	    if ($number_count > 0){
						 $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
							<thead>';
						   $html .= '<tr >  
						  <th  ><div class="sort">Strain&nbsp;ID</div></th><th  ><div class="sort">Strain</div></th>
						  <th  ><div class="sort">Source</div></th>  ';			  		 
						   $html .= '</tr></thead><tbody>';	
						   if ($number_count > 0){
							   foreach ($all_records->result_array() as $row):
								   $html .= '<tr>';
								   $html .= '<td>' .$row['strain_ID']. '</td>';
								   $html .= '<td>' . $row['strain'] . '</td>';
								   $html .= '<td>' . $row['source'] . '</td>'; 
								   $html .= '</tr>';	  
								endforeach; 
						   }
		   	 				$html .= '</tbody> </table>';
				}else{
					$html .= 'No strains!';	
				} 
 		echo $html;
}
function output_transgenes_user($url,$all_records){
			 	$tableID = "all_transgene_users";
				$number_count = $all_records->num_rows();
				$html .= table_format($tableID,'0', '','','','','','','','');  
			 	$html .= '<h2>Transgene <input type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png" name="doit3" value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:500,width:500, content:\'' . $url . 'index.php/fish/modify_transgene/n\'}); return false" /></h2>';
		 	    if ($number_count > 0){
					  $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
							<thead>';
					   $html .= '<tr >   
					  <th  ><div class="sort">Transgene&nbsp;ID</div></th><th  ><div class="sort">Promoter</div></th>
					  <th  ><div class="sort">Gene</div></th><th  ><div class="sort">Strain</div></th>  ';			  		 
					   $html .= '</tr></thead><tbody>';	 
						   foreach ($all_records->result_array() as $row):
							   $html .= '<tr>';
							   $html .= '<td>' .$row['transgene_ID']. '</td>';
							   $html .= '<td>' . $row['promoter'] . '</td>';
							   $html .= '<td>' . $row['gene'] . '</td>'; 
							   $html .= '<td>' . $row['strain'] . '</td>';
							   $html .= '</tr>';	  
							endforeach; 				   
			   	 		$html .= '</tbody> </table>';
				}else{
					$html .= 'No transgenes!';	
				} 
 		echo $html;
}
function output_search_results($data,$url,$admin_access,$search){
	switch ($report_array[1]) {
		case "m":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;
		case "ml":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "l":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "u":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;			
	}
	echo '<script language="javascript"> 
			Shadowbox.init({ 
				players:    ["iframe"]	 
			});
			</script>';
				$_SESSION['report_data']=""; 
				$tableID = "results_table"; 
				 if ($admin_access != "on"){
					$search['datatables_buttons'] .= "_user_access";
				 }
				$html .= table_format($tableID,'','',$url . 'assets/server_processing.php',addslashes($search['datatables_fields']),addslashes($search['datatables_select']),addslashes($search['datatables_buttons']),addslashes($search['datatables_from']),addslashes($search['datatables_field_wtables']),addslashes($search['datatables_where']),'fish.batch_ID');  
			 	echo '<table><tr>
				<td> 
			   <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/search_results\'"/>
			  <a href="' . $url . 'index.php/fish/print_prev_search" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
		 	 </td>  </tr></table>
			 <h2>Search Results</h2><div style="width:1000px; padding-left:30px;">'; 
				  $html .=  '<table style="font-size:.7em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
							<thead>'; 
				   $html .= '<tr ><th ></th><th>Batch&nbsp;#</th>
							   <th>Name</th>
							  <th>Status</th><th  >Birthday</th>
							  <th  >User</th><th  >Lab</th>
							  <th>Strain</th> 
							  <th  >Generation</th>
							  <th  >Cur Adults</th><th  >Start Nursery</th></tr>';	
				  $html .= '</thead><tbody> 
						</tbody></table></div> ';	 
					echo $html;	 
}
function output_report_recipients($url,$all_users,$all_report_recipients){
	 echo "<script type=\"text/javascript\">
			     $().ready(function() {  
      $('#add').click(function() {  
       return !$('#select1 option:selected').remove().appendTo('#select2');  
      });  
      $('#remove').click(function() {  
      return !$('#select2 option:selected').remove().appendTo('#select1');  
      });  
    }); 
	function selectAllOptions(selStr){
	  var selObj = document.getElementById(selStr);
		  for (var i=0; i<selObj.options.length; i++) {
			selObj.options[i].selected = true;
		  }		   
	} 
	function submit_recipients(){
		selectAllOptions('select2');
		document.recipients_form.submit();	
	}
			</script>
			<table><tr><td style=\"padding-right:20px\"><div id=\"standard_box\" style=\"height:300px; padding-left:20px;\">
			<h2>Email Recipients</h2>"; 
	echo '<form method="post" action="' . $url . 'index.php/fish/db_update_recipients"   name="recipients_form">  <input name="report" type="hidden" value="1">
	<br><a href="#" onclick="submit_recipients();" class="jq_buttons">Update</a><br><br>
	<div  style="float:left"> 
	<strong>Users</strong> <br>
			<select multiple="multiple" id="select1" name="select1[]">  ';
			foreach ($all_users->result_array() as $row){ 
				$already_recip = "0";
				foreach ($all_report_recipients->result_array() as $cur_recip){
					if ($cur_recip['user_ID'] == $row['user_ID'] && $cur_recip['report_ID'] == "1"){ 
						$already_recip = "1";
						break;
					}
				}
				if ($already_recip == "0"){
					echo '<option value="' . $row['user_ID'] . '">' . $row['last_name'] . ',' . $row['first_name'] . '</option>';
				}
			}
	echo '</select>  		  
	</div> 
	<div style="float:left;"><br><br>
		   <table> <tr><td>
			Add 
			</td></tr><tr><td> 
			<a href="javascript:;" id="add"><img width="25px" src="' . $url . 'assets/Pics/next_button.png" border="0"></a>
			</td></tr><tr><td>
			Remove
			</td></tr><tr><td> 	
			<a href="javascript:;" id="remove"><img width="25px" src="' . $url . 'assets/Pics/previous_button.png" border="0"></a>
			</td></tr></table>
		</div>
   <div style=" float:left"> <strong>Current Recipients</strong> <br>
	  <select multiple="multiple" id="select2" name="users[]">'; 
			foreach ($all_users->result_array() as $row){ 
				foreach ($all_report_recipients->result_array() as $cur_recip){
					if ($cur_recip['user_ID'] == $row['user_ID'] && $cur_recip['report_ID'] == "1"){
						echo '<option value="' . $row['user_ID'] . '">' . $row['last_name'] . ',' . $row['first_name'] . '</option>';
						break;
					}
				}
			}  
	echo '</select>  
	</div>  ';	
	echo '</form></div>
	</td><td valign="top"><div id="standard_box">'; 
			   $tableID = "scheduled_table";
			   $html  = table_format($tableID,'0','','','','','','','','');		 			 	 
			   $html .= '<h2>Scheduled Reports</h2>';
			   $html .=  '<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
									<thead>';	
			   $html .= '<tr ><th style=" width:5%"><div class="sort">File</div></th>
			   <th style=" width:5%"><div class="sort"></div></th>';			  		 
			   $html .= '</tr></thead><tbody>';
			   $dir = "assets/scheduled_reports/";				    
			   $html .= directory_tree($dir,$url);  
			   $html .= '</tbody> </table>				
				</div></td></tr></table>';
				echo $html; 
}

function output_fields_remove($refresh, $batch_ID){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update/r', $attributes); ?>                            
	<?=form_hidden('batch_ID',$batch_ID); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this batch?</h2>';	 
	$html .= '<input type="hidden" name="batch_ID" value="' . $refresh['batch_ID'] . '">
	<h4>Batch Number: ' . $refresh['batch_ID'] . '</h4>';
	$html .= '<h4>Name: '. $refresh['name'] . '</h4>';
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function output_fields_wq_remove($refresh, $entry_ID){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_wq/r', $attributes); ?>                            
	<?=form_hidden('entry_ID',$entry_ID); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this entry?</h2>';	 
	$html .= ' <h4>Record ID: ' . $refresh['entry_ID'] . '</h4>';
	$html .= '<h4>System Name: '. $refresh['system_name'] . '</h4>';
	$html .= '<h4>Record Date: '. date("m/d/Y",$refresh['record_date']) . '</h4>';
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function output_water_quality($url,$search_options){ 
				$tableID ="wq";
				$html .= table_format($tableID,'2','',$url . 'assets/server_processing.php',addslashes($search_options['datatables_fields']),addslashes($search_options['datatables_select']),addslashes($search_options['datatables_buttons']),addslashes($search_options['datatables_from']),addslashes($search_options['datatables_field_wtables']),addslashes($search_options['datatables_where']),'record_date');  
			 	$html .= '<table><tr><td> 
			   <input alt="add" title="add record" type="image"  src="' . $url . 'assets/Pics/Symbol-Add_48.png"  name="doit"  value="Open ShadowBox" onClick="Shadowbox.open({player:\'iframe\', title:\'Insert\',height:400,width:500, content:\'' . $url . 'index.php/fish/modify_line_wq/n_\'}); return false" />
		    	  </td><td >
			  <input alt="excel export" title="excel export" type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/water_quality\'"/>
			  <a  alt="print view" title="print view" href="' . $url . 'index.php/fish/print_prev_wq" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
			</td><td style=" padding-left:80px"> 
			  </td></tr></table>';
			  $html .=  '<div style="width:800px;float:left">	<table class="display" cellpadding="0" cellspacing="0" border="0" style="font-size:.8em" class="display" id="' . $tableID . '">
									<thead>';
				$html .= '<tr ><th></th>
				   <th>System Name</th>
				   <th>Location</th>
				   <th>Nitrate</th>
				   <th>Nitrite</th>
				   <th>pH</th> 
				   <th>Conductivity</th> 
				   <th>D.O.</th>
				   <th>Temperature</th>
				   <th>Record Date</th>			   
				</tr></thead><tbody><tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>	
				</tbody></table></div>'; 
				echo $html;  
}
function output_chart_ranges($url,$name,$id){ 
	$html = '<div id="standard_box" style="width:400px; margin-left:40px;">
	<form method="post" name="crange_form_' . $name . '">';
	$html .= '<h3>Show By Date Range</h3>
	<table><tr><td>Start<br>';
	$html .= output_cal_func('start_d', "",$name . 'start_d');
	$html .= '</td><td>End<br>';
	$html .= output_cal_func('end_d', "",$name . 'end_d');
	$html .= '</td><td><a class="jq_buttons" href="#" onClick="Shadowbox.open({player:\'iframe\', title:\'\',height:800,width:900, content:\'' . $url . 'index.php/fish/submit_charts_data/' . $name . '\'}); return false">Go</a>';	
	$html .= '</td></tr></table>';
	$html .= '</form></div>';	
	echo $html; 	
}
function output_nitrate_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->nitrate;
		$date_t[] = "'" . date("d",$row->record_date) . "'";
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrate_chart;
	$(document).ready(function() {
	   nitrate_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		   chart: {
        	 renderTo: 'nitrate_chart'
     	 },
		  title: {
			 text: 'Nitrate Levels " . $display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'Nitrate'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y + '(ppm)';
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'Nitrate',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="nitrate_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function output_nitrite_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->nitrite;
		$date_t[] = "'" . date("d",$row->record_date) . "'";
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrite_chart;
	$(document).ready(function() {
	   nitrite_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		 chart: {
        	 renderTo: 'nitrite_chart'
     	 },
		  title: {
			 text: 'Nitrite Levels " . $display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'Nitrate'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y + '(ppm)';
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'Nitrite',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="nitrite_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function output_ph_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->ph;
		$date_t[] = "'" . date("d",$row->record_date) . "'";
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrite_chart;
	$(document).ready(function() {
	   ph_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		 chart: {
        	 renderTo: 'ph_chart'
     	 },
		  title: {
			 text: 'pH Levels " . $display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'pH'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y;
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'pH',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="ph_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function output_conductivity_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->conductivity;
		$date_t[] = "'" . date("d",$row->record_date) . "'";
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrite_chart;
	$(document).ready(function() {
	   conductivity_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		 chart: {
        	 renderTo: 'conductivity_chart'
     	 },
		  title: {
			 text: 'Conductivity Levels " . $display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'Conductivity'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y + 'µS';
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'Conductivity',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="conductivity_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function output_do_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->do;
		$date_t[] = "'" . date("d",$row->record_date) . "'";
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrite_chart;
	$(document).ready(function() {
	   do_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		 chart: {
        	 renderTo: 'do_chart'
     	 },
		  title: {
			 text: 'D.O. Levels " . $display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'D.O.'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y + 'mg/L';
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'D.O.',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="do_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function output_temperature_chart($water_quality){ 
	foreach ($water_quality->result() as $row){ 
		$data[] = $row->temperature;
		$date_t[] = "'" . date("d",$row->record_date) . "'"; 
		$display_date = date("M Y",$row->record_date);
	}
	$plots = implode(",",$data);
	$dates = implode(",",$date_t);
	$html = "<script language='javascript'>
	var nitrite_chart;
	$(document).ready(function() {
	   temperature_chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'line',
			 marginRight: 130,
			 marginBottom: 25
		  },
		 chart: {
        	 renderTo: 'temperature_chart'
     	 },
		  title: {
			 text: 'Temperature Levels " .$display_date . "',
			 x: -20 //center
		  },
		  subtitle: {
			 text: '',
			 x: -20
		  },
		  xAxis: {
			 categories: [" . $dates . "]
		  },
		  yAxis: {
			 title: {
				text: 'Temperature'
			 },
			 plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			 }]
		  },
		  tooltip: {
			 formatter: function() {
					   return '<b>'+ this.series.name +'</b><br/>'+
				   this.x +': '+ this.y + ' C';
			 }
		  },
		  legend: {
			 layout: 'vertical',
			 align: 'right',
			 verticalAlign: 'top',
			 x: -10,
			 y: 100,
			 borderWidth: 0
		  },
		  series: [{
			 name: 'Temperature',
			 data: [" . $plots . "]
		  }]
	   }); 
	});
	</script>";	
	$html .= '<div style="float:left"><div id="temperature_chart" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 780px">
	</div></div>';
	echo $html;
}
function user_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_user/r', $attributes); ?>                            
	<?=form_hidden('user_ID',$refresh['user_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this user?</h2>';	 
	$html .= ' 
	<h4>User ID: ' . $refresh['user_ID'] . '</h4>';
	$html .= '<h4>Username: '. $refresh['name'] . '</h4>';
	$html .= '<h4>Name: '. $refresh['full_name'] . '</h4>';
	$html .= '<h4>Lab: '. $refresh['lab_name'] . '</h4>';
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function mutant_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_mutant/r', $attributes); ?>                            
	<?=form_hidden('mutant_ID',$refresh['mutant_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this mutant?</h2>';	 
	$html .= ' 
	<h4>Mutant ID: ' . $refresh['mutant_ID'] . '</h4>';
	$html .= '<h4>Mutant: '. $refresh['mutant'] . '</h4>';
	$html .= '<h4>Strain: '. $refresh['strain'] . '</h4>'; 
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();" >No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function strain_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_strain/r', $attributes); ?>                            
	<?=form_hidden('strain_ID',$refresh['strain_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this strain?</h2>';	 
	$html .= ' 
	<h4>Strain ID: ' . $refresh['strain_ID'] . '</h4>';
	$html .= '<h4>strain: '. $refresh['strain'] . '</h4>';
 	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#"  onclick="self.parent.Shadowbox.close();" >No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function transgene_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_transgene/r', $attributes); ?>                            
	<?=form_hidden('transgene_ID',$refresh['transgene_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this transgene?</h2>';	 
	$html .= ' 
	<h4>Transgene ID: ' . $refresh['transgene_ID'] . '</h4>';
	$html .= '<h4>Promoter: '. $refresh['promoter'] . '</h4>';

	$html .= '<h4>Strain: '. $refresh['strain'] . '</h4>';
 	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function tank_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_tank/r', $attributes); ?>                            
	<?=form_hidden('tank_ID',$refresh['tank_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this tank?</h2>';	 
	$html .= '<h4>Tank: '. $refresh['tank_ID'] . '</h4>'; 
	$html .= '<h4>Location: '. $refresh['location'] . '</h4>'; 
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
}
function search_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_search/r', $attributes); ?>                            
	<?=form_hidden('search_ID',$refresh['search_ID']); ?>	
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this search?</h2>';
	$html .= '<h4>Search ID: '. $refresh['search_ID'] . '</h4>';	 
	$html .= '<h4>Name: '. $refresh['search_name'] . '</h4>'; 
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div></div>';
	echo $html;
} 
function lab_fields_remove($refresh){
	$html .=  '<div style="width:400px; padding-left:40px; padding-top:40px;"><div id="standard_box">';
	$attributes = array('id' => 'record_form_ID','name' => 'record_form');
	echo form_open('fish/db_update_lab/r', $attributes); ?>   
	<?php 	  
	$html .= '<h2>Are you sure you want to remove this lab?</h2>
	<input type="hidden" name="lab_ID" value="' . $refresh['lab_ID'] . '">';	 
	$html .= '<h4>Lab: '. $refresh['lab_name'] . '</h4>'; 
	$html .= '<a class="jq_buttons" href="#" onclick="document.record_form.submit();">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jq_buttons" href="#" onclick="self.parent.Shadowbox.close();">No</a>'; 
	$html .= '</form>
	</div>';
	echo $html;
}
function output_current_tanks($url,$tanks,$refresh){
	$number_count = count($tanks);
	 if ($number_count > 0){
	$_SESSION['preview_array']="";
				$tableID = "fish_tanks";
				$html .= table_format($tableID,'0','','','','','','','','');  
				$attributes = array('id' => 'tank_form_remove_ID','name' => 'tank_form_remove');
				$html .= form_open('fish/remove_tanks', $attributes);
				$html .= form_hidden('batch_ID', $refresh['batch_ID']); 
				$html .= '<select id="cur_rem_tanks" multiple name="tanks[]" style=" visibility:hidden; position:absolute;"></select></form>';
		 	   $html .=' <div style=" overflow:auto; height:460px; width:490px;"><table><tr><td>';			 
			   $html .=  '<div style="  width:300px; font-size:.7em;">	<table style=" font-size:.8em " class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>'; 
						   $html .= '<tr > <th  width="3"></th> 
						  <th>Location</th><th>Room</th>
						  <th  >Multiple Batches</th>';			  		 
						   $html .= '</tr></thead><tbody>';	 
								 foreach ($tanks  as $row){ 								 	  
									  $html .= '<tr><td><a href="#" onclick="displayremoveVals(\'' . $row['tank_ID'] . '\',\'' . $row['location'] . '\',\'add_tank\');"><img width="12" src="' . $url . 'assets/Pics/Red_x.png" border=0></a></td>'; 				   
									  $html .= '<td>' . $row['location'] . '</td>';
									  $html .= '<td>' . $row['room'] . '</td>';
									  if(is_array($row['multiple_batch']) && count($row['multiple_batch']) > 1){
										  $html .= "<td>";
										  foreach ($row['multiple_batch']  as $value){   
											$html .= '<a href="'. $url .'index.php/fish/modify_line/u_' . $value . '">' . $value . '</a>,';
										  }
										 $html .= "</td>";
									   }else{
											$html .= "<td></td>";
									   }
									   $html .= "</tr>";	
								 }						 
							$html .= '</tbody> </table> </div>  
						</td><td valign="top">  				
							<a href="#" onclick="submit_remove_tanks();" class="jq_buttons" style=" font-size:.8em;">Remove</a>
							<div id="remove_tanks"></div>
					</td></tr></table>
					 </div>
					<script>
	 function submit_remove_tanks() { 
		 selectAllOptions("cur_rem_tanks");			 
		 document.tank_form_remove.submit();
	 }
	 
    function displayremoveVals(temp_var,location,change_var) { 
     	 if (change_var == "remove_tank"){
			var d1=document.getElementById("cur_rem_tanks");
			var d2=document.getElementById("opt_r_" + temp_var);			 
			d1.removeChild(d2); 
			var d1=document.getElementById("remove_tanks");
			var d2=document.getElementById("div_r_" + temp_var);			 
			d1.removeChild(d2); 
			return ;
		}
	 	if (change_var == "add_tank"){
			var innervar = document.getElementById("remove_tanks").innerHTML;
			document.getElementById("remove_tanks").innerHTML = innervar + "<div id=\"div_r_" + temp_var + "\">" + location + 
			"<a href=\"#\" onclick=\"displayremoveVals(\\\'" + temp_var + "\\\',\\\'\\\',\\\'remove_tank\\\');\"><img src=\"' . $url . 'assets/Pics/Red_x.png\" width=\"16\" border=0></a></div>";	
	 		var elSel=document.getElementById("cur_rem_tanks");
			var elOptNew = document.createElement("option");
			elOptNew.text = temp_var;
			elOptNew.id = "opt_r_" + temp_var;  
			try {
				elSel.add(elOptNew, null); // standards compliant; does not work in IE
			  }
			  catch(ex) {
				elSel.add(elOptNew); // IE only
			  } 
		} 
    }  
</script>';
					
					$_SESSION['preview_array']= $html;
	 
	 }
    return $html;
}
function output_all_tanks($url,$tanks,$refresh){ 
				$tableID = "fish_all_tanks"; 
				$tank['datatables_select'] = 'tank_ID,location,room'; 
				$tank['datatables_field_wtables'] =   'tank_ID,location,room';
				$tank['datatables_from'] =  'tank'; 
				$tank['datatables_fields'] =  'tank_ID,location,room';
				$tank['datatables_buttons'] = $url;
				$tank['datatables_where'] = "";  
				$html .= table_format($tableID,'0','',$url . 'assets/server_processing.php',addslashes($tank['datatables_fields']),addslashes($tank['datatables_select']),addslashes($tank['datatables_buttons']),addslashes($tank['datatables_from']),addslashes($tank['datatables_field_wtables']),addslashes($tank['datatables_where']),'location');
			  	$attributes = array('id' => 'tank_form_ID','name' => 'tank_form');
				$html .= form_open('fish/add_tanks', $attributes);
				$html .= form_hidden('batch_ID', $refresh['batch_ID']); 
				$html .= '<select id="cur_tanks" multiple name="tanks[]" style=" visibility:hidden; position:absolute;"></select></form>';
			    $html .= '  <div style=" overflow:auto; height:460px; width:530px; "> <table  ><tr><td><div style="  width:400px; font-size:.7em;">
				  <table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>  '; 
				$html .= '<tr ><th></th>
						  <th  >Location</th>	
						   <th  >Room</th>		   
				</tr></thead><tbody> 
				</tbody></table> ';  
				$html .= '</div>
					</td><td valign="top"> 
					<a href="#"  onclick="submit_add_tanks();" class="jq_buttons" style=" font-size:.8em;">Insert</a>
					<div id="selected_vars"></div>
					</td></tr></table> </div> 
					<script>
	 function submit_add_tanks() { 
		 selectAllOptions("cur_tanks");			 
		 document.tank_form.submit();
	 }
	 
    function displayVals(temp_var,location,change_var) { 
     	 if (change_var == "remove_tank"){
			var d1=document.getElementById("cur_tanks");
			var d2=document.getElementById("opt_" + temp_var);			 
			d1.removeChild(d2); 
			var d1=document.getElementById("selected_vars");
			var d2=document.getElementById("div_" + temp_var);			 
			d1.removeChild(d2); 
			return ;
		}
	 	if (change_var == "add_tank"){
			var innervar = document.getElementById("selected_vars").innerHTML;
			document.getElementById("selected_vars").innerHTML = innervar + "<div id=\"div_" + temp_var + "\">" + location + 
			"<a href=\"#\" onclick=\"displayVals(\\\'" + temp_var + "\\\',\\\'\\\',\\\'remove_tank\\\');\"><img src=\"' . $url . 'assets/Pics/Red_x.png\" width=\"16\" border=0></a></div>";	
			//var elSel = document.createElement(\'div\');
  			//newdiv.setAttribute(\'id\', id);	
			var elSel=document.getElementById("cur_tanks");
			var elOptNew = document.createElement("option");
			elOptNew.text = temp_var;
			elOptNew.id = "opt_" + temp_var;
			//elOptNew.value = "append" + num;

			try {
				elSel.add(elOptNew, null); // standards compliant; does not work in IE
			  }
			  catch(ex) {
				elSel.add(elOptNew); // IE only
			  }

		}
	    
    } 

</script> ';
					
					$_SESSION['preview_array']= $html;
    return $html;
} 
function output_batch_summary($data,$url,$report_array,$search_options){ 
	switch ($report_array[1]) {
		case "m":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;
		case "ml":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "l":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "u":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;			
	}
	echo '<script language="javascript"> 
			Shadowbox.init({ 
				players:    ["iframe"]	 
			});
			</script>'; 
				$tableID = "mylb_table"; 
				 if ($data['admin_access'] != "on"){
					$search_options['datatables_buttons']  .= "_user_access";
				 }
				$html .= table_format($tableID,'0','',$url . 'assets/server_processing.php',addslashes($search_options['datatables_fields']),addslashes($search_options['datatables_select']),addslashes($search_options['datatables_buttons']),addslashes($search_options['datatables_from']),addslashes($search_options['datatables_field_wtables']),addslashes($search_options['datatables_where']),'batch_ID');  
			 	echo '<div style="padding-left:10px;padding-right:10px;"><table><tr><td> 
			   <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/batch_summary\'"/>
			  <a href="' . $url . 'index.php/fish/print_prev_batchsum" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
		 	  </td></tr></table>';
			   $attributes = array('id' => 'fish_form_ID','name' => 'fish_form');
				echo form_open('/modify_line', $attributes);                      
				echo form_hidden('modify_line', $batch_ID); 
				echo form_hidden('batch_ID', ''); 
				   $html .=  '<table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
						<thead>';  
				   $html .= '<tr ><th ></th><th style=" width:5%">Batch&nbsp;#</th>
				  <th>Name</th>
				  <th>Status</th><th  >Birthday</th>
				  <th  >Date of Death</th>
				  <th  >User</th><th>Strain</th> 
				  <th  >Generation</th>
				  <th  >Cur Adults</th> 
				  <th  >Start Nursery</th>';
				    $html .= '</tr></thead><tbody> 	
						</tbody></table></div> ';	 
					$html .= '</form>  ';
					echo $html;	  
}

function output_quantity_summary($data,$url,$report_array){
	switch ($report_array[1]) {
		case "m":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;
		case "ml":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "l":
			echo '<h2>' . $report_array[0] . ' Lab</h2>';
			break;
		case "u":
			echo '<h2>' . $report_array[0] . '</h2>';
			break;			
	}
 
	echo '
	<style>
	.quantity_wrapper {
		margin-bottom:-130px;	
	}
	</style>
	<script language="javascript"> 
Shadowbox.init({ 
    players:    ["iframe"]	 
});
</script>'; 	 
				$_SESSION['preview_array']="";
				$tableID = "user_table";
				$html = table_format($tableID,'0','Quantity Summary','','','','','','','');   
			 	$html .= '<div id="standard_box" style="margin-left:20px;margin-right:20px;"><table><tr><td> 
			   <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/quantity_summary\'"/>
			   <a href="' . $url . 'index.php/fish/print_prev_quantsum" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
	 		   </td></tr></table>';
			   $attributes = array('id' => 'fish_form_ID','name' => 'fish_form'); 
			   $html .=  '<div class="quantity_wrapper"><table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
				<thead>';
			   $html .= '<tr ><th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
			   <th ><div class="sort">Total Batches</div></th>';			  		 
			   $html .= '</tr></thead><tbody>';	
			   foreach ($data['user_quant']->result() as $row): 
				   $_SESSION['report_data']['user_quant'][] = $row;
				   $html .= '<tr>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   
				   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
				endforeach; 				 
				$html .= '</tbody> </table></div>';
				echo $html;
				$_SESSION['preview_array']= $html; 
	  			$html = "";
	  			$tableID = "mutant_table";
				$html = table_format($tableID,'0','Mutant Summary','','','','','','','');  
			    $attributes = array('id' => 'mutant_form_ID','name' => 'mutant_form'); 
				$html .=  '<div class="quantity_wrapper"><table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
				<thead>'; 
			   $html .= '<tr ><th >Mutant</th>
			   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
			   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';	 
			   foreach ($data['mutant_quant']->result() as $row): 
				  $_SESSION['report_data']['mutant_quant'][] = $row;
				  if ($row->mutant){
					   $html .= '<tr>';
					   $html .= '<td>' .$row->mutant. '</td>';
					   $html .= '<td>' .$row->starting_adults. '</td>';
					   $html .= '<td>' .$row->current_adults . '</td>';
					   $html .= '<td>' . $row->starting_nursery . '</td>';
					   $html .= '<td>' . $row->starting_nursery . '</td>';
					   $html .= '<td>' .$row->total_batches . '</td> </tr>'; 
				  }
				endforeach; 				 
				$html .= '</tbody> </table></div> '; 
				$_SESSION['preview_array'] = $html;
				echo $html;
	   			$html = "";
	  			$tableID = "strain_table";
				$html = table_format($tableID,'0','Strain Summary','','','','','','','');  
				$html .=  '<div class="quantity_wrapper"><table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
				<thead>';  
			   $html .= '<tr ><th >Strain</th>
			   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
			   <th >Total Batches</th>';			  		 
			   $html .= '</tr></thead><tbody>';	
			   $index = "0";
			   foreach ($data['strain_quant']->result() as $row): 
				  $_SESSION['report_data']['strain_quant'][] = $row;
				  if ($row->strain){
				   $html .= '<tr>';
				   $html .= '<td>' .$row->strain. '</td>';
				   $html .= '<td>' .$row->starting_adults. '</td>';
				   $html .= '<td>' .$row->current_adults . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' . $row->starting_nursery . '</td>';
				   $html .= '<td>' .$row->total_batches . '</td> </tr>';
				   $index++;
				  }
				endforeach; 				 
				$html .= '</tbody> </table></div>'; 
				echo $html;					 
				$_SESSION['preview_array']= $html; 
				$html = "";
	  			$tableID = "transgene_table";
				$html = table_format($tableID,'0','Transgene Summary','','','','','','','');  
				$html .=  '<div class="quantity_wrapper"><table style="font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
				<thead>'; 
				   $html .= '<tr ><th >Promoter</th>
				   <th >Start Adults</th>
			   <th >Cur Adults</th>
			   <th >Start Nursery</th>
			   <th >Cur Nursery</th>
				   <th >Total Batches</th>  ';			  		 
				   $html .= '</tr></thead><tbody>';	 
				   $index = "0";
                   foreach ($data['transgene_quant']->result() as $row):
				 	  $_SESSION['report_data']['transgene_quant'][] = $row;
				   	  if ($row->promoter){
				   	   $html .= '<tr>';
					   $html .= '<td>' .$row->promoter. '</td>';
					    $html .= '<td>' .$row->starting_adults. '</td>';
					   $html .= '<td>' .$row->current_adults . '</td>';
					   $html .= '<td>' . $row->starting_nursery . '</td>';
					   $html .= '<td>' . $row->starting_nursery . '</td>';
					   $html .= '<td>' .$row->total_batches . '</td> </tr>';
					   $index++;
					  }
                  	endforeach; 				 
		   	 		$html .= '</tbody> </table></div></div> ';
					echo $html;				 
					$_SESSION['preview_array']= $html;         
}
function track_percentage($data,$url,$datefilter,$admin_access,$search_options_track){  
				$tableID = "track_table"; 
				echo '<div id="standard_box" style=" padding-top:30px; padding-bottom:30px;   margin-left:5px;width:850px; overflow-x: auto; overflow-y: hidden;  ">';
				$show .= '<h3>Show by month:<br>
				<select id="cursurvival_filter"><option></option>';
				$first_run = 1;
				foreach($datefilter->result_array() as $row){ 
					if ($first_run == 1){
						$show .=  '<optgroup label="' . date('Y',$row['date_taken']) . '">';	
					}elseif ($yearcheck != date('Y',$row['date_taken'])) {
						$show .=  '</optgroup><optgroup label="' . date('Y',$row['date_taken'])  . '">';	
					}
					$show .=  '<option value="' . $row['date_taken'] . '">' . date('F Y',$row['date_taken']) . '</option>';
					$yearcheck = date('Y',$row['date_taken']);
					$first_run++;
				}
				$show .=  '</optgroup></select>&nbsp;<a href="#" style="font-size:.8em" class="jq_buttons" onclick="show_filterby();">Show</a></h3>';
		 		if ($admin_access != "on"){
					$search_options_track['track_datatables_buttons'] .= "_user_access";
				}
				$html .= table_format($tableID,'0','',$url . 'assets/server_processing.php',addslashes($search_options_track['track_datatables_fields']),addslashes($search_options_track['track_datatables_select']),addslashes($search_options_track['track_datatables_buttons']),addslashes($search_options_track['track_datatables_from']),addslashes($search_options_track['track_datatables_field_wtables']),addslashes($search_options_track['track_datatables_where']),'STAT.batch_ID');  
				$html .=  '<table><tr><td> 
				<input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/survival_stat\'"/>
				<a href="' . $url . 'index.php/fish/print_prev_survivalstat" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
				</td><td>' . $show . '
				</td></tr><tr><td colspan=3>'; 
				$html .=  '<div style="width:800px;" >'; 
				$html .= '<table class="display" style="font-size:.8em"cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
				<thead>';  
				$html .= '<tr ><th  ></th><th style=" width:2%">Batch&nbsp;#</th>
				<th>Start Nursery</th>
				<th>Cur Adults</th><th>Start Adults</th>
				<th>Lab</th>
				<th>Status</th>
				<th>Survival Rate</th><th  >Birthday</th><th>Date of Death</th>
				<th  >Report Date</th>';			  		 
				$html .= '</tr></thead><tbody></tbody> </table>';			 
				$html .= '</div>
				</td></tr></table>';						
				echo $html;	?>  
				<script>
				function show_filterby(){
					if (document.getElementById("cursurvival_filter").value){
						Shadowbox.open({
							content:   "<?php echo $url; ?>index.php/fish/filter_track_survival/" + document.getElementById("cursurvival_filter").value,
							player:     "iframe",
							title:      "Track Current Survival",
							height:     800,
							width:      1100
						}); 
					}
				}
				</script> </div><?php				
}
function track_percentage_filtered($data,$url,$search_options_track){
  			 $_SESSION['percent_report_data']=""; 
			 	echo '<script language="javascript"> 
					Shadowbox.init({ 
						players:    ["iframe"]	 
					});
					</script>';
				$tableID = "track_filter_table";
				if ($data['admin_access'] != "on"){
					$search_options_track['track_datatables_buttons'] .= "_user_access";
				}
			 	$html .= table_format($tableID,'0','',$url . 'assets/server_processing.php',addslashes($search_options_track['track_datatables_fields']),addslashes($search_options_track['track_datatables_select']),addslashes($search_options_track['track_datatables_buttons']),addslashes($search_options_track['track_datatables_from']),addslashes($search_options_track['track_datatables_field_wtables']),addslashes($search_options_track['track_datatables_where']),'STAT.batch_ID');  
			 	echo '<table><tr>
				<td> 
			   <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/survival_month_stat/' . $data['date_taken'] . '\'"/>
			  <a href="' . $url . 'index.php/fish/print_prev_survivalstat" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>
		    </td></tr></table>';
			   $attributes = array('id' => 'fish_form_ID','name' => 'fish_form');
				echo form_open('/modify_line', $attributes);                      
				echo form_hidden('modify_line', $batch_ID); 
				echo form_hidden('batch_ID', '');  
				   $html .=  '<h2>Track Survival Percentage ' . date('F Y',$data['first_record']['date_taken']) . '</h2>
				   <div style="width:1040px;" ><table style=" font-size:.8em" class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';  
				   $html .= '<tr ><th ></th><th >Batch&nbsp;#</th>
				   <th>Start Nursery</th>
				  <th>Cur Adults</th><th>Start Adults</th>
				   <th>Lab</th><th>Status</th>
				  <th>Survival Rate</th><th  >Birthday</th>
				   <th  >Date of Death</th><th  >Report Date</th>';			  		 
				   $html .= '</tr></thead><tbody>';
				   $html .= '</tbody> </table>'; 
				   $html .= ' </form> ';
				   echo $html;					 
                   ?>  
                     <script> 
				function show_update(update_var){
					 	 Shadowbox.open({
							content:    "<?php echo $url; ?>index.php/fish/modify_line/u_" + update_var,
							player:     "iframe",
							title:      "Update",
							height:     700,
							width:      930
						});  
				}
				</script> 
	  <?php
}
function track_current($data,$url,$admin_access,$search_options_survival){
  		 $_SESSION['current_report_data']=""; 
				$tableID = "track_current";  
				if ($admin_access != "on"){
					$search_options_survival['survival_datatables_buttons'] .= "_user_access";
				}
				$html = table_format($tableID,'0', '',$url . 'assets/server_processing.php',addslashes($search_options_survival['survival_datatables_fields']),addslashes($search_options_survival['survival_datatables_select']),addslashes($search_options_survival['survival_datatables_buttons']),addslashes($search_options_survival['survival_datatables_from']),addslashes($search_options_survival['survival_datatables_field_wtables']),addslashes($search_options_survival['survival_datatables_where']),'batch_ID'); 
	 			?>
        	 	<div id="standard_box" style=" padding-top:30px; padding-bottom:30px; margin-left:30px;  width: 800px; ">
           		 <?php
			 	 $html .= '<table><tr>
				<td> 
			   <input type="image"  src="' . $url . 'assets/Pics/File-Excel-48.png" name="doit"   onClick="location.href=\'' . $url . 'index.php/fish/export/survival_current\'"/>
			  <a href="' . $url . 'index.php/fish/print_prev_currentstat" target="_blank"><img border=0 src="' . $url . 'assets/Pics/Print-Preview-48.png"></a>';				 
			    $html .= '</td></tr><tr><td colspan=3>';
			    $attributes = array('id' => 'fish_form_ID','name' => 'fish_form');
				 $html .= form_open('/modify_line', $attributes);                      
				 $html .= form_hidden('modify_line', $batch_ID); 
				 $html .= form_hidden('batch_ID', ''); 
				    $html .=  '<div style="width:795px;" ><table class="display" style=" font-size:.8em" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $tableID . '">
								<thead>';  
				      $html .= '<tr > 
				   <th ></th>
				   <th>Batch&nbsp;#</th>
				   <th>User</th> 
				   <th>Lab</th>
				  <th>Cur Adults</th><th>Start Adults</th><th>Start Nursery</th>
				  <th>Cur Nursery</th>
				  <th>Birthday</th>
				   <th>Survival Rate</th>
				   </thead><tbody>	
						</tbody></table> ';		  		 
				   
				   echo $html;  
					
					$html = '</div> </form>
					</td></tr></table>';
					echo $html;					 
                   ?>  
	 			</div><?php
}  
function search_function($all_users,$all_mutants,$all_strains,$all_transgenes,$quantity,$batch_sum,$url,$all_labs,$all_tanks,$all_searches,$all_mutant_allele,$all_transgene_allele){	
	$html .= '<table><tr><td valign="top">';	 
	$attributes = array('id' => 'search_form_ID','name' => 'search_form');
	$html .= form_open($url . 'fish/search_data', $attributes);   
	$html .= '<div id="standard_box">
	<table><tr><td><h2>Custom Search</h2></td><td style=" padding-left:20px">My Lab Results: <input type="checkbox" name="mylab" checked></td></tr></table>'; 
 	$html .= '<a class="jq_buttons" onclick="submit_search_data();">Search</a>';	 
	$html .=  '<table style=" font-size:12px; font-family:Verdana, Geneva, sans-serif;"><tr><td>';
	$html .= 'Batch Number:<br><input type="text" name="batch_ID">
	</td><td>';
	$html .=  'User: <br><select name="user_ID"><option></option>';
 	foreach ($all_users->result() as $row){
		if ($row->user_ID  == $refresh['user_ID']){
			$html .=  '<option value="' . $row->user_ID  . '">' . $row->username . '</option>'; 
		}else{
			$html .=  '<option value="' . $row->user_ID  . '">' . $row->username . '</option>';
		}
	}  
	$html .=  '</select>
	</td><td>';
    $html .= 'Batch Name:<br><input type="text" name="batch_name" value="' .  $refresh['batch_name'] . '">';
	$html .= '</td></tr><tr><td>'; 
 	$html .= 'Status:<br><select name="status"><option></option>';
	$status_array = "";
	$status_array[0] = "Alive";
	$status_array[1] = "Dead";
	$status_array[2] = "Sick";	 
	foreach($status_array as $value){
		if ($value == $refresh['status']){
			$html .= '<option selected>' . $value . '</option>';
		}else{
			$html .= '<option>' . $value . '</option>';
		}
	}
	$html .= '</select>'; 
	$html .= '</td><td>';
	$html .= 'Gender:<br><select name="gender"><option></option>';
	$gender_array = "";
	$gender_array[0] = "M";
	$gender_array[1] = "F";	
	$gender_array[2] = "Mixed"; 	 
	foreach($gender_array as $value){
		if ($value == $refresh['gender']){
			$html .= '<option selected>' . $value . '</option>';
		}else{
			$html .= '<option>' . $value . '</option>';
		}
	}
	$html .= '</select>';
	$html .= '</td><td>';
	$html .=  'Strain: <br><select name="strain_ID"><option></option>';
 	foreach ($all_strains->result() as $row){
		if ($row->strain_ID == $refresh['strain_ID']){
			$index="1";
			$html .=  '<option value="' . $row->strain_ID  . '" selected>' . $row->strain  . '</option>';
		}else{
			$html .=  '<option value="' . $row->strain_ID  . '">' . $row->strain . '</option>';
		}	
	 }  
	$html .=  '</select>'; 
	$html .= '</td></tr><tr><td>'; 	
	$html .= 'Lab:<br><select name="lab"><option></option>';
 	foreach ($all_labs->result_array() as $row){
  		$html .= '<option value="' . $row['lab_ID'] . '">' . $row['lab_name'] . '</option>'; 
	} 
	$html .= '</select>';
	$html .= '</td><td>';
	$html .=  'Tank: <br><input name="tank_ID" value="' . $refresh['tank_ID'] . '">'; 
	$html .= '</td><td>';
	$gen_array = "";
	$gen_array[0] = "outcross/F0";
	$gen_array[1] = "F1";
	$gen_array[2] = "F2";
	$gen_array[3] = "F3";
	$html .= 'Generation: <br><select name="generation"><option></option>';
	foreach($gen_array as $value){ 
		$html .= '<option>' . $value . '</option>';	 
	}
	$html .= '</select>';
	$html .= '</td></tr><tr><td>';
	$html .= 'Birthday: <br>'; 
	$birthday =  'empty';	
	$html .= output_cal_func('birthday', $birthday,'birthday');	 
	$html .= '</td><td>';
	$html .= 'Date of Death: <br>'; 
	$death_date =  'empty';	
	$html .= output_cal_func('death_date', $death_date,'death_date');	 
	$html .= '</td></tr><tr><td colspan=4>';
	$html .=  'Mother: <br><input type="text" name="mother_ID">'; 
	$html .= '</td></tr><tr><td colspan=4>';	 
	$html .=  'Father: <br><input type="text" name="father_ID">'; 
	$html .= '</td></tr><tr><td colspan=4>
	<table><tr><td>';
	$html .=  '<div id="plain_box" ><h3 style=" padding:0px;margin:0px">Mutant</h3>';	
	$html .= '<table><tr><td>Name:<br><select name="mutant_ID"><option></option>';
 	foreach ($all_mutants->result() as $row){ 
		$html .= '<option value="' . $row->mutant_ID . '">' .  $row->mutant . '</option>'; 
	}	 
	$html .= '</select></td>'; 
	$html .= '<td>Allele:<br><select name="mutant_allele"><option></option>';
	foreach ($all_mutant_allele->result() as $row){ 
		$html .= '<option>' . $row->allele . '</option>'; 
	}
 	$html .= '</select>';
	$html .= '</td></tr></table>';	
  	$html .= ' +/+ <input type="checkbox" name="mutant_genotype_wildtype"  >';
  	$html .= ' +/- <input type="checkbox" name="mutant_genotype_heterzygous"  >';
	$html .= ' -/- <input type="checkbox" name="mutant_genotype_homozygous"  >';
 	$html .= '</div></td></tr><tr><td>';  	
	$html .=  '<div id="plain_box"  ><h3 style=" padding:0px;margin:0px">Transgene</h3>';	
	$html .= '<table><tr><td>Name:<br><select name="transgene_ID"><option></option>';
	foreach ($all_transgenes->result() as $row){ 
		$html .= '<option value="' . $row->transgene_ID . '">' . $row->transgene . '</option>'; 
	}
 	$html .= '</select></td>'; 
	$html .= '<td>Allele:<br><select name="transgene_allele"><option></option>';
	foreach ($all_transgene_allele->result() as $row){ 
		$html .= '<option>' . $row->allele . '</option>'; 
	}
 	$html .= '</select>';
	$html .= '</td></tr></table>';
	$html .= ' +/+ <input type="checkbox" name="transgene_genotype_wildtype"  >';
	$html .= ' +/- <input type="checkbox" name="transgene_genotype_heterzygous"  >';	 
	$html .= ' -/- <input type="checkbox" name="transgene_genotype_homozygous"  >';		
	$html .= '</div></div></td></tr></table>'; 
	$html .= '</td></tr><tr><td colspan=6>';
	$html .= 'Comment:<br><textarea name="comments" cols="60" rows="5"></textarea>';
	$html .= '</td></tr></table>'; 
	$html .= '</div></form>  
	</td><td valign="top" style=" width:600px;">';
 	$html .=  '<div id="tab_reports" > 
						<ul  >
							<li><a href="#t-0">Saved Searches</a></li>
							<li><a href="#t-1">Quantity</a></li>
							<li><a href="#t-2">Batch</a></li> 
						</ul>
						<div id="t-0" > 	
							<div id="standard_box" style="width:330px;">
							<h2>Save Search Criteria</h2> <form id="saved_search_ID" name="saved_search_form" action="' . $url . 'index.php/fish/index/nsearch/2" method="post">
						 	Name:<input name="search_name" type="text">	<a href="#" onclick="save_search();" class="jq_buttons" style=" font-size:12px;">Save</a>						
							<div id="hidden_vars" style="visibility:hidden; position:absolute;"></div>
							</form></div><br>
							<h3>Searches</h3>';
						$ID = "saved_searches_table";
						$html .= table_format($ID,'0','','','','','','','',''); 
						$html .=  '	<table class="display" cellpadding="0" cellspacing="0" border="0" class="display" id="' . $ID . '">
									<thead><tr><th width="1"></th><th>Search Name</th></tr></thead><tbody>';
						foreach ($all_searches->result() as $row){
							$html .= '<tr class="gradeC">';
							$html .= '<td style=" width:5px;"><a href="#" onclick="remove_search(\'' . $row->search_ID . '\');"><img border=0 width="14" src="' . $url . 'assets/Pics/Red_x.png"></a></td>';
							$html .= '<td>' . $row->search_name . '<a href="#" onclick="load_saved_search(\'' . $row->search_ID . '\');"><img border=0 width="14" src="' . $url . 'assets/Pics/Search-32.png"></a></td>'; 
							$html .= '</tr>';  
						}
						$html .=  '</tbody></table> 
						</div>
						<div id="t-1" > 
							<div id="standard_box" > 
								<h2>Quantity Reports</h2>';
								$html .= $quantity; 
								$html .=  '
							</div> 
						</div><!-- t-1-->
						<div id="t-2"> 
							<div id="standard_box"  >  
							 <h2>Batch Reports</h2>';
							$html .= $batch_sum; 
							$html .='
							</div>
					</div><!-- t-2-->
				</div><!--vreports-->
			</td></tr></table>
			'; 
	echo $html; 
	?> 
    <script language="javascript">   
	function submit_search_data(){		 
		 Shadowbox.open({
			content:    "<?php echo $url; ?>index.php/fish/submit_search_data",
			player:     "iframe",
			title:      "Search",
			height:     800,
			width:      1080
		}); 
	}
	function remove_search(searchID){
		Shadowbox.open({ 
			content:    "<?php echo $url; ?>index.php/fish/modify_search/r_" + searchID,
			player:     "iframe",
			title:      "Search",
			height:     500,
			width:      500
		}); 
	}
	function load_saved_search(searchID){ 
		Shadowbox.open({
			content:    "<?php echo $url; ?>index.php/fish/load_search_data/" + searchID,
			player:     "iframe",
			title:      "Search",
			height:     800,
			width:      1200
		}); 
	}
	function save_search(){
				var new_form_var = document.getElementById("hidden_vars");
				var form_var = document.search_form; 
				for(i=0; i<form_var.elements.length; i++){	 
					if (form_var.elements[i].checked == true && form_var.elements[i].type == "checkbox"){
						var newinput = document.createElement("input");
						new_form_var.appendChild(newinput);	
						newinput.name = form_var.elements[i].name;
						newinput.value = "1";   
					}else if(form_var.elements[i].checked == false && form_var.elements[i].type == "checkbox"){ 
					}else if(form_var.elements[i].value == ""){ 
					}else{ 
						var newinput = document.createElement("input");
						new_form_var.appendChild(newinput); 
						newinput.name = form_var.elements[i].name;
						newinput.value = form_var.elements[i].value; 
						newinput.id = i;  
					}  				
				} 
				document.saved_search_form.submit();	
			}
	</script>
    <?php
	
}

function directory_tree($address,$url){
 @$dir = opendir($address);
  if(!$dir){ return 0; }
        while($entry = readdir($dir)){
                if(is_dir("$address/$entry") && ($entry != ".." && $entry != ".")){                            
                        directory_tree("$address/$entry",$comparedate);
                } else   {
                  if($entry != ".." && $entry != ".") {                 
                    $fulldir=$address.'/'.$entry;
                    $last_modified = filemtime($fulldir);
                    $last_modified_str= date("Y-m-d h:i:s", $last_modified);
                    $html .= '<tr><td>
					<input type="image" width="20" src="' . $url . 'assets/Pics/Task Report Regular_32.png" name="doit" value="Open ShadowBox" onclick="Shadowbox.open({player:\'iframe\',height:500,width:500, title:\'Confirm\', content:\'' . $url . 'assets/scheduled_reports/' . $entry . '\'}); return false" /> 
			 		</td><td>' . $entry . '</td></tr>';                                                  
                 }
            }
      }
	  return $html;
}
?>