<html>
<head>
	<title>Preview</title>
</head>

<body>
<h1>プレビュー画面</h1>
<?php
	session_start();
	//session変数に保存したプレビューデータの表示
	print($_SESSION['preview']);
	//previewデータの破棄
	//unset($_SESSION['preview']);
?>
</body>
</html>