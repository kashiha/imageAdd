<html>
<head>
<title>投稿ページ　新規登録</title>
</head>

<body>
<h1>新規登録 </h1>
<?php
if ($_POST["submit"]!="") {
    $name= $_POST['name'];
    $pass = $_POST['pass'];
    $passConf= $_POST['passConf'];
    $introName = $_POST['introName'];
    $introPass = $_POST['introPass'];

    require "database.php";

    //紹介者照会
    $introductionUserConfirm = $mysqli->query("select count(userId) as Conf from user
    where name = '$introName' and pass = '$introPass'");
    /*
    $sql = "select count(userId) as Conf from user
    where name = '$introName' and pass = '$introPass'";
    $result = mysql_query($sql);
    */
    
    //ユーザ名およびpassの長さに関するエラー
    if( !(4 <= mb_strlen($name) && mb_strlen($name) <= 8)) {
        printf("ユーザ名は4～8文字で登録してください。<br />");
        exit;
    }
    if(! (8 <= strlen($name) && strlen($name) <= 16)) {
        print("パスワードは8～16文字で登録してください。<br />");
    }
    
    //紹介者が存在しない場合
    if ($introductionUserConfirm) {
        print("紹介者が存在しません。もう一度入力して下さい<BR>");
        //print(mysql_errno().": ".mysql_error()."<BR>");
        exit;
    }

    if ($pass == $passConf){
        //新規ユーザ登録
        $addUser = $mysqli->query("INSERT INTO user(name , pass)
        values('$name', '$pass')");
        /*
        $sql = "INSERT INTO user(name , pass)
        values('$name', '$pass')";
        $result = mysql_query($sql);
        */
        //登録エラー
        if ($addUser) {
            print("SQLの実行に失敗しました<BR>");
            //print(mysql_errno().": ".mysql_error()."<BR>");
            exit;
        }
        //登録成功
        else {
            print("登録完了");
        }
    }
    else {
        print("Passと確認用Passが違います。<br /><br />");
        print('<a href="register.html">新規登録画面に戻る</a>');
    }
?>
    <br /><br />
<?php
}
?>

<a href = "login.html" >ログイン画面に戻る</a>