<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Zebrafish</title>  
<link href="<?php echo $this->config->item('base_url') ?>style/oneColFixCtrHdr_nonav.css" rel="stylesheet" type="text/css" />
 
<?php
$url = $this->config->item('base_url');
libraries($url);
?> 
<script language="javascript"> 

$(function() {
	$("#vtabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
	$("#vtabs li").removeClass('ui-corner-top').addClass('ui-corner-left'); 
	$("#wq_vtabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
	$("#wq_vtabs li").removeClass('ui-corner-top').addClass('ui-corner-left'); 
}); 
$(function() { 
	$( "#admin_acc" ).accordion({ active: <?php
	if ($url_var_5 && is_numeric($url_var_5)){
		echo $url_var_5;
	}else{
		echo 0;
	}
	?>});
}); 

$(function() {  
	$("#tabs").tabs({selected:2});   
	$("#tab_reports").tabs({selected:0}); 
});
 
function move_next(move_var){	 
	sp1.showPanel(move_var); 
	return false;	
}
function open_summary(report_var){
	if (report_var == "selected_user"){
		 var temp = document.getElementById("batch_user_select"); 
		 var report_v = temp.options[temp.selectedIndex].value + '_u';		
	}else if (report_var == "entire_lab"){
		var temp = document.getElementById("batch_lab_select"); 
		var report_v = temp.options[temp.selectedIndex].value + '_l';
	}else if (report_var == "mylab"){
		var report_v = report_var + '_ml';
	}else{
		var report_v = report_var + '_m';
	} 
	 Shadowbox.open({
		content:    "<?php echo $url; ?>index.php/fish/batch_summary/" + report_v,
		player:     "iframe",
		title:      "Batch Summary",
		height:     800,
		width:      1100
	}); 
}
function open_qsummary(report_var){
	
	if (report_var == "selected_user"){ 
		var temp = document.getElementById("quantity_user_select");
		var report_v = temp.options[temp.selectedIndex].value + '_u'; 
	}else if (report_var == "entire_lab"){
		var temp = document.getElementById("quantity_lab_select");
		var report_v = temp.options[temp.selectedIndex].value + '_l';
	}else if (report_var == "mylab"){
		var report_v = report_var + '_ml';
	}else{
		var report_v = report_var + '_m';
	}  
	Shadowbox.open({
		content:    "<?php echo $url; ?>index.php/fish/quantity_summary/" + report_v,
		player:     "iframe",
		title:      "Quantity Summary",
		height:     800,
		width:      1100
	}); 
}  
</script> 
   
</head>
<body class="main_body blue" onload='document.getElementById("tabs").focus'>  
<div class="container">
  <div class="header2">
  <div class="statement"> <img src="<?php echo $this->config->item('base_url') ?>style/images/zebase-blue.png" alt="Zbase" width="185" height="50" hspace="10" />an open-source database for zebrafish inventory</div>
    <!-- end .header --></div>
   <div style=" float:right; margin-right:20px;"><?php echo anchor('fish/logout', 'Logout', 'style="font-size: 1.2em; font-weight:800 "'); ?></div>
  <div class="content"  >
 <div id="tabs" style="margin-left:10px; margin-top:20px; margin-right:10px;"><table><tr><td>
           <ul style="width:800px;">
           <li><a href="#tabs-3">Quick Stats</a></li>
           <li><a href="#tabs-0">My Lab Fish</a></li>
           <li><a href="#tabs-1">Search</a></li>  
           <li><a href="#tabs-4">Settings</a></li>
           <li><a href="#tabs-5">Scheduled Reports</a></li>
           <li><a href="#tabs-6">Water Quality</a></li>
           </ul>
             </td><td><div style="position:absolute;"><input type="hidden" name="batch_number" id="batch_num" ></div>
             </td><td><div style="padding-left:380px;"> </div></td></tr></table>
            <div id="tabs-3" > 
           		 <div id="vtabs" style="width:1050px"> 
                        <ul >
                            <li><a href="#tabs-a">Overall Stats</a></li>
                            <li><a href="#tabs-b">Track Survival</a></li>
                            <li><a href="#tabs-c">Current Survival</a></li>
                        </ul>
                        <div id="tabs-a"> 
                                        <table cellpadding="10" ><tr><td>
                                        <div id="standard_box">
                                         <h2>Current Fish Count</h2><div style="padding-left:15px;">                           	 
                                          <?php 
                                          $count = "";
                                          foreach ($current_count as $lab){?>
                                              <?php foreach ($lab->result() as $row):?>
                                               <?=$row->lab_name?>:&nbsp;&nbsp;<?=$row->fish_count?> <br />                                
                                              <?php 
                                              $count += $row->fish_count;
                                              endforeach; ?>	
                                          <?php } ?><br /> 
                                           All Labs:&nbsp;&nbsp;<?=$count?> 
                                          </div></div>
                                           </td><td >
                                           <div id="standard_box">
                                           <h2>Current Nursery Count</h2><div style="padding-left:15px;">
                                          <?php  
                                           $count = "";
                                          foreach ($nurseryq_count as $lab){?>
                                              <?php foreach ($lab->result() as $row):?>
                                               <?=$row->lab_name?>:&nbsp;&nbsp;<?=$row->fish_count?> <br />                                  
                                              <?php 
                                              $count += $row->fish_count;
                                              endforeach; ?>	
                                          <?php } ?><br /> 
                                            All Labs:&nbsp;&nbsp;<?=$count?>	 
                                           </div></div>   
                                          </td></tr></table> 
                         </div><!--tabs-a-->  
                        <div id="tabs-b" > <div style="width:900px"> 
                             <h2 style="padding-left: 20px;"  >Track Survival Percentage <?php echo  date('F Y',time()); ?></h2>  
		                      <?php 
							track_percentage($track_percentage,$url,$datefilter,$admin_access,$search_options_track);
							?></div> 
                            </div><!--tabs-b--> 
                             <div id="tabs-c">  
                             <h2 style="padding-left: 20px;"  >Current Survival</h2> 
        				   <?php 
							 track_current($current_survival,$url,$admin_access,$search_options_survival);
							?> 
                            </div><!--tabs-c--></div><!--vtab-->
        
            </div>  
           <div id="tabs-0" >  
			 <?php 
			 show_lab_fish($url,$all_lab_fish,$loggedin_user,$admin_access,$search_options);			 
			 ?>
           </div> <!--tab-0-->
             <div  id="tabs-1" >
            <?php
				$quantity .=' '; 
				$attributes = array('id' => 'quantity_sum_form_id','name' => 'quantity_sum_form');
				$quantity .= form_open('', $attributes);	 
				$quantity .='
				<table><tr><td  >&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#"  onclick="open_qsummary(\'my\');" class="jq_buttons">My Quantities</a> 
				</td></tr><tr><td  ><br>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#"  onclick="open_qsummary(\'mylab\');" class="jq_buttons" >My Lab Quantities</a>
			 	</td></tr><tr><td valign="top">
				<h3>Select a Lab:</h3>&nbsp;&nbsp;&nbsp;&nbsp;<select name="lab" id="quantity_lab_select"><option></option>';
				foreach ($all_labs->result() as $row){ 
					$quantity .=' <option value="' . $row->lab_ID . '">' . $row->lab_name . '</option>';
				}		
				$quantity .=' </select> <a href="#"  onclick="open_qsummary(\'entire_lab\');" class="jq_buttons">Go</a>
				</form>
				</td></tr><tr><td>
				<h3>Select a User:</h3>&nbsp;&nbsp;&nbsp;&nbsp;<select name="user" id="quantity_user_select"   ><option></option>';
				foreach ($all_users->result() as $row){
					$quantity .=' <option value="' . $row->user_ID . '">' . $row->username . '</option>';
				}	
				$quantity .='</select> <a href="#"  onclick="open_qsummary(\'selected_user\');" class="jq_buttons">Go</a>
				<br /><br />  
				</td></tr></table> 
				 </form> '; 
				
				$batch_sum .=' ';
				$attributes = array('id' => 'batch_sum_form_id','name' => 'batch_sum_form');
				$batch_sum .= form_open('', $attributes); 
				$batch_sum .= '<table><tr><td  >  &nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#"  onclick="open_summary(\'my\');" class="jq_buttons">My Batches</a>&nbsp;&nbsp;&nbsp;&nbsp;
				</td></tr><tr><td  ><br>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#"   onclick="open_summary(\'mylab\');" class="jq_buttons">My Lab Batches</a>
		 		</td></tr><tr><td valign="top">
				<h3>Select a Lab:</h3>&nbsp;&nbsp;&nbsp;&nbsp;<select name="lab" id="batch_lab_select"   ><option></option>';
				foreach ($all_labs->result() as $row){
					$batch_sum .= '<option value="' . $row->lab_ID . '">' . $row->lab_name . '</option>';
				}	
				$batch_sum .= '</select> <a href="#"  onclick="open_summary(\'entire_lab\');" class="jq_buttons">Go</a>
				</form>
				</td></tr><tr><td>
				<h3>Select a User:</h3>&nbsp;&nbsp;&nbsp;&nbsp;<select name="user" id="batch_user_select"><option></option>';
				foreach ($all_users->result() as $row){
					$batch_sum .= '<option value="' . $row->user_ID . '">' . $row->username . '</option>';
				}	
				$batch_sum .= '</select> <a href="#"  onclick="open_summary(\'selected_user\');" class="jq_buttons">Go</a>
				<br /><br />  
				</td></tr></table> 
				</div></form>';
				search_function($all_users,$all_mutants,$all_strains,$all_transgenes,$quantity,$batch_sum,$url,$all_labs,$all_tanks,$all_searches,$all_mutant_allele,$all_transgene_allele);
				 ?>
            </div>  
              <div id="tabs-5" > 
				<?php
                    output_report_recipients($url,$all_users,$all_report_recipients);
                ?>
            </div><!--tabs-5-->  
             <div id="tabs-4" > 
          			 <div id="admin_acc"   > 
                       <?php if ($admin_access == "on"){ ?>
                                <h3><a href="#">Users</a></h3> 
                                <div id="tab_admin-1" style="height:500px;">  
                                <?php
                                    output_users($url,$all_users); 
                                ?>  
                                 </div> 
                                 <h3><a href="#">Tanks</a></h3> 
                                 <div id="tab_admin-4"><div style="height:500px; width:800px;">  
                                 <?php 
	                                output_tanks($url,$all_tanks);									 
                                 ?>
                                 </div>  </div> 
                                 <h3><a href="#">Other Attributes</a></h3> 
                                 <div id="tab_admin-2" > <div style="height:500px;">
                                <table><tr><td valign="top" >
                                 <?php
                                    output_mutants($url,$all_mutants);
                                 ?>
                                </td></tr><tr><td valign="top" >
                                 <?php 
                                    output_strains($url,$all_strains)
                                 ?> 
                                 </td></tr><tr><td valign="top" >
                                 <?php 
                                    output_transgenes($url,$all_transgenes)
                                 ?>
                                </td></tr><tr><td valign="top" >
                                 <?php 
                                    output_labs($url,$all_labs);
                                 ?>
                                  </td></tr></table>
                                  </div> </div>
                        <?php }else{ ?>
                                  <h3><a href="#">Other Attributes</a></h3>  
                                  <div id="tab_admin-3"  > <div style="height:500px;">
                                <table><tr><td valign="top" >
                                 <?php
                                    output_mutants_user($url,$all_mutants);
                                 ?>
                                </td></tr><tr><td valign="top" >
                                 <?php 
                                    output_strains_user($url,$all_strains)
                                 ?> 
                                 </td></tr><tr><td valign="top" >
                                 <?php 
                                    output_transgenes_user($url,$all_transgenes)
                                 ?>
                                 </td></tr></table>
                                  </div></div> 
                          <?php }?> 
                        </div><!--admin_acc-->
             </div><!--tabs-4-->    
            <div id="tabs-6" > 
           		 <div id="wq_vtabs" style="width:1000px"> 
                        <ul >
                            <li><a href="#wq_vtabs-a">Water Quality</a></li>
                            <li><a href="#wq_vtabs-b">Nitrate</a></li>
                            <li><a href="#wq_vtabs-c">Nitrite</a></li>
                            <li><a href="#wq_vtabs-d">pH</a></li>
                            <li><a href="#wq_vtabs-e">Conductivity</a></li>
                            <li><a href="#wq_vtabs-f">D.O.</a></li>
                            <li><a href="#wq_vtabs-g">Temperature</a></li>
                        </ul>
                        <div id="wq_vtabs-a">
                        <?php
							output_water_quality($url,$search_water_quality);
						?>
                        </div>
                        <div id="wq_vtabs-b">
                        	<table><tr><td>
                        	<?php			
							output_chart_ranges($url,'nitrate','nchart'); 
							?><br />
                            </td></tr>
                            <tr><td>
                            <?php	 
							output_nitrate_chart($water_quality); 
							?>
                            </td></tr></table>
                        </div>
                        <div id="wq_vtabs-c">
                      	    <table><tr><td>
                        	<?php							
							output_chart_ranges($url,'nitrite','nichart');
							?>
                            <br />
                            </td></tr>
                            <tr><td>
                        	<?php 
							output_nitrite_chart($water_quality); 
							?>
                            </td></tr></table>
                        </div>
                        <div id="wq_vtabs-d">
                       		<table><tr><td>
                        	<?php			
							output_chart_ranges($url,'ph','phchart'); 
							?><br />
                            </td></tr>
                            <tr><td>
							<?php
                            output_ph_chart($water_quality);
                            ?>
                        </td></tr></table>
                        </div>
                        <div id="wq_vtabs-e">
                         	<table><tr><td>
                        	<?php			
							output_chart_ranges($url,'conductivity','cchart'); 
							?><br />
                            </td></tr>
                            <tr><td>
							<?php
                            output_conductivity_chart($water_quality);
                            ?>
                            </td></tr></table>
                        </div>
                        <div id="wq_vtabs-f">
                            <table><tr><td>
                            <?php			
                            output_chart_ranges($url,'do','dhart'); 
                            ?><br />
                            </td></tr>
                            <tr><td>
                            <?php
                            output_do_chart($water_quality);
                            ?>
                            </td></tr></table>
                        </div>
                        <div id="wq_vtabs-g">
                         <table><tr><td>
                            <?php			
                            output_chart_ranges($url,'temperature','thart'); 
                            ?><br />
                            </td></tr>
                            <tr><td>
							<?php
                            output_temperature_chart($water_quality);
                            ?>
                            </td></tr></table>
                        </div>
                   </div>
				
            </div><!--tabs-6-->    
           </div> <!--tabs-->  
   
<script language="javascript">
$(function() { 
	$("#tabs").tabs({selected:0}); 
	<?php
	if ($url_var_4 && is_numeric($url_var_4)){
		echo '$("#tabs").tabs({selected:' . $url_var_4 . '});';
	}
	?>  
});
$('#1').iphoneSwitch("<?php
		if ($_SESSION['scanning'] == "enabled"){
			 echo 'on'; 
		}else{
			echo 'off'; 
		}?>",
		function() {$('#ajax').load('<?php echo $url; ?>assets/switch/switch.php?type=on');},
		function() {$('#ajax').load('<?php echo $url; ?>assets/switch/switch.php?type=off');}, 
		{switch_on_container_path: '<?php echo $url; ?>assets/switch/iphone_switch_container_off.png'
	});
<?php 
if ($_SESSION['scanning'] == "enabled"){
	echo "$(document).ready(function(){
		$('#ajax').load('" .  $url . "assets/switch/switch.php?type=on');
	});";
}else{
	echo 'document.getElementById("scan_mode").style.visibility = "hidden";';	
}

?>

</script> 
    <!-- end .content --></div></div>
 
  <!-- end .container --></div>
</body>
</html>