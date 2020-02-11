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
<title>events</title>



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
<link href="style_event.css" rel="stylesheet" type="text/css" />
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
        <p><a href="index.php">Home</a> | <a href="about.php">About</a> | <a href="event.php">Events</a> | <a href="locations.php">Locations</a> | <a href="interviews.php">Interviews</a>| <a href="gallery.php">Gallery</a></p>
      </div>
    </div>
    <!-- end #header -->
  <img src="images/logo.gif" alt="Lawork" width="167" height="73" border="0" usemap="#Map" />
<map name="Map" id="Map"><area shape="rect" coords="4,4,215,102" href="index.php" />
</map></div>
  <div id="sidebar1">
    <div id="Interviews">Interviews</div>
    <div class="green" id="view01"><a href="interviews.php"><img src="images/gen.gif" width="66" height="58" /></a><a href="interviews.php">Legal grind</a> <br/>
      <span class="style6">" I would rather get paid for my skills than get arrested. "</span></div>
    <div class="green" id="view02"><a href="interviews2.php"><img src="images/fern.gif" width="66" height="58" /></a><a href="interviews2.php">One chance</a> <br/>
   	<span class="style6">" I took the oportunity and now I dont want to go back.</span></div>
<div class="green" id="view03"> <a href="interviews3.php"><img src="images/train.gif" width="66" height="58" /></a><a href="interviews3.php">For the rush</a> <br/>
    	<span class="style6"> " I can't let it go, I am like a thief in the night."</span></div>
    <div id="calendar">Events</div>
    <!--events-->
    <div id="events">
      <p class="green"><a href="event.php">May 18</a>: <span class="style1">Mother Falcon Clothing Presents: Charles Marklin</span></p>
      <p class="green"><span class="green"><a href="event.php">May 23</a> : </span><span class="style1">Scion Presents: 1000 Days </span></p>
      <p class="green"><a href="event.php">June 4</a>: <span class="style1">ARTundresed</span></p>
      <p class="green"><a href="event.php">June 5:</a> <span class="style1">Junebug Arts Festival</span></p>
    </div>
  <!-- end #sidebar1 --></div>
  <h1>
    <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
    <span class="green">June</span></h1>
  <table width="70%" height="407" border="1" align="left" cellpadding="5" cellspacing="0" id="days" name="days">
<tr>
        <td width="15%" class="days">Mon</td>
        <td width="11%" class="days">Tue</td>
        <td width="10%" class="days">Wed</td>
        <td width="15%" class="days">Thu</td>
        <td width="16%" class="days">Fri</td>
        <td width="15%" class="days">Sat</td>
        <td width="18%" class="days">Sun</td>
      </tr>
      <tr>
        <td><div class="style3">
            <h3 align="left" class="style3">1</h3>
          <h3 align="left" class="style3">Personality: Real &amp; Imagined</h3>
        </div></td>
        <td class="style3"><div class="style3">
            <p>2</p>
        </div></td>
        <td><div class="style3">
            <p>3</p>
        </div></td>
        <td><div class="style3">
            <p align="left" class="style3">4</p>
          <p align="left" class="style3">ARTundresed</p>
        </div>
            <p align="left" class="style3">&nbsp;</p></td>
        <td class="style3"><div class="style3">
            <p align="left">5</p>
          <p align="left">Junebug Arts Festival</p>
        </div></td>
        <td><div class="style3">
            <p>6</p>
          <p>AUGURIES OF INNOCENCE </p>
        </div></td>
        <td class="style3"><div class="style3">
            <p>7</p>
          <p>Retna &amp; The Mac - Live Painting and Art Show</p>
        </div></td>
      </tr>
      <tr>
        <td class="style3"><div class="style3">
            <p>8</p>
          <p>Lilliput</p>
        </div></td>
        <td><div class="style3">
            <p>9</p>
        </div></td>
        <td><div class="style3">
            <p>10</p>
        </div></td>
        <td><div class="style3">
            <p>11 </p>
        </div></td>
        <td><div class="style3">
            <p>12</p>
          <p>RoboGames </p>
        </div></td>
        <td><div class="style3">
            <p>13</p>
          <p>RoboGames </p>
        </div></td>
        <td><div class="style3">
            <p>14</p>
          <p>RoboGames </p>
        </div></td>
      </tr>
      <tr>
        <td><div class="style3">
            <p>15</p>
        </div></td>
        <td><div class="style3">
            <p>16</p>
        </div></td>
        <td><div class="style3">
            <p>17</p>
        </div></td>
        <td><div class="style3">
            <p>18</p>
        </div></td>
        <td><div class="style3">
            <p>19</p>
          <p>DUST by William LeGoullon</p>
        </div></td>
        <td><div class="style3">
            <p>20</p>
          <p>Pasadena Chalk Fest </p>
        </div></td>
        <td><div class="style3">
            <p>21</p>
          <p>The Crow &amp; The Wolf: Benefit Masquerade</p>
        </div></td>
      </tr>
      <tr>
        <td><div class="style3">
            <p>22</p>
          <p>Centered on the Center 2009</p>
        </div></td>
        <td><div class="style3">
            <p>23</p>
          <p>Intro to Photo Encaustic</p>
        </div></td>
        <td><div class="style3">
            <p>24</p>
        </div></td>
        <td><div class="style3">
            <p>25</p>
        </div></td>
        <td><div class="style3">
            <p>26</p>
        </div></td>
        <td><div class="style3">
            <p>27</p>
        </div></td>
        <td><div class="style3">
            <p>29</p>
        </div></td>
      </tr>
      <tr>
        <td><div class="style3">
            <p>30</p>
        </div></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
</table><br class="clearfloat" />
   <div id="footer">
     <p align="center" class="style3">copyright &copy; 2009 LAWork</p>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
