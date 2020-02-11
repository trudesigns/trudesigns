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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_showme = 1;
$pageNum_showme = 0;
if (isset($_GET['pageNum_showme'])) {
  $pageNum_showme = $_GET['pageNum_showme'];
}
$startRow_showme = $pageNum_showme * $maxRows_showme;

mysql_select_db($database_connector, $connector);
$query_showme = "SELECT * FROM art";
$query_limit_showme = sprintf("%s LIMIT %d, %d", $query_showme, $startRow_showme, $maxRows_showme);
$showme = mysql_query($query_limit_showme, $connector) or die(mysql_error());
$row_showme = mysql_fetch_assoc($showme);

if (isset($_GET['totalRows_showme'])) {
  $totalRows_showme = $_GET['totalRows_showme'];
} else {
  $all_showme = mysql_query($query_showme);
  $totalRows_showme = mysql_num_rows($all_showme);
}
$totalPages_showme = ceil($totalRows_showme/$maxRows_showme)-1;

$queryString_showme = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_showme") == false && 
        stristr($param, "totalRows_showme") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_showme = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_showme = sprintf("&totalRows_showme=%d%s", $totalRows_showme, $queryString_showme);
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
<title>home</title>
<link href="style_index.css" rel="stylesheet" type="text/css" />
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
        <p><a href="about.php">About</a> | <a href="event.php">Events</a> | <a href="locations.php">Locations</a> | <a href="interviews.php">Interviews</a>| <a href="gallery.php">Gallery</a></p>
      </div>
    </div>
    <!-- end #header -->
  <img src="images/logo.gif" alt="Lawork" width="167" height="73" border="0" usemap="#Map" />
<map name="Map" id="Map"><area shape="rect" coords="4,4,215,102" href="index.php" />
</map></div>
  <div id="sidebar1">
    <div id="Interviews">Interviews</div>
    <div class="green" id="view01"><a href="interviews.php"><img src="images/gen.gif" width="66" height="58" /></a><a href="interviews.php">Legal grind</a> <br/>
      <span class="style5">" I would rather get paid for my skills than get arrested. "</span></div>
    <div class="green" id="view02"><a href="interviews2.php"><img src="images/fern.gif" width="66" height="58" />One chance</a> <br/>
   	<span class="style5">" I took the opportunity and now I don't want to go back.</span></div>
<div class="green" id="view03"> <a href="interviews3.php"><img src="images/train.gif" width="66" height="58" /></a><a href="interviews3.php">For the rush</a> <br/>
    	<span class="style5"> " I can't let it go, I am like a thief in the night."</span></div>
    <div id="calendar">Events</div>
    <!--events-->
    <div id="events">
      <p class="green1"><a href="event.php">May 18: </a><span class="style1">Mother Falcon Clothing Presents: Charles Marklin</span></p>
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
    
   
  </div>
  <div id="wii">
      <img src="images/wii_can.gif" width="66" height="106" />
<span class="green">WiiSpray:</span><br />
        If blank walls tend to stir your graffiti 
        urges, the Nintendo Wii may soon 
        offer a solution that doesn't involve 
  arrests of any kind.    </div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
   <div id="footer">
     <p align="center" class="style3">copyright &copy; 2009 LAWork</p>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
<?php
mysql_free_result($showme);
?>
