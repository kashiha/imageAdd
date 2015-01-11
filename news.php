<?php
    /* HTML特殊文字をエスケープする関数 */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // XHTMLとしてブラウザに認識させる
    // (IE8以下はサポート対象外ｗ)
    $header = 'Content-Type: application/xhtml+xml; charset=utf-8';
    header($header);

    session_start();
    //閲覧記事番号の取得
    $select_article_id = $_GET['article_id'];
    
    // データベースに接続  
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
    
    //表示記事情報（id,タイトル、投稿時間）取得
    $select_article_sql = 'select * from articles ORDER BY article_id = \''.$select_article_id.'\' DESC limit 1';
    $select_article = $pdo->prepare($select_article_sql);
    $select_article->execute();
    $tmp_select_article = $select_article->fetch();
    $title = $tmp_select_article['title'];
    $time = $tmp_select_article['add_date'];
    
    //投稿内容取得
    $select_article_sentence_sql = 'select count(*) from article_contents ORDER BY article_id = \''.$select_article_id.'\'';
    $select_article_sentence = $pdo->prepare($select_article_sentence_sql);
    $select_article_sentence->execute();
    $tmp_article_sentence = $select_article_sentence->fetch();
    //for($sen_count=1;isset())
    
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>新着記事</title>
</head>

<body>
    <?php
    var_dump($tmp_article_sentence);
    //投稿内容の表示
    /*
    if (isset($_SESSION["article_title"])) {
        echo "<h2>タイトル：".$_SESSION["article_title"]."</h2>";
    }
    for ($i=1;$i<=6;$i++) {
        if (isset($_SESSION["divide_article"][$i])) {
            echo $_SESSION["divide_article"][$i];
        }
        else {
            break;
        }
        //サムネイルの表示
        if (isset($_SESSION["img_name"][$i])) {
            echo "<br />";
            echo "<br />";
            echo sprintf(
                '<img src="data:%s;base64,%s" alt="%s" />',
                image_type_to_mime_type($_SESSION["img_type"][$i]),
                base64_encode($_SESSION["thumb_data"][$i]),
                h($_SESSION["img_name"][$i])
            );
        }
    }
    */
    echo $title;
    ?>
    </body>
</html>