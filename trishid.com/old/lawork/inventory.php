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

$maxRows_showme = 10;
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>inventory</title>
</head>

<body>
<table border="1">
  <tr>
    <td>art_id</td>
    <td>title</td>
    <td>artist</td>
    <td>genre</td>
    <td>description</td>
    <td>image</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_showme['art_id']; ?></td>
      <td><?php echo $row_showme['title']; ?></td>
      <td><?php echo $row_showme['artist']; ?></td>
      <td><?php echo $row_showme['genre']; ?></td>
      <td><?php echo $row_showme['description']; ?></td>
      <td><?php echo $row_showme['image']; ?></td>
    </tr>
    <?php } while ($row_showme = mysql_fetch_assoc($showme)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($showme);
?>
