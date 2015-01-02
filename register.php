<html>
<head>
<title>投稿ページ　新規登録</title>
</head>

<body>
<h1>新規登録 </h1>
<?php
if ($_POST["submit"]!="") {
    $new_user_name = $_POST['name'];
    $pass = $_POST['pass'];
    $pass_confirm= $_POST['passConf'];
    $introducer_name = $_POST['introName'];
    $introducer_pass = $_POST['introPass'];
    
    require "database.php";
    $mysqli = new mysqli($host, $db_user, $db_pass, $table_name);

    //紹介者ID照会
    $introducer_confirm = $mysqli->query("select user_id from users
    where user_name = '$introducer_name' and user_pass = '$introducer_pass'");

    if (!isset($new_user_name)) {
        printf("ユーザ名を入力してください。<br /><br />");
        print('<a href="register.html">新規登録画面に戻る</a>');
        exit;
    }
    //ユーザ名およびpassの長さに関するエラー
    else if( !(4 <= mb_strlen($new_user_name)&& mb_strlen($new_user_name) <= 8)) {
        printf("ユーザ名は4～8文字で登録してください。<br /><br />");
        print('<a href="register.html">新規登録画面に戻る</a>');
        exit;
    }
    else if(! (8 <= strlen($pass) && strlen($pass) <= 16)) {
        print("パスワードは8～16文字で登録してください。<br /><br />");
        print('<a href="register.html">新規登録画面に戻る</a>');
        exit;
    }
    
    //紹介者が存在しない場合
    if (!$introducer_confirm) {
        print("紹介者が存在しません。もう一度入力して下さい<BR><br />");
        print('<a href="register.html">新規登録画面に戻る</a>');
        //print(mysql_errno().": ".mysql_error()."<BR>");
        exit;
    }
    
    if ($pass == $pass_confirm){
        //新規ユーザ登録
        $introducer_info = $introducer_confirm->fetch_assoc();
        $introducer_id = $introducer_info['user_id'];
        $add_user_sql = "INSERT INTO users(user_name , user_pass, introducer_id)
        values('$new_user_name', '$pass', '$introducer_id')";

        //登録成功
        if ($mysqli->query($add_user_sql)) {
            print("登録完了");
        }
        //登録エラー
        else {
            print("SQLの実行に失敗しました<BR>");
            //print(mysql_errno().": ".mysql_error()."<BR>");
            exit;
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
print('<a href = "login.html" >ログイン画面に戻る</a>');
?>