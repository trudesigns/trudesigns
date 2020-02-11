<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connector = "localhost";
$database_connector = "stay2r5_peanut";
$username_connector = "stay2r5_butter";
$password_connector = "jelly";
$connector = mysql_pconnect($hostname_connector, $username_connector, $password_connector) or trigger_error(mysql_error(),E_USER_ERROR); 
?>