<?php
	$db_json_dtls = array("server" => "localhost:3306", "user" => "root", "pass" => "", "dbname" => "dg_v6");
	$db_json_encode = json_encode($db_json_dtls,true);
	$db_json_decode = json_decode($db_json_encode,true);
?>