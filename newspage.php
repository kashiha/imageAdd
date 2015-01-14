<?php
    /* HTML特殊文字をエスケープする関数 */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
    
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

<html>
<head>
<title>新着記事</title>
<link rel="stylesheet" href="css/main3.css" type="text/css" />
<link rel="stylesheet" href="css/style2.css" type="text/css" media="screen"/>

</head>
<body>


<div id="text">
    <div id="title-main"><a class="tit" href="index.html">ITF-2</a>
    </div>
    <a class="project-jump" href="../../index.html">「結」プロジェクトホームへ</a>
    <div class="content">
        <ul id="sdt_menu" class="sdt_menu">
            <li>
                <a href="mission.html">
                    <img src="images/1.jpg" alt="mission.html"/>
                    <span class="sdt_active"></span>
                    <span class="sdt_wrap">
                        <span class="sdt_link">「結」ミッション</span>
                        <span class="sdt_descr">Mission</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="satellite.html">
                    <img src="images/3.jpg" alt="satellite.html"/>
                    <span class="sdt_active"></span>
                    <span class="sdt_wrap">
                        <span class="sdt_link">衛星紹介</span>
                        <span class="sdt_descr">Satellite</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="member.html">
                    <img src="images/4.jpg" alt="member.html"/>
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
                <a href="picture.html">
                    <img src="images/5.jpg" alt="picture.html"/>
                    <span class="sdt_active"></span>
                    <span class="sdt_wrap">
                        <span class="sdt_link">ギャラリー</span>
                        <span class="sdt_descr">Our photograph</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="diary.html">
                    <img src="images/7.jpg" alt="diary.html"/>
                    <span class="sdt_active"></span>
                    <span class="sdt_wrap">
                        <span class="sdt_link">ダイアリー</span>
                        <span class="sdt_descr">Diary</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="contact.html">
                    <img src="images/6.jpg" alt=""/>
                    <span class="sdt_active"></span>
                    <span class="sdt_wrap">
                        <span class="sdt_link">お問い合わせ</span>
                        <span class="sdt_descr">Contact</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="report.html">
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
        //タイトルの表示
        if (isset($title)) {
            echo "<h3>".$title."</h3><HR><br />";
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
        //投稿時間の表示
        echo "<br /><br /><HR><Div Align=\"right\">投稿時間：".$time."</Div>";
        //表示内容のリセット
        unset($article_sentence);
        unset($article_img);
        unset($title);
        ?>
    </div>

    <img src="IMG_0158.JPG" width="1200px">
    <br /><br />
    <center>
    <font color="black">&copy;2014 筑波大学「結」プロジェクト</font>
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
