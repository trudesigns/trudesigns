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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_stuff = 10;
$pageNum_stuff = 0;
if (isset($_GET['pageNum_stuff'])) {
  $pageNum_stuff = $_GET['pageNum_stuff'];
}
$startRow_stuff = $pageNum_stuff * $maxRows_stuff;

mysql_select_db($database_connector, $connector);
$query_stuff = "SELECT * FROM items";
$query_limit_stuff = sprintf("%s LIMIT %d, %d", $query_stuff, $startRow_stuff, $maxRows_stuff);
$stuff = mysql_query($query_limit_stuff, $connector) or die(mysql_error());
$row_stuff = mysql_fetch_assoc($stuff);

if (isset($_GET['totalRows_stuff'])) {
  $totalRows_stuff = $_GET['totalRows_stuff'];
} else {
  $all_stuff = mysql_query($query_stuff);
  $totalRows_stuff = mysql_num_rows($all_stuff);
}
$totalPages_stuff = ceil($totalRows_stuff/$maxRows_stuff)-1;

$queryString_stuff = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_stuff") == false && 
        stristr($param, "totalRows_stuff") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_stuff = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_stuff = sprintf("&totalRows_stuff=%d%s", $totalRows_stuff, $queryString_stuff);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>master</title>
</head>

<body>
<table border="1" align="center">
  <tr>
    <td>name</td>
    <td>price</td>
    <td>mini_desc</td>
    <td>image_loc</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_stuff['name']; ?>&nbsp; </td>
      <td><?php echo $row_stuff['price']; ?>&nbsp; </td>
      <td><?php echo $row_stuff['mini_desc']; ?>&nbsp; </td>
      <td><a href="detail.php?recordID=<?php echo $row_stuff['id']; ?>"><img src="<?php echo $row_stuff['image_loc']; ?>" /></a></td>
    </tr>
    <?php } while ($row_stuff = mysql_fetch_assoc($stuff)); ?>
</table>
<br />
<table border="0">
  <tr>
    <td><?php if ($pageNum_stuff > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_stuff=%d%s", $currentPage, 0, $queryString_stuff); ?>">First</a>
          <?php } // Show if not first page ?>
    </td>
    <td><?php if ($pageNum_stuff > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_stuff=%d%s", $currentPage, max(0, $pageNum_stuff - 1), $queryString_stuff); ?>">Previous</a>
          <?php } // Show if not first page ?>
    </td>
    <td><?php if ($pageNum_stuff < $totalPages_stuff) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_stuff=%d%s", $currentPage, min($totalPages_stuff, $pageNum_stuff + 1), $queryString_stuff); ?>">Next</a>
          <?php } // Show if not last page ?>
    </td>
    <td><?php if ($pageNum_stuff < $totalPages_stuff) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_stuff=%d%s", $currentPage, $totalPages_stuff, $queryString_stuff); ?>">Last</a>
          <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
Records <?php echo ($startRow_stuff + 1) ?> to <?php echo min($startRow_stuff + $maxRows_stuff, $totalRows_stuff) ?> of <?php echo $totalRows_stuff ?>
</body>
</html>
<?php
mysql_free_result($stuff);
?>
