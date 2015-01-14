<?php

    /* HTML特殊文字をエスケープする関数 */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // XHTMLとしてブラウザに認識させる
    // (IE8以下はサポート対象外ｗ)
    /*
    $header = 'Content-Type: application/xhtml+xml; charset=utf-8';
    header($header);
    */
    
    if (isset($_POST["preview"]) && $_POST["preview"]!="") {
        
        try {

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
            
            $img_number = 1;
            $file_id = "upfile".$img_number;
            
            session_start();
            
            //現在投稿されている記事の中で最も新しいもののIDを取得
            $get_newest_article_sql = 'select article_id from articles ORDER BY article_id DESC limit 1';
            $newest_article = $pdo->prepare($get_newest_article_sql);
            $newest_article->execute();
           
            if (!($newest_article_id = $newest_article->fetch())) {
                $next_article_id = 1;
            }
            else {
                $next_article_id = $newest_article_id['article_id'] + 1;
            }
            unset($newest_article_id);
            $_SESSION["article_id"] = $next_article_id;
            
            /* アップロードがあったとき */
            while (isset($_FILES[$file_id]['error']) && is_int($_FILES[$file_id]['error']) && $_FILES[$file_id]['error'] == 0) {
            
                // バッファリングを開始
                ob_start();

                try {
                    // $_FILES['upfile']['error'] の値を確認
                    switch ($_FILES[$file_id]['error']) {
                        case UPLOAD_ERR_OK: // OK
                            break;
                        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                            throw new RuntimeException("ファイルが選択されていません", 400);
                        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                            throw new RuntimeException('ファイルサイズが大きすぎます', 400);
                        default:
                            throw new RuntimeException('その他のエラーが発生しました', 500);
                    }

                    // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので
                    // MIMEタイプを自前でチェックする
                    if (!$info = @getimagesize($_FILES[$file_id]['tmp_name'])) {
                        throw new RuntimeException('有効な画像ファイルを指定してください', 400);
                    }
                    if (!in_array(
                        $info[2],
                        array(
                            IMAGETYPE_GIF,
                            IMAGETYPE_JPEG,
                            IMAGETYPE_PNG,
                        ),
                        true
                    )) {
                        throw new RuntimeException('未対応の画像形式です', 400);
                    }

                    // サムネイルをバッファに出力
                    $tmp = explode('/', $info['mime']);
                    $create = "imagecreatefrom{$tmp[1]}";
                    $output = "image{$tmp[1]}";
                    if ($info[0] >= $info[1]) {
                        $dst_w = 120;
                        $dst_h = ceil(120 * $info[1] / max($info[0], 1));
                    } else {
                        $dst_w = ceil(120 * $info[0] / max($info[1], 1));
                        $dst_h = 120;
                    }
                    if (!$src = @$create($_FILES[$file_id]['tmp_name'])) {
                        throw new RuntimeException('画像リソースの生成に失敗しました', 500);
                    }
                    $dst = imagecreatetruecolor($dst_w, $dst_h);
                    imagecopyresampled(
                        $dst, $src,
                        0, 0, 0, 0,
                        $dst_w, $dst_h, $info[0], $info[1]
                    );
                    $output($dst);
                    imagedestroy($src);
                    imagedestroy($dst);

                    // INSERT処理
                    /*
                    $add_img_sql = $pdo->prepare(implode(' ', array(
                        'INSERT',
                        'INTO multiimage(id, imageid, name, type, raw_data, thumb_data, date)',
                        'VALUES (?, ?, ?, ?, ?, ?, ?)',
                    )));
                    */
                    /*
                    //現在投稿されている記事の中で最も新しいもののIDを取得
                    $get_newest_article_sql = 'select article_id from articles ORDER BY article_id DESC limit 1';
                    $newest_article = $pdo->prepare($get_newest_article_sql);
                    $newest_article->execute();
                   
                    if (!($newest_article_id = $newest_article->fetch())) {
                        $next_article_id = 1;
                    }
                    else {
                        $next_article_id = $newest_article_id['id'] + 1;
                    }
                    unset($newest_article_id);
                    */
                    /*
                    $add_img_sql->execute(array(
                        $next_article_id,
                        1,
                        $_FILES[$file_id]['name'],
                        $info[2],
                        file_get_contents($_FILES['upfile1']['tmp_name']),
                        ob_get_clean(), // バッファからデータを取得してクリア
                        date_format(
                            new DateTime('now', new DateTimeZone('Asia/Tokyo')),
                            'Y-m-d H:i:s'
                        ),
                    ));
                    */
                    
                    //ギャラリーに投稿するかどうかのフラグの変数名宣言
                    $bool_add_galley = "galley".$img_number;
                                        
                    //画像データをセッションに保存
                    $_SESSION["article_img_id"][$img_number] = $img_number;
                    $_SESSION["img_name"][$img_number] = $_FILES[$file_id]['name'];
                    $_SESSION["img_type"][$img_number] = $info[2];
                    $_SESSION["img_data"][$img_number] = file_get_contents($_FILES[$file_id]['tmp_name']);
                    $_SESSION["thumb_data"][$img_number] = ob_get_clean();
                    $_SESSION["galley_flag"][$img_number] = (int) $_POST[$bool_add_galley];

                    $msg = array('green', 'ファイルは正常にアップロードされました');
 
                } catch (PDOException $e) {

                    ob_end_clean(); // バッファをクリア
                    header($header, true, 500);
                    $msg = array('red', 'INSERT処理中にエラーが発生しました');

                } catch (RuntimeException $e) {

                    ob_end_clean(); // バッファをクリア
                    header($header, true, $e->getCode()); 
                    $msg = array('red', $e->getMessage());

                }
                
                $img_number++;
                $file_id = "upfile".$img_number;
                
                if ($img_number >5) {
                    break;
                }
            }         
        } catch (PDOException $e) { }
        
        //画像を文章間に挿入するための分割処理
        $_SESSION["divide_article"][0] = $tmp_article_content = nl2br($_POST["detail"]);
        
        for ($file_num=1;$file_num<=5;$file_num++) {
            //検出文字列設定
            $img_position_string = "[File-".$file_num."]";
            //投稿内容に画像が挿入されていないか探索
            if (strstr($tmp_article_content, $img_position_string)) {
                //echo $file_num."だよ<br /><br />";
                $pieces = explode($img_position_string, $tmp_article_content);
                $_SESSION["divide_article"][$file_num] = $pieces[0];
                $tmp_article_content = $pieces[1];
                //echo $pieces[0]."<br /><br />";
                //echo $pieces[1]."<br /><br />";
                //file-5の後の記事内容の余りを保存
                if ($file_num == 5) {
                    $last_num = $file_num + 1;
                    $_SESSION["divide_article"][$last_num] = $tmp_article_content;
                }
            }
            else {
                $_SESSION["divide_article"][$file_num] = $tmp_article_content;
                break;
            }
        } 
        //送信データをセッションに保存
        $_SESSION["article_title"] = $_POST["title"];
        
        //DB切断（明示的に）
        unset($pdo);
    }
	/*
    session_start();
	//session変数に保存したプレビューデータの表示
	print($_SESSION['preview']);
	//previewデータの破棄
	//unset($_SESSION['preview']);
    */
    ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Preview</title>
    <link rel="stylesheet" href="css/main3.css" type="text/css" />
    <link rel="stylesheet" href="css/style2.css" type="text/css" media="screen"/>
</head>

<body>
<div id="text">
<div id="title-main"><a class="tit" href="">ITF-2</a>
    </div>
    <a class="project-jump" href="">「結」プロジェクトホームへ</a>
<div class="content">
	<ul id="sdt_menu" class="sdt_menu">

		<li>
			<a href="">
				<img src="images/1.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">ミッション</span>
					<span class="sdt_descr">Mission</span>
				</span>
			</a>
		</li>

		<li>
			<a href="">
				<img src="images/3.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">紹介</span>
					<span class="sdt_descr">intro</span>
				</span>
			</a>
		</li>
		<li>
			<a href="">
				<img src="images/4.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">メンバー</span>
					<span class="sdt_descr">Member</span>
				</span>
			</a>
			<div class="sdt_box">
					<a href="recruit.html">メンバー募集</a>
			</div>
		</li>

		<li>
			<a href="">
				<img src="images/5.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">ギャラリー</span>
					<span class="sdt_descr">Our photograph</span>
				</span>
			</a>
		</li>

	<li>
			<a href="">
				<img src="images/7.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">ダイアリー</span>
					<span class="sdt_descr">Diary</span>
				</span>
			</a>
		</li>
		<li>
			<a href="">
				<img src="images/6.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">お問い合わせ</span>
					<span class="sdt_descr">Contact</span>
				</span>
			</a>
		</li>
		<li>
			<a href="">
				<img src="images/8.jpg" alt=""/>
				<span class="sdt_active"></span>
				<span class="sdt_wrap">
					<span class="sdt_link">受信報告</span>
					<span class="sdt_descr">Report</span>
				</span>
			</a>
		</li>
	</ul>
</div>

<div id="column">
    <?php
    //投稿内容の表示
    if (isset($_SESSION["article_title"])) {
        echo "<h3>".$_SESSION["article_title"]."</h3><HR><br />";
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
    $time = date('Y-m-d h:i:s');
    echo "<br /><br /><HR><Div Align=\"right\">投稿時間：".$time."</Div>";
    ?>
    

    <br /><br />
    <a href ="contribute.php">戻る</a>
    <?php echo "　"; ?>
    <a href ="addarticlecomplete.php">投稿する</a>


</div>

<img src="IMG_0158.JPG" width="1200px" />
<br /><br />
<center>
<font color="black">プロジェクト</font>
</center>

</div>
<!-- The JavaScript -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="jquery.easing.1.3.js"></script>
    <script type="text/javascript">
        $(function() {
            /**
            * for each menu element, on mouseenter, 
            * we enlarge the image, and show both sdt_active span and 
            * sdt_wrap span. If the element has a sub menu (sdt_box),
            * then we slide it - if the element is the last one in the menu
            * we slide it to the left, otherwise to the right
            */
            $('#sdt_menu > li').bind('mouseenter',function(){
                var $elem = $(this);
                $elem.find('img')
                     .stop(true)
                     .animate({
                        'width':'170px',
                        'height':'170px',
                        'left':'0px'
                     },400,'easeOutBack')
                     .andSelf()
                     .find('.sdt_wrap')
                     .stop(true)
                     .animate({'top':'140px'},500,'easeOutBack')
                     .andSelf()
                     .find('.sdt_active')
                     .stop(true)
                     .animate({'height':'170px'},300,function(){
                    var $sub_menu = $elem.find('.sdt_box');
                    if($sub_menu.length){
                        var left = '170px';
                        if($elem.parent().children().length == $elem.index()+1)
                            left = '-170px';
                        $sub_menu.show().animate({'left':left},200);
                    }	
                });
            }).bind('mouseleave',function(){
                var $elem = $(this);
                var $sub_menu = $elem.find('.sdt_box');
                if($sub_menu.length)
                    $sub_menu.hide().css('left','0px');
                
                $elem.find('.sdt_active')
                     .stop(true)
                     .animate({'height':'0px'},300)
                     .andSelf().find('img')
                     .stop(true)
                     .animate({
                        'width':'0px',
                        'height':'0px',
                        'left':'85px'},400)
                     .andSelf()
                     .find('.sdt_wrap')
                     .stop(true)
                     .animate({'top':'25px'},500);
            });
        });
    </script>
</body>
</html>