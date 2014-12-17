<html>
<head>
	<title>ログアウト</title>
</head>
<body>
	<?php
	session_start();
	if(!(isset($_SESSION['userId'])) || $_SESSION['userId'] == ""){
		header('Location: http://localhost/yui/login.html', true, 301);
		exit();
	}
	$_SESSION = array();
	
?>
ログアウトしました。<br />
<a href ="login.html">ログイン画面へ</a>
</body>
</html>