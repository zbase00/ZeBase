<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Zebrafish</title> 
<link href="../../../oneColFixCtrHdr.css" rel="stylesheet" type="text/css" />
<link href="../../../js/lavalamp.css" rel="stylesheet" type="text/css" />
<script src="../../../js/jquery-1.4.1.min.js" type="text/javascript"></script>
<!-- <script src="js/lavalamp.js" type="text/javascript"></script>-->
<script type="text/javascript" src="../../../js/jquery.lavalamp.js"></script>
<script type="text/javascript" src="../../../js/jquery.easing.1.1.js"></script>  
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
  
</head>
<body> 

<div class="container">
 <div class="header">
  <div class="statement"> <img src="../../../images/zebase-blue.png" alt="Zbase" width="185" height="50" hspace="10" />an open-source database for zebrafish inventory</div>
  <a href="http://www.purdue.edu"><img src="../../../images/purdue.jpg" alt="Insert Logo Here" name="Insert_logo" width="194" height="90" border="0" id="Insert_logo" style="display:block;" /></a>
  <div class="orange">  
<ul class="lavaLampWithImage" id="1" style="width: 1100px;">  
<li class="current"><a href="../../../index.html">Home</a></li>  
<li><a href="../../../install.html" >Install</a></li>
<li><a href="http://zebase.bio.purdue.edu/fish/index.php/fish/login">Demo</a></li>   
<li><a href="../../../resources.html">Resources</a></li>  
 
<li><a href="../../../new.html">What's New</a></li>  
 <li><a href="../../../developers.html">Developers</a></li> 
 <li><a href="../../../contacts.html">Contacts</a></li>  
</ul>
  </div>   
  
  <!-- end .header --></div>  
  <div class="content">
 
<div style="width: 500px; height:300px; margin-left:30%; text-align: left"> <div style="padding-top:100px;">
             You are now logged out!
 <?php
echo anchor('fish/login', 'Back to login');
?> 
 </div>  </div>             
  <!-- end .content --></div> 
  <!-- end .container --></div>
</body>
</html>
 
 