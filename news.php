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
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NAMED,
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
    $select_article_sentence_sql = 'select article_sentence from article_contents where article_id = '.$select_article_id;
    $select_article_sentence = $pdo->prepare($select_article_sentence_sql);
    $select_article_sentence->execute();
    $article_sentence = $select_article_sentence->fetchAll();
    //for($sen_count=1;isset())
    
    //記事内画像取得
    $select_article_img_sql = 'select img_type, img_name, thumb_data from images where article_id = '.$select_article_id;
    $select_article_img = $pdo->prepare($select_article_img_sql);
    $select_article_img->execute();
    $article_img = $select_article_img->fetchAll();
    
    /************ memo ***************
        fetch() 単一のカラムを取得
        fetchAll()　全てのカラムを取得
    *********************************/
    
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>新着記事</title>
</head>

<body>
    <?php
    //タイトルの表示
    if (isset($title)) {
        echo "<h2>タイトル：".$title."</h2>";
    }
    $img_number=0;
    //画像がある場合、つまり投稿内容が分割されて保存されている場合
    if (isset($article_img[$img_number])) {
        for ($img_number;isset($article_img[$img_number]);$img_number++) {
            //分割している記事内容の表示
            if (isset($article_sentence[$img_number]["article_sentence"])) {
                echo $article_sentence[$img_number]["article_sentence"];
            }
            //
            else {
                break;
            }
            //サムネイルの表示
            if (isset($article_img[$img_number]["img_name"])) {
                echo "<br />";
                echo "<br />";
                echo sprintf(
                    '<img src="data:%s;base64,%s" alt="%s" />',
                    image_type_to_mime_type($article_img[$img_number]["img_type"]),
                    base64_encode($article_img[$img_number]["thumb_data"]),
                    h($article_img[$img_number]["img_name"])
                );
            }
        }
        //分割した記事で5番目の画像以降に文章がある場合表示
        if (isset($article_sentence[$img_number]["article_sentence"])) {
            echo $article_sentence[$img_number]["article_sentence"];
        }
    }
    //記事内容が単一な場合
    else {
        //記事内容の表示
        if (isset($article_sentence[$img_number]["article_sentence"])) {
            echo $article_sentence[$img_number]["article_sentence"];
        }
    }
    //表示内容のリセット
    unset($article_sentence);
    unset($article_img);
    unset($title);
    ?>
    </body>
</html>