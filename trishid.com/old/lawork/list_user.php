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

$maxRows_listUsers = 10;
$pageNum_listUsers = 0;
if (isset($_GET['pageNum_listUsers'])) {
  $pageNum_listUsers = $_GET['pageNum_listUsers'];
}
$startRow_listUsers = $pageNum_listUsers * $maxRows_listUsers;

mysql_select_db($database_connector, $connector);
$query_listUsers = "SELECT * FROM users ORDER BY family_name ASC";
$query_limit_listUsers = sprintf("%s LIMIT %d, %d", $query_listUsers, $startRow_listUsers, $maxRows_listUsers);
$listUsers = mysql_query($query_limit_listUsers, $connector) or die(mysql_error());
$row_listUsers = mysql_fetch_assoc($listUsers);

if (isset($_GET['totalRows_listUsers'])) {
  $totalRows_listUsers = $_GET['totalRows_listUsers'];
} else {
  $all_listUsers = mysql_query($query_listUsers);
  $totalRows_listUsers = mysql_num_rows($all_listUsers);
}
$totalPages_listUsers = ceil($totalRows_listUsers/$maxRows_listUsers)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registered user</title>
</head>

<body>
<h1>Regitered user</h1>
<table width="50%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>Name</td>
    <td>Username</td>
    <td>Admin</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_listUsers['first_name']; ?><?php echo $row_listUsers['family_name']; ?> </td>
      <td><?php echo $row_listUsers['username']; ?></td>
      <td><?php echo $row_listUsers['admin_priv']; ?></td>
      <td><a href="update_user.php?<?php echo $row_listUsers['user_id']; ?>=">EDIT</a></td>
      <td><a href="delete_user.php?<?php echo $row_listUsers['user_id']; ?>=">DELET</a></td>
    </tr>
    <?php } while ($row_listUsers = mysql_fetch_assoc($listUsers)); ?>
</table>
<h1>&nbsp; </h1>
</body>
</html>
<?php
mysql_free_result($listUsers);
?>