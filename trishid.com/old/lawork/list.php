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

$maxRows_artlist = 10;
$pageNum_artlist = 0;
if (isset($_GET['pageNum_artlist'])) {
  $pageNum_artlist = $_GET['pageNum_artlist'];
}
$startRow_artlist = $pageNum_artlist * $maxRows_artlist;

mysql_select_db($database_connector, $connector);
$query_artlist = "SELECT * FROM art";
$query_limit_artlist = sprintf("%s LIMIT %d, %d", $query_artlist, $startRow_artlist, $maxRows_artlist);
$artlist = mysql_query($query_limit_artlist, $connector) or die(mysql_error());
$row_artlist = mysql_fetch_assoc($artlist);

if (isset($_GET['totalRows_artlist'])) {
  $totalRows_artlist = $_GET['totalRows_artlist'];
} else {
  $all_artlist = mysql_query($query_artlist);
  $totalRows_artlist = mysql_num_rows($all_artlist);
}
$totalPages_artlist = ceil($totalRows_artlist/$maxRows_artlist)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<p>here what's in the artist database table</p>
<p>&nbsp; </p>

<table border="1">
  <tr>
    <td>art_id</td>
    <td>artist</td>
    <td>genre</td>
    <td>title</td>
    <td>description</td>
    <td>image</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_artlist['art_id']; ?></td>
      <td><?php echo $row_artlist['artist']; ?></td>
      <td><?php echo $row_artlist['genre']; ?></td>
      <td><?php echo $row_artlist['title']; ?></td>
      <td><?php echo $row_artlist['description']; ?></td>
      <td><?php echo $row_artlist['image']; ?></td>
    </tr>
    <?php } while ($row_artlist = mysql_fetch_assoc($artlist)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($artlist);
?>
