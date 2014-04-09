<?php

/*
 * Makes the connection to the server and selects the right database
 * 
 */

include_once 'config.php';

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name);

?>