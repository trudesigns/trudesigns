<?php require_once('Connections/connector.php'); ?>
<?php
if (isset($_POST['pwd'])) {$_POST['pwd'] = sha1($_POST['pwd']); }
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['pwd'];
  $MM_fldUserAuthorization = "admin_priv";
  $MM_redirectLoginSuccess = "success.php";
  $MM_redirectLoginFailed = "loginfail.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_connector, $connector);
  	
  $LoginRS__query=sprintf("SELECT username, pwd, admin_priv FROM users WHERE username=%s AND pwd=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $connector) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'admin_priv');
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>lawork</title>


<style type="text/css"> 
<!-- 
body  {
	font: 100% Verdana, Arial, Helvetica, sans-serif;
	margin: 0; /* it's good practice to zero the margin and padding of the body element to account for differing browser defaults */
	padding: 0;
	text-align: center; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
	background-color: #CCCCCC;
}

/* Tips for Elastic layouts 
1. Since the elastic layouts overall sizing is based on the user's default fonts size, they are more unpredictable. Used correctly, they are also more accessible for those that need larger fonts size since the line length remains proportionate.
2. Sizing of divs in this layout are based on the 100% font size in the body element. If you decrease the text size overall by using a font-size: 80% on the body element or the #container, remember that the entire layout will downsize proportionately. You may want to increase the widths of the various divs to compensate for this.
3. If font sizing is changed in differing amounts on each div instead of on the overall design (ie: #sidebar1 is given a 70% font size and #mainContent is given an 85% font size), this will proportionately change each of the divs overall size. You may want to adjust based on your final font sizing.
*/
.twoColElsLtHdr #container {
	width: 780px; /* the auto margins (in conjunction with a width) center the page */
	border: 1px solid #000000;
	text-align: left; /* this overrides the text-align: center on the body element. */
	height: 650px;
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
	background-color: #FFFFFF;
} 
#nav {
	height: 20px;
	width: 780px;
	position: absolute;
	top: 80px;
	font-variant: small-caps;
	font-size: 12px;
	float: none;
}
.twoColElsLtHdr #header {
	background-color: #000000;
	height: 110px;
	padding-top: 0;
	padding-right: 10px;
	padding-bottom: 0;
	background-repeat: no-repeat;
} 
#header img {
	padding-left: 20px;
}
#login {
	background-color: #000000;
	height: 105px;
	width: 215px;
	position: absolute;
	top: -10px;
	margin-left: 565px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	float: none;
}
#video {
}

/* Tips for sidebar1:
1. Be aware that if you set a font-size value on this div, the overall width of the div will be adjusted accordingly.
2. Since we are working in ems, it's best not to use padding on the sidebar itself. It will be added to the width for standards compliant browsers creating an unknown actual width. 
3. Space between the side of the div and the elements within it can be created by placing a left and right margin on those elements as seen in the ".twoColElsLtHdr #sidebar1 p" rule.
*/
.twoColElsLtHdr #sidebar1 {
	float: left;
	width: 10em; /* top and bottom padding create visual space within this div */
	background-color: #999999;
	background-image: url(images/leftsidebar_2.gif);
	height: 500px;
	background-repeat: no-repeat;
	padding-top: 15px;
	padding-right: 0;
	padding-bottom: 15px;
	padding-left: 0;
	border-right-width: 10px;
	border-right-style: solid;
	border-right-color: #FFFFFF;
}
#images {
	width: 340px;
	position: relative;
	top: -185px;
	height: 300px;
	background-color: #8BC53F;
	left: 430px;
}
.twoColElsLtHdr #footer {
	height: 35px;
	width: 780px;
	background-color: #000000;
	font-size: 12px;
	position: absolute;
	top: 630px;
} 

/* Miscellaneous classes for reuse */
.fltrt { /* this class can be used to float an element right in your page. The floated element must precede the element it should be next to on the page. */
	float: right;
	margin-left: 8px;
}
.fltlft { /* this class can be used to float an element left in your page */
	float: left;
	margin-right: 8px;
}
.clearfloat { /* this class should be placed on a div or break element and should be the final element before the close of a container that should fully contain a float */
	clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;
}
#Interviews {
	background-color: #CCCCCC;
	font-family: Arial, Helvetica, sans-serif;
	width: 160px;
	font-size: 18px;
	height: 25px;
	font-weight: bold;
}
#wii {
	font-family: Arial, Helvetica, sans-serif;
	width: 195px;
	font-size: 12px;
	padding-left: 4px;
	margin-left: 9em;
	top: 500px;
	height: 128px;
	position: absolute;
	left: 170px;
}
#newsletter {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 18px;
	height: 25px;
	width: 187px;
	background-color: #CCCCCC;
	margin-top: 15px;
	font-weight: bold;
}
#view01 {
	background-color: #CCCCCC;
	width: 150px;
	height: 62px;
	margin-left: 3px;
	margin-top: 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	border: thin solid #666666;
}
#view02 {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #CCCCCC;
	height: 62px;
	width: 150px;
	margin-top: 15px;
	margin-left: 3px;
	font-size: 12px;
	border: thin solid #666666;
}
#view03 {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #CCCCCC;
	height: 62px;
	width: 150px;
	margin-top: 15px;
	margin-left: 3px;
	font-size: 12px;
	border: thin solid #666666;
}
#view01 img {
	float: left;
	padding-right: 5px;
}
#view02 img {
	padding-right: 5px;
	float: left;
	
}
.green {
	font-weight: bold;
	color: #8BC53F;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
#wii img {
	float: left;
	padding-right: 5px;
}
#view03 img {
	float: left;
	padding-right: 5px;
}
#events {
	background-color: #CCCCCC;
	width: 145px;
	height: 180px;
	margin-top: 10px;
	margin-left: 3px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	border: thin solid #666666;
	padding-left: 5px;
}
#calendar {
	background-color: #CCCCCC;
	height: 25px;
	width: 160px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 18px;
	font-weight: bold;
	margin-top: 10px;
}
.style1 {
	font-size: 10px;
	color: #000000;
}
#form1 {
	font-family: Arial, Helvetica, sans-serif;
	color: 8bc53f;
	background-color: #000000;
	font-size: 12px;
	height: 45px;
	width: 250px;
	margin-left: 530px;
	position: absolute;
	top: 0px;
}
#doLogin {
	background-color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	color: 8bc53f;
	position: absolute;
	float: left;
	left: 158px;
	top: 80px;
}
a:link {
	text-decoration: none;
	color: #8BC53F;
}
a:visited {
	text-decoration: none;
	color: #CCCCCC;
}
a:hover {
	text-decoration: underline;
	color: #CCCCCC;
}
a:active {
	text-decoration: none;
	color: #8BC53F;
}
.style2 {font-size: 10px}
.style3 {font-weight: bold; color: #8BC53F; border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none; font-size: 10px; }
--> 
</style>
<!--[if IE]>
<style type="text/css"> 
/* place css fixes for all versions of IE in this conditional comment */
.twoColElsLtHdr #sidebar1 { padding-top: 30px; }
.twoColElsLtHdr #mainContent { zoom: 1; padding-top: 15px; }
/* the above proprietary zoom property gives IE the hasLayout it needs to avoid several bugs */
</style>
<![endif]-->
<script src="Scripts/AC_ActiveX.js" type="text/javascript"></script>
<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
</head>

<body class="twoColElsLtHdr">

<div id="container">
  <div id="header">
    <form id="login" name="login" method="POST" action="<?php echo $loginFormAction; ?>">
      <p>
        <label for="username"><span class="style3"> Username:</span></label>
        <span class="green">
        <input type="text" name="username" id="username" />
      </span></p>
      <p> <span class="green">
        <label for="pwd"><span class="style2">Password:</span></label>
        </span>
          <input name="pwd" type="password" />
          <a href="register_user.php"></a></p>
      <p align="center" class="style3"><a href="register_user.php">Register</a></p>
        <input name="doLogin" type="submit" class="style3" id="doLogin" value="Log in" />
      </p>
    </form>
    <div class="green" id="nav">
      <div align="right">
        <p><a href="home.php">About</a> | Events | Locations | Interviews| Gallery</p>
      </div>
    </div>
    <!-- end #header -->
  <img src="images/logo.gif" alt="Lawork" width="167" height="73" border="0" usemap="#Map" />
<map name="Map" id="Map"><area shape="rect" coords="4,4,215,102" href="index.php" />
</map></div>
  <div id="sidebar1">
    <div id="Interviews">Interviews</div>
    <div class="green" id="view01"><img src="images/gen.gif" width="66" height="58" />Legal grind <br/>
      <span class="style1">" I would rather get paid for my skills than get arrested. "</span></div>
    <div class="green" id="view02"><img src="images/fern.gif" width="66" height="58" />One chance <br/>
   	<span class="style1">" I took the oportunity and now I dont want to go back.</span></div>
    <div class="green" id="view03"> <img src="images/train.gif" width="66" height="58" />For the rush <br/>
    	<span class="style1"> " I can't let it go, I am like a thief in the night."</span></div>
    <div id="calendar">Events</div>
    <!--events-->
    <div id="events">
      <p class="green">May 18: <span class="style1">Mother Falcon Clothing Presents: Charles Marklin</span></p>
      <span class="green">May 23 : </span><span class="style1">Scion Presents: 1000 Days </span></div>
  <!-- end #sidebar1 --></div>
  <div id="mainContent">
    <p>
      <script type="text/javascript">
AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0','width','580','height','180','src','source/flash_ad','quality','high','pluginspage','http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash','movie','source/flash_ad' ); //end AC code
      </script>
      <noscript>
      <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="580" height="180">
        <param name="movie" value="source/flash_ad.swf" />
        <param name="quality" value="high" />
        <embed src="source/flash_ad.swf" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="580" height="180"></embed>
      </object>
      </noscript>
    </p>
   
    <p>
<script type="text/javascript">
AC_AX_RunContent( 'name','video','width','225','height','169','src','http://vimeo.com/moogaloop.swf?clip_id=3941280&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=8BC53F&fullscreen=1','type','application/x-shockwave-flash','allowfullscreen','true','allowscriptaccess','always','movie','http://vimeo.com/moogaloop.swf?clip_id=3941280&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=8BC53F&fullscreen=1' ); //end AC code
           </script>
           <noscript>
                <object name="video" width="225" height="169">
                  <param name="allowfullscreen" value="true" />
                  <param name="allowscriptaccess" value="always" />
                  <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=3941280&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=8BC53F&amp;fullscreen=1" />
                  <embed src="http://vimeo.com/moogaloop.swf?clip_id=3941280&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=8BC53F&amp;fullscreen=1" width="225" height="169" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" name="video"></embed>
                </object>
                </noscript>
      <!--end video-->
      <!-- wii can -->
        </p>
    <div id="images">
      <p>THis can be a title</p>
      <p>A picture can go here with CSS control</p>
    </div>
    
    <div id="wii"><img src="images/wii_can.gif" width="66" height="123" /><span class="green">WiiSpray: legal Graffiti, </span><br />
      If blank walls tend to stir your graffiti 
      urges, the Nintendo Wii may soon 
      offer a solution that doesn't involve 
      arrests of any kind. </div>
    <p>
      <!-- end #mainContent -->
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp; </p>
  </div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
   <div id="footer">
     <p align="center" class="style3">copyright &copy; 2009 LAWork</p>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
