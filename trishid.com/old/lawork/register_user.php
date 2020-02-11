<?php require_once('Connections/connector.php'); ?>
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

// *** Check if username exists
$error = array();
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
// Check name
if (empty($_POST['first_name']) || empty($_POST['family_name'])) {
$error['name'] = 'Please enter both first and family name';
}
// set a flag that assumes the password is OK
$pwdOK = true;
// trim leading and trailing white space
$_POST['pwd'] = trim($_POST['pwd']);
// if less than 6 characters, creat alert and set flag to false
if (strlen($_POST['pwd']) < 6) {
  $error['pwd_length'] = 'Your password must be at least 6 characters';
  $pwdOK = false;
  }
  // if no match, creat alert and set flag to false
  if ($_POST['pwd'] != trim($_POST['conf_pwd'])) {
    $error['pwd'] = 'Your password don\'t match';
	$pwdOK = false;
	}
	// if password OK, encrypt it
	if ($pwdOK) {
	$_POST['pwd'] = sha1($_POST['pwd']);
	}
	// regex to identify illegal characters in email address
$checkEmail = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
if (!preg_match($checkEmail, trim($_POST['email']))) {
  $error['email'] = 'Please enter a valid email address';
  }
//check username
$_POST['username'] = trim($_POST['username']);
  $loginUsername = $_POST['username'];
  if (strlen($loginUsername) < 6) {
    $error['length'] = 'Please select a username that contains at least 6 characters';
	}
  $LoginRS__query = sprintf("SELECT username FROM users WHERE username=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_connector, $connector);
  $LoginRS=mysql_query($LoginRS__query, $connector) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
  $error['username'] = "$loginUsername is already in use. Please choose a different username.";
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if (!$error) {
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO users (first_name, family_name, email, username, pwd, admin_priv) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['first_name'], "text"),
                       GetSQLValueString($_POST['family_name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['admin_priv'], "text"));

  mysql_select_db($database_connector, $connector);
  $Result1 = mysql_query($insertSQL, $connector) or die(mysql_error());
}
// if the record has been inserted, clear the $_POST array $_POST = array();
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>register user</title>

<!--[if IE]>
<style type="text/css"> 
/* place css fixes for all versions of IE in this conditional comment */
.twoColElsLtHdr #sidebar1 { padding-top: 30px; }
.twoColElsLtHdr #mainContent { zoom: 1; padding-top: 15px; }
/* the above proprietary zoom property gives IE the hasLayout it needs to avoid several bugs */
</style>
<![endif]-->
<script src="file:///I|/web/lawork_03/lawork/Scripts/AC_ActiveX.js" type="text/javascript"></script>
<script src="file:///I|/web/lawork_03/lawork/Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
<link href="style_regester.css" rel="stylesheet" type="text/css" />
</head>
<body class="twoColElsLtHdr">

<div id="container">
  <div id="header">
    <div class="green" id="nav">
      <div align="right">
        <p><a href="index.php">Home</a> |<a href="about.php">About</a> | <a href="event.php">Events</a> | <a href="locations.php">Locations</a> | <a href="interviews.php">Interviews</a>| <a href="gallery.php">Gallery</a></p>
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
    <div class="green" id="view02"><a href="interviews2.php"><img src="images/fern.gif" width="66" height="58" /></a><a href="interviews2.php">One chance</a> <br/>
   	<span class="style5">" I took the opportunity and now I don't want to go back.</span></div>
<div class="green" id="view03"> <a href="interviews3.php"><img src="images/train.gif" width="66" height="58" /></a><a href="interviews3.php">For the rush</a> <br/>
    	<span class="style5"> " I can't let it go, I am like a thief in the night."</span></div>
    <div id="calendar">Events</div>
    <!--events-->
    <div id="events">
      <p class="green"><a href="event.php">May 18:</a> <span class="style1">Mother Falcon Clothing Presents: Charles Marklin</span></p>
      <p class="green"><a href="event.php">May 23 </a>: <span class="style1">Scion Presents: 1000 Days </span></p>
      <p class="green"><a href="event.php">June 4:</a> <span class="style1">ARTundresed</span></p>
      <p class="green"><a href="event.php">June 5</a>: <span class="style1">Junebug Arts Festival</span></p>
    </div>
  <!-- end #sidebar1 --></div>
  <div id="mainContent">
    <p>&nbsp;
      
<h1 class="green">Register user</h1>
<?php
if ($error) {
 echo '<ul>';
 foreach ($error as $alert) {
   echo "<li class='warning'>$alert</li>\n";
   }
   echo '</ul>';
   // remove escape characters from POST array
if (get_magic_quotes_gpc()) {
  function stripslashes_deep($value) {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
    }
  $_POST = array_map('stripslashes_deep', $_POST);
  }
   }
   ?>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" class="style3" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First name:</td>
      <td><input type="text" name="first_name" value="<?php if (isset($_POST['first_name'])) {
echo htmlentities($_POST['first_name']);} ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Family name:</td>
      <td><input type="text" name="family_name" value="<?php if (isset($_POST['family_name'])) {
echo htmlentities($_POST['family_name']);} ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input type="text" name="email" value="<?php if (isset($_POST['email'])) {
echo htmlentities($_POST['email']);} ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input type="text" name="username" value="<?php if (isset($_POST['username'])) {
echo htmlentities($_POST['username']);} ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input type="password" name="pwd" value="" size="32" /></td>
     </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Confirm password:</td>
        <td valign="baseline"><input type="password" name="conf_pwd" id="conf_pwd" /></td>
      </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Administrator:</td>
      <td valign="baseline"><table>
        <tr>
          <td><input <?php if ($_POST && !(strcmp($_POST['admin_priv'],"y"))) {echo "checked=\"checked\"";} ?> type="radio" name="admin_priv" value="y" />
            yes</td>
        </tr>
        <tr>
          <td><input <?php if (($_POST && !(strcmp($_POST['admin_priv'],"n")))|| !$_POST) {echo "checked=\"checked\"";} ?> type="radio" name="admin_priv" value="n" />            
            no</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="style3" value="Insert record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
  <a href="index.php" class="green">login page<br class="clearfloat" />
  </a>
</form>
<h2>&nbsp;</h2>

      
    </p>
   
    <p>

           
      <!--end video-->
      <!-- wii can -->
    </p>
    <p>
      <!-- end #mainContent -->
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp; </p>
  </div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<div id="footer">
     <p align="center" class="style3">copyright &copy; 2009 LAWork</p>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>


