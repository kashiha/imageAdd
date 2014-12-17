<?php
	
	$id = intval($_GET['id']);
	
	header("Content-Type: image/jpeg");

	$con = mysql_connect("server_name", "user_id", "password");
	if (!$con) {
		print("MySQLへの接続に失敗しました");
		exit;
	}
	
	if (!mysql_select_db("db_name")) {
		print("データベースへの接続に失敗しました");
		exit;
	}
	$sql = "select img_data from image where id = $id";
	
	$result = mysql_query($sql);
	if (!$result) {
		print("SQLの実行に失敗しました<BR>");
		print(mysql_errno().": ".mysql_error()."<BR>");
		exit;
	}
	
	if (mysql_num_rows($result) == 0){
		$sql = "select img_data from image where id = 1";
		
		$result = mysql_query($sql);
		if (!$result) {
			print("SQLの実行に失敗しました<BR>");
			print(mysql_errno().": ".mysql_error()."<BR>");
			exit;
		}
	}
	
	$row = mysql_fetch_row($result);
	
	echo $row[0];
	
	mysql_close($con);

?>