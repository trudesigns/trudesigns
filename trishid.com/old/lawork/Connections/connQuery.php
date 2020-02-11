<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connQuery = "LocalHost";
$database_connQuery = "stay2r5_peanut";
$username_connQuery = "stay2r5_butter";
$password_connQuery = "jelly";
$connQuery = mysql_pconnect($hostname_connQuery, $username_connQuery, $password_connQuery) or trigger_error(mysql_error(),E_USER_ERROR); 
?>