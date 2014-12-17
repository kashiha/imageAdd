<html>
<head><title>ログイン</title></head>
<body>
<?php
$name= $_POST['name'];
$pass = $_POST['pass'];

//DBへ接続
require "database.php";

$userSelect = $mysqli->query("select * from user
where name = '$name' and pass = '$pass'");

if (!$userSelect)
{
   print("SQLの実行に失敗しました<BR>");
   exit;
}
else {
    //データの取り出し
    $user = $userSelect->fetch_object();
}
//ログイン処理
if(isset($user->name)) {
    session_start();
    $userName = $user->name;
    $userId = $user->userId;
    $_SESSION['userName'] = $userName;
    $_SESSION['userId'] = $userId;
    print('ようこそ'.$userName.'さん<br />');

    print('<a href = "contribute.php">投稿画面へ</a>');
}
//エラー表示
else{
  print('IDもしくはPassが違います。<br />');
  print('<a href = "login.html">ログイン画面へ</a>');
}
?>

</body>
</html>
