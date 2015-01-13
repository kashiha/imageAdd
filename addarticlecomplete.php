<?php
    /* HTML特殊文字をエスケープする関数 */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // XHTMLとしてブラウザに認識させる
    // (IE8以下はサポート対象外ｗ)
    
    $header = 'Content-Type: application/xhtml+xml; charset=utf-8';
    header($header);
    
    //ログインしていない場合トップに戻る
	session_start();
	if(!(isset($_SESSION['userId'])) || $_SESSION['userId'] == ""){
		header('Location: http://localhost/yui/login.html', true, 301);
		exit();
	}

    try {
        require "database.php";
        $pdo = new PDO(
            $db_set,
            $db_user,
            $db_pass,
            array(
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            )
        );
        //新規記事の追加
        try {
            $add_article_sql = $pdo->prepare(implode(' ', array(
                'INSERT',
                'INTO articles(title, add_user_id)',
                'VALUES (?, ?)',
            )));
            $add_article_sql->execute(array(
                $_SESSION["article_title"],
                $_SESSION['userId']
            ));
        
            for ($img_number=1;isset($_SESSION["img_name"][$img_number]);$img_number++) {
                // 画像のINSERT処理
                $add_img_sql = $pdo->prepare(implode(' ', array(
                    'INSERT',
                    'INTO images(article_id, article_img_number, img_name, img_type, raw_data, thumb_data, is_galley)',
                    'VALUES (?, ?, ?, ?, ?, ?, ?)',
                )));
                $add_img_sql->execute(array(
                    $_SESSION["article_id"],
                    $_SESSION["article_img_id"][$img_number],
                    $_SESSION["img_name"][$img_number],
                    $_SESSION["img_type"][$img_number],
                    $_SESSION["img_data"][$img_number],
                    $_SESSION["thumb_data"][$img_number],
                    $_SESSION["galley_flag"][$img_number],
                ));
            }
            for ($sentence_number=1;isset($_SESSION["divide_article"][$sentence_number]);$sentence_number++) {
                //分割した記事内容の追加処理
                $add_article_sentence_sql = $pdo->prepare(implode(' ', array(
                    'INSERT',
                    'INTO article_contents(article_id, sentence_number, article_sentence)',
                    'VALUES (?, ?, ?)',
                )));
                $add_article_sentence_sql->execute(array(
                    $_SESSION["article_id"],
                    $sentence_number,
                    $_SESSION["divide_article"][$sentence_number],                
                ));
                
                $msg = array('green', 'ファイルは正常にアップロードされました');
                //$name = $_SESSION["img_name"][$img_number];
            }
        } catch (PDOException $e) {
            header($header, true, 500);
            $msg = array('red', 'INSERT処理中にエラーが発生しました');
        } catch (RuntimeException $e) {
            header($header, true, $e->getCode()); 
            $msg = array('red', $e->getMessage());
        }
        /*
        $add_article_sentence_sql->execute(array(
                    $_SESSION["article_id"],
                    $img_number,
                    $_SESSION["divide_article"][$img_number],                
        ));
        */
    } catch (PDOException $e) { }
    
    //sessionのリセット
    unset($_SESSION["article_id"]);
    unset($_SESSION["article_img_id"]);
    unset($_SESSION["img_name"]);
    unset($_SESSION["img_type"]);
    unset($_SESSION["img_data"]);
    unset($_SESSION["thumb_data"]);
    unset($_SESSION["galley_flag"]);
    //unset($_SESSION["divide_article"]);
    unset($_SESSION["article_title"]);

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>投稿完了</title>
</head>
<body>
<?php if (isset($msg)): ?>
  <fieldset>
    <legend>メッセージ</legend>
    <span style="color:<?=h($msg[0])?>;"><?=h($msg[1])?></span>
  </fieldset>
<?php endif; ?>
<?php
    echo "投稿が完了しました。<br /><br />";
    echo "<a href =\"contribute.php\">投稿画面へ戻る</a>　<a href =\"logout.php\">ログアウト</a>";
?>
</body>
</html>
