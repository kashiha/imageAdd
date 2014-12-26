<html>
<head><title>ログイン</title></head>
<body>
<?php
    $name= $_POST['name'];
    $pass = $_POST['pass'];

    //DBへ接続
    //require "database.php";
    $host = "localhost";

    $mysqli = new mysqli($host, 'root', 's1413109db', 'yuitf2');
    //エラーが発生したら
    if ($mysqli->connect_error){
      print("接続失敗：" . $mysqli->connect_error);
      exit();
    }
    mysql_set_charset("utf8");

    $userSelect = $mysqli->query("select * from users
    where user_name = '$name' and user_pass = '$pass'");

    if (!$userSelect)
    {
       print("SQLの実行に失敗しました<BR>");
       exit;
    }
    else {
        //データの取り出し
        $user = $userSelect->fetch_object();
        unset($userSelect);
    }
    //ログイン処理
    if(isset($user->user_id)) {
        session_start();
        $userName = $user->user_name;
        $userId = $user->user_id;
        $_SESSION['userName'] = $userName;
        $_SESSION['userId'] = $userId;
        unset($user);
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
