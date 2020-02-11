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

if ((isset($_POST['user_id'])) && ($_POST['user_id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM users WHERE user_id=%s",
                       GetSQLValueString($_POST['user_id'], "int"));

  mysql_select_db($database_connector, $connector);
  $Result1 = mysql_query($deleteSQL, $connector) or die(mysql_error());

  $deleteGoTo = "list_user.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delete user</title>
</head>

<body>
<h1>Delete user
</h1>
<form id="form1" name="form1" method="post" action="">
  <p class="warning">You are about to delete the following user record. This cannot be undone.</p>
  <p>Name: <?php echo $row_getUser['first_name']; ?><?php echo $row_getUser['family_name']; ?></p>
  <p>Username: <?php echo $row_getUser['username']; ?></p>
  <p>
    <input type="submit" name="button" id="button" value="delet" />
  </p>
  <input name="user_id" type="hidden" value="<?php echo $row_getUser['user_id']; ?>" />
</form>
</body>
</html>
<?php
mysql_free_result($getUser);
?>
