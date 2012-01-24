<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Zebrafish</title> 
<link href="http://zebase.bio.purdue.edu/oneColFixCtrHdr.css" rel="stylesheet" type="text/css" />
<link href="http://zebase.bio.purdue.edu/js/lavalamp.css" rel="stylesheet" type="text/css" />
<script src="http://zebase.bio.purdue.edu/js/jquery-1.4.1.min.js" type="text/javascript"></script>
<!-- <script src="js/lavalamp.js" type="text/javascript"></script>-->
<script type="text/javascript" src="http://zebase.bio.purdue.edu/js/jquery.lavalamp.js"></script>
<script type="text/javascript" src="http://zebase.bio.purdue.edu/js/jquery.easing.1.1.js"></script>  
    <script type="text/javascript">
        $(function() {
            $("#1").lavaLamp({
                fx: "backout", 
                speed: 700,
                click: function(event, menuItem) {
                     
                }
            });
        });
    </script>

<script language="javascript">
function sub_data(){
	if (document.login_form.username.value == "" || document.login_form.pass.value == ""){
		alert("Please make sure you fill in your username and password!");
	}else{
		document.login_form.submit();	
	}
}
function submitenter(myfield,e)
{
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
   myfield.form.submit();
   return false;
   }
else
   return true;
}

</script>   
</head>
<body   onload="document.login_form.username.focus();"> 
 <div class="container">
 <div class="header">
  <div class="statement"> <img src="http://zebase.bio.purdue.edu/images/zebase-blue.png" alt="Zbase" width="185" height="50" hspace="10" />an open-source database for zebrafish inventory</div>
  <a href="http://www.purdue.edu"><img src="http://zebase.bio.purdue.edu/images/purdue.jpg" alt="Insert Logo Here" name="Insert_logo" width="194" height="90" border="0" id="Insert_logo" style="display:block;" /></a>
  <div class="orange">  
<ul class="lavaLampWithImage" id="1" style="width: 1100px;">  
<li class="current"><a href="http://zebase.bio.purdue.edu/index.html">Home</a></li>  
<li><a href="http://zebase.bio.purdue.edu/install.html" >Install</a></li>
<li><a href="http://zebase.bio.purdue.edu/fish/index.php/fish/login">Demo</a></li>   
<li><a href="http://zebase.bio.purdue.edu/resources.html">Resources</a></li>  
 
<li><a href="http://zebase.bio.purdue.edu/new.html">What's New</a></li>  
 <li><a href="http://zebase.bio.purdue.edu/developers.html">Developers</a></li> 
 <li><a href="http://zebase.bio.purdue.edu/contacts.html">Contacts</a></li>  
</ul>
  </div>   
  
  <!-- end .header --></div> 
  <div class="content">
<?php
	$attributes = array('id' => 'login_form_ID','name' => 'login_form');
	echo form_open('fish/send_login', $attributes); 
?>
<h1 style="font-size:4.5em">Demo</h1> 
<div style="width: 500px; height:300px; margin-left:30%; text-align: left"> <div style="padding-top:50px;">
          
                 <?php
				if ($attempt == "f"){
					echo '<div style="color:#F00; padding-left:100px;">Username or Password is incorrect!</div>';
				}
				?>
                    <table>
                    <tr><td><strong>Username</strong>:</td><td>admin</td></tr>
                    <tr><td><strong>Password</strong>:</td><td>user</td></tr>
                    <tr><td><br /></td><td></td></tr>
                    <tr><td><strong>Username</strong>:</td><td><input type="text" style="width: 160px;" name="username" /></td></tr>
                    <tr><td><strong>Password</strong>:</td><td> <input onKeyPress="return submitenter(this,event)" type="password" style="width: 160px; margin-top: 10px;" name="pass"/></td></tr>
                    </table> 
                  <input type="button" onclick="sub_data();" value="Sign In" />
                </div> </div> 
</form> 
  <!-- end .content --></div>
   
  <!-- end .container --></div>
</body>
</html>