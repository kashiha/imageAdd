<html>
<head><title>投稿結果</title></head>
<body>
<?php
if(isset($_POST["pre"]) && $_POST["pre"] !="")
{
	session_start();
	$_SESSION['preview'] = nl2br($_POST['detail']);
	header('Location: http://localhost/yui/preview.php', true, 301);
}

 if (isset($_POST["submit"]) &&$_POST["submit"]!="")
 {
  $sub= $_POST['sub'];
  $title = $_POST['title'];
  //\nを<br />に変換
  $detail= nl2br($_POST['detail']);
  $flag = $_POST['flag'];
  
  if ($_FILES["upfile"]["tmp_name"]=="")
  {
   print("ファイルのアップロードができませんでした。<BR>\n
   もしくはファイルが選択されていません");
   exit;
  }
  $fp = fopen($_FILES["upfile"]["tmp_name"], "rb");
  if(!$fp)
  {
   print("アップロードしたファイルを開けませんでした");
   exit;
  }
  $imgdat = fread($fp, filesize($_FILES["upfile"]["tmp_name"]));
  fclose($fp);

  print("ファイルサイズ：{$_FILES["upfile"]["size"]}<BR>\n");
  $len = strlen($imgdat);
  print("データ長 = $len<BR>");

  $imgdat = addslashes($imgdat);

  //DBへの接続
  require "database.php";

  $sql = "INSERT INTO data(sub, title, detail, img, garellyFlag)
  values('$sub', '$title', '$detail', '$imgdat','$flag')";
  $result = mysql_query($sql);
  if (!$result)
  {
   print("SQLの実行に失敗しました<BR>");
   print(mysql_errno().": ".mysql_error()."<BR>");
   exit;
  }
  
  $sql = "select max(dataId) from data";
  $result = mysql_query($sql);
  if (!$result)
  {
   print("SQLの実行に失敗しました<BR>");
   print(mysql_errno().": ".mysql_error()."<BR>");
   exit;
  }
  
  $row = mysql_fetch_row($result);
  
  print("登録完了\n登録ID:" . $row[0]);?>
  <br /><br />
  <?php
  
  mysql_close($conn);
  
  unlink($_FILES["upfile"]["tmp_name"]);
 }
 
 ?>
 
 <a href = "contribute.php" >戻る</a>
