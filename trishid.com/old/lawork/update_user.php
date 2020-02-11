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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


$colname_getUser = "-1";
if (isset($_GET['user_id'])) {
  $colname_getUser = $_GET['user_id'];
}
mysql_select_db($database_connector, $connector);
$query_getUser = sprintf("SELECT * FROM users WHERE user_id = %s", GetSQLValueString($colname_getUser, "int"));
$getUser = mysql_query($query_getUser, $connector) or die(mysql_error());
$row_getUser = mysql_fetch_assoc($getUser);
$totalRows_getUser = mysql_num_rows($getUser);

// *** Check if username exists
$error = array();
$MM_flag="MM_update";
if (isset($_POST[$MM_flag])) {
// Check name
if (empty($_POST['first_name']) || empty($_POST['family_name'])) {
$error['name'] = 'Please enter both first and family name';
}
// set a flag that assumes the password is OK
$pwdOK = true;
// trim leading and trailing white space
$_POST['pwd'] = trim($_POST['pwd']);
// if password field is empty, use existing password
if (empty($_POST['pwd'])) {
$_POST['pwd'] = $row_getUser['pwd'];
}
// otherwise, conduct normal checks
else {
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
  $LoginRS__query = sprintf("SELECT username FROM users WHERE username=%s AND user_id !=".$_POST['user_id'], GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_connector, $connector);
  $LoginRS=mysql_query($LoginRS__query, $connector) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
  $error['username'] = "$loginUsername is already in use. Please choose a different username.";
  }
}

if (!$error) {
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE users SET username=%s, pwd=%s, first_name=%s, family_name=%s, email=%s, admin_priv=%s WHERE user_id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['first_name'], "text"),
                       GetSQLValueString($_POST['family_name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['admin_priv'], "text"),
                       GetSQLValueString($_POST['user_id'], "int"));

  mysql_select_db($database_connector, $connector);
  $Result1 = mysql_query($updateSQL, $connector) or die(mysql_error());

  $updateGoTo = "list_user.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>updating user details</title>
</head>

<body>
<h1>Updating user details</h1>
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
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First name:</td>
      <td><input name="first_name" type="text" value="<?php echo htmlentities($row_getUser['first_name']); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Family name:</td>
      <td><input name="family_name" type="text" value="<?php echo htmlentities($row_getUser['family_name']); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input name="email" type="text" value="<?php echo htmlentities($row_getUser['email']); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input name="username" type="text" value="<?php echo htmlentities($row_getUser['username']); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right"><p>Password</p>
      <p>(leave blank if nochange):</p></td>
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
          <td><input 
		  <?php 
		  if (!$_POST &&!(strcmp($row_getUser['admin_priv'],"y"))) {echo "checked=\"checked\"";}  elseif ($_POST && !(strcmp($_POST['admin_priv'],"y"))) {echo "checked=\"checked\"";} ?> type="radio" name="admin_priv" value="y" />            
            yes</td>
        </tr>
        <tr>
          <td><input 
		  <?php 
		  if (!$_POST &&!(strcmp($row_getUser['admin_priv'],"n"))) {echo "checked=\"checked\"";}  elseif ($_POST && !(strcmp($_POST['admin_priv'],"n"))) {echo "checked=\"checked\"";} ?> type="radio" name="admin_priv" value="n" />            
            no</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input name="user_id" type="hidden" value="<?php echo $row_getUser['user_id']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($getUser);
?>
