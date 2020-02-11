<?php require_once('Connections/connQuery.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "y,n";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
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

$colname_getName = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_getName = $_SESSION['MM_Username'];
}
mysql_select_db($database_connQuery, $connQuery);
$query_getName = sprintf("SELECT first_name, family_name FROM users WHERE username = %s", GetSQLValueString($colname_getName, "text"));
$getName = mysql_query($query_getName, $connQuery) or die(mysql_error());
$row_getName = mysql_fetch_assoc($getName);
$totalRows_getName = mysql_num_rows($getName);
$_SESSION['first_name'] = $row_getName['first_name'];
$_SESSION['family_name'] = $row_getName['family_name'];
$_SESSION['full_name'] = $row_getName['first_name'].' '.$row_getName['family_name'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Members only</title>
<style type="text/css">
<!--
.greeting {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #8BC53F;
	float: none;
	width: 215px;
	position: absolute;
	background-color: #000000;
	height: 65px;
	top: -10px;
	margin-left: 565px;
}
a {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
a:link {
	color: #8BC53F;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #8BC53F;
}
a:hover {
	text-decoration: underline;
	color: #999999;
}
a:active {
	text-decoration: none;
	color: #660066;
}
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
	width: 780px;  /* this width will create a container that will fit in an 800px browser window if text is left at browser default font sizes */
	background: #FFFFFF; /* the auto margins (in conjunction with a width) center the page */
	border: 1px solid #000000;
	text-align: left; /* this overrides the text-align: center on the body element. */
	height: 650px;
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
} 
#nav {
	height: 20px;
	width: 780px;
	position: absolute;
	top: 78px;
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
#video {
}

/* Tips for sidebar1:
1. Be aware that if you set a font-size value on this div, the overall width of the div will be adjusted accordingly.
2. Since we are working in ems, it's best not to use padding on the sidebar itself. It will be added to the width for standards compliant browsers creating an unknown actual width. 
3. Space between the side of the div and the elements within it can be created by placing a left and right margin on those elements as seen in the ".twoColElsLtHdr #sidebar1 p" rule.
*/
.twoColElsLtHdr #sidebar1 {
	float: left;
	width: 12em; /* top and bottom padding create visual space within this div */
	background-color: #999999;
	background-image: url(images/leftsidebar_2.gif);
	height: 500px;
	background-repeat: no-repeat;
	padding-top: 15px;
	padding-right: 0;
	padding-bottom: 15px;
	padding-left: 0;
	border-right-width: medium;
	border-right-style: solid;
	border-right-color: #FFFFFF;
}

/* Tips for mainContent:
1. If you give this #mainContent div a font-size value different than the #sidebar1 div, the margins of the #mainContent div will be based on its font-size and the width of the #sidebar1 div will be based on its font-size. You may wish to adjust the values of these divs.
2. The space between the mainContent and sidebar1 is created with the left margin on the mainContent div.  No matter how much content the sidebar1 div contains, the column space will remain. You can remove this left margin if you want the #mainContent div's text to fill the #sidebar1 space when the content in #sidebar1 ends.
3. To avoid float drop, you may need to test to determine the approximate maximum image/element size since this layout is based on the user's font sizing combined with the values you set. However, if the user has their browser font size set lower than normal, less space will be available in the #mainContent div than you may see on testing.
4. In the Internet Explorer Conditional Comment below, the zoom property is used to give the mainContent "hasLayout." This avoids several IE-specific bugs that may occur.
*/
.twoColElsLtHdr #mainContent {
	margin-top: -2px;
	margin-right: 0em;
	margin-left: 5em;
	height: -5px;
} 
#images {
	width: 340px;
	position: relative;
	top: -170px;
	height: 300px;
	float: right;
}
.twoColElsLtHdr #footer {
	height: 25px;
	width: 780px;
	background-color: #000000;
	font-size: 12px;
	position: absolute;
	top: 630px;
} 
.twoColElsLtHdr #footer p {
	margin: 0; /* zeroing the margins of the first element in the footer will avoid the possibility of margin collapse - a space between divs */
	padding: 10px 0; /* padding on this element will create space, just as the the margin would have, without the margin collapse issue */
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
	width: 187px;
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
	left: 515px;
	border-top-width: medium;
	border-top-style: solid;
	border-top-color: #FFFFFF;
}
#view01 img {
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
	width: 160px;
	height: 62px;
	margin-left: 15px;
	margin-top: 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	border: thin solid #666666;
}
#view02 {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #CCCCCC;
	height: 62px;
	width: 160px;
	margin-top: 15px;
	margin-left: 15px;
	font-size: 12px;
	border: thin solid #666666;
}
#view03 {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #CCCCCC;
	height: 62px;
	width: 160px;
	margin-top: 15px;
	margin-left: 15px;
	font-size: 12px;
	border: thin solid #666666;
}
#view01 img {
	float: left;
	padding-right: 5px;
	border: thin solid #CCCCCC;
}
#view02 img {
}
#view02 img {
	padding-right: 5px;
	float: left;
	border: thin solid #CCCCCC;	
}
#view03 img {
}
.green {
	font-weight: bold;
	color: #8BC53F;
}
#wii img {
	float: left;
	padding-right: 5px;
}
#view03 img {
	float: left;
	padding-right: 5px;
	border: thin solid #CCCCCC;
}
#events {
	background-color: #CCCCCC;
	width: 170px;
	height: 180px;
	margin-top: 10px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	border: thin solid #666666;
	padding-left: 5px;
	margin-left: 5px;
	line-height: 10px;
}
#calendar {
	background-color: #CCCCCC;
	height: 25px;
	width: 187px;
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
.style4 {font-size: 9px; color: #000000; }
.green1 {font-weight: bold;
	color: #8BC53F;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
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
<link href="style_success.css" rel="stylesheet" type="text/css" />
</head>

<body class="twoColElsLtHdr">

<div id="container">
  <div id="header">
<div class="greeting">
  <p align="right" class="green">Hello, <?php echo $_SESSION['full_name']; ?>.You're in!</p>
  <p align="right">&nbsp;<a href="<?php echo $logoutAction ?>">Log out</a></p>
</div>
<?php
mysql_free_result($getName);
?>

    <div class="green" id="nav">
      <div align="right">
        <p><a href="index.php">Home</a> | <a href="about.php">About</a> | <a href="event.php">Events</a> | <a href="locations.php">Locations</a> | <a href="interviews.php">Interviews</a>| <a href="gallery.php">Gallery</a></p>
      </div>
    </div>
    <!-- end #header -->
  <img src="images/logo.gif" alt="Lawork" width="167" height="73" border="0" usemap="#Map" />
<map name="Map" id="Map"><area shape="rect" coords="4,3,213,79" href="index.php" />
</map></div>
  <div id="sidebar1">
    <div id="Interviews">Interviews</div>
    <div class="green" id="view01"><a href="interviews.php"><img src="images/gen.gif" width="66" height="58" /></a><a href="interviews.php">Legal grind</a> <br/>
      <span class="style4">" I would rather get paid for my skills than get arrested. "</span></div>
    <div class="green" id="view02"><a href="interviews2.php"><img src="images/fern.gif" width="66" height="58" /></a><a href="interviews2.php">One chance </a><br/>
   	<span class="style4">" I took the opportunity and now I don't want to go back.</span></div>
<div class="green" id="view03"> <a href="interviews3.php"><img src="images/train.gif" width="66" height="58" /></a><a href="interviews3.php">For the rush</a> <br/>
    	<span class="style4"> " I can't let it go, I am like a thief in the night."</span></div>
    <div id="calendar">Events</div>
    <!--events-->
    <div id="events">
      <p class="green1"><a href="event.php">May 18:</a> <span class="style1">Mother Falcon Clothing Presents: Charles Marklin</span></p>
      <p class="green1"><a href="event.php">May 23 :</a> <span class="style1">Scion Presents: 1000 Days </span></p>
      <p class="green1"><a href="event.php">June 4:</a> <span class="style1">ARTundresed</span></p>
      <p class="green1"><a href="event.php">June 5:</a> <span class="style1">Junebug Arts Festival</span></p>
      <p class="green">&nbsp;</p>
    </div>
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
   
   <!--video-->
   <script type="text/javascript">
AC_AX_RunContent( 'name','video','width','225','height','169','src','http://vimeo.com/moogaloop.swf?clip_id=3941280&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=8BC53F&fullscreen=1','type','application/x-shockwave-flash','allowfullscreen','true','allowscriptaccess','always','movie','http://vimeo.com/moogaloop.swf?clip_id=3941280&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=8BC53F&fullscreen=1' ); //end AC code
</script><noscript><object name="video" width="225" height="169">
     <param name="allowfullscreen" value="true" />
     <param name="allowscriptaccess" value="always" />
     <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=3941280&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=8BC53F&amp;fullscreen=1" />
     <embed src="http://vimeo.com/moogaloop.swf?clip_id=3941280&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=8BC53F&amp;fullscreen=1" width="225" height="169" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" name="video"></embed>
   </object></noscript>
   <!--end video-->
   <!-- wii can -->

    <div id="images">
      <table height="286" border="0" id="pix">
        
        <tr>
          <td height="92" align="center">
          <a href="detail.php?recordID=<?php echo $row_showme['art_id']; ?>"><img src="images/2rustyle.jpg" alt="2rustyles" width="95" height="95" border="0" /></a>
          <a href="detail.php?recordID=<?php echo $row_showme['art_id']; ?>"></a><a href="detail.php?recordID=<?php echo $row_showme['art_id']; ?>"></a></td>
          
          <td align="center">
          <a href="detail.php?recordID=<?php echo $row_showme['art_id']; ?>"><img src="images/4much.jpg" alt="4much" width="95" height="95" border="0" /></a></td>  
          <td align="center">
          <a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=6"><img src="images/b2.jpg" alt="b2" width="95" height="95" border="0" /></a></td>
        </tr>
        <tr>
          <td height="87" align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=7"><img src="images/bang.jpg" alt="bang" width="95" height="95" border="0" /></a></td>
          <td align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=8"><img src="images/boa.jpg" alt="boa" width="95" height="95" border="0" /></a></td>
          <td align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=9"><img src="images/chaos.jpg" alt="chaos" width="95" height="95" border="0" /></a></td>
        </tr>
        <tr>
          <td height="95" align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=10"><img src="images/trish.jpg" alt="trish" width="95" height="95" border="0" /></a></td>
          <td align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=13"><img src="images/mask.jpg" alt="mask" width="95" height="95" border="0" /></a></td>
          <td align="center"><a href="http://www.stay2ru.com/mm2213/final/detail.php?recordID=12"><img src="images/warp1.jpg" alt="warp1" width="95" height="95" border="0" /></a></td>
        </tr>
      </table>
     
 
    </div>
    
    <div id="wii">
      <img src="images/wii_can.gif" width="66" height="109" />
<span class="green">WiiSpray:</span><br />
        If blank walls tend to stir your graffiti 
        urges, the Nintendo Wii may soon 
        offer a solution that doesn't involve 
        arrests of any kind.    </div>
    
   
  <!-- end #mainContent --></div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
   <div id="footer">
    <p align="center" class="green">copyright &copy; 2009 LAWork</p>
    <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
