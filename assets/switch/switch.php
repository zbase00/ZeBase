<?php
session_start();
if ($_GET['type'] == "off"){
	$_SESSION['scanning'] = "disabled";
	echo '<script language="javascript">
	function KeyCheck(event){
		var KeyID = event.keyCode; 
		alert(KeyID);
	}
	document.onkeyup = "";
	document.getElementById("scan_mode").style.visibility = "hidden";
	</script>';
}elseif ($_GET['type'] == "on"){  
	$_SESSION['scanning'] = "enabled";
	echo '<script language="javascript">
	document.onkeyup = KeyCheck;  
	document.getElementById("scan_mode").style.visibility = "visible"; 
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
					Shadowbox.open({
						content:    url_link,
						player:     "iframe",
						title:      "Update",
						height:     800,
						width:      1100
					});
				}  	
			}
			function check_barcode_function(all_keys){
				var i ="";
				var string_check = "";	 
				for (i=0; i<all_keys.length; i++){
					string_check += all_keys[i];		 
				}
				var search_check = string_check.search("m01");
				if (search_check != -1){
					id_fix = string_check.replace("m01","");		 
					this.location.href= "' . $url . 'index.php/fish/modify_line/u_" + id_fix;
				}else{		 
					return "false";
				}
			}
			
	</script>';
}
?>