<?php

    /* HTML特殊文字をエスケープする関数 */
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // XHTMLとしてブラウザに認識させる
    // (IE8以下はサポート対象外ｗ)
    
    $header = 'Content-Type: application/xhtml+xml; charset=utf-8';
    header($header);
    
try {

    // データベースに接続
    require "pdo_database.php";
	
	$img_number = 1;
    $file_id = "upfile".$img_number;
    $test = 0;
    
    session_start();
    
    /* アップロードがあったとき */
    while (isset($_FILES[$file_id]['error']) && is_int($_FILES[$file_id]['error'])) {

        // バッファリングを開始
        ob_start();
		//1
		$test += 2;

        try {

            // $_FILES['upfile']['error'] の値を確認
            switch ($_FILES[$file_id]['error']) {
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                    throw new RuntimeException('ファイルが選択されていません', 400);
                case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                    throw new RuntimeException('ファイルサイズが大きすぎます', 400);
                default:
                    throw new RuntimeException('その他のエラーが発生しました', 500);
            }
			
			//2
			$test++;
			
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
			//3
			$test++;
			
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

			//4
			$test++;
			
            // INSERT処理
            $add_img_sql = $pdo->prepare(implode(' ', array(
                'INSERT',
                'INTO multiimage(id, imageid, name, type, raw_data, thumb_data, date)',
                'VALUES (?, ?, ?, ?, ?, ?, ?)',
            )));
            
			//5
			$test++;
			
			$get_newest_article_sql = 'select id from multiimage ORDER BY id DESC limit 1';
            $newest_article = $pdo->prepare($get_newest_article_sql);
			$newest_article->execute();
           
			if (!$newest_article_id = $newest_article->fetch()) {
                $next_article_id = 1;
            }
			else {
				$next_article_id = $newest_article_id['id'] + 1;
			}
			
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
            
            //画像データをセッションに保存
            //id, imageid, name, type, raw_data, thumb_data
            
            
            $_SESSION["img"][$img_number]["article_id"] = $next_article_id;
            $_SESSION["img"][$img_number]["article_img_id"] = $img_number;
            $_SESSION["img"][$img_number]["img_name"] = $_FILES[$file_id]['name'];
            $_SESSION["img"][$img_number]["img_type"] = $info[2];
            $_SESSION["img"][$img_number]["img_data"] =file_get_contents($_FILES['upfile1']['tmp_name']);
            $_SESSION["img"][$img_number]["thumb_data"] = ob_get_clean();
            
            $id = $_SESSION["img"][$img_number]["article_id"];
            $img = $_SESSION["img"][$img_number]["img_data"];
            $name = $_SESSION["img"][$img_number]["img_name"];
            $type = $_SESSION["img"][$img_number]["img_type"];

            $msg = array('green', 'ファイルは正常にアップロードされました');
			//6
			$test++;
			
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
        
        if ($img_number >=5) {
            break;
        }

    /* ID指定があったとき */
    }
/*    elseif (isset($_GET['id'])) {

        try {

            $stmt = $pdo->prepare(implode(' ', array(
                'SELECT type, raw_data',
                'FROM multiimage',
                'WHERE id = ?',
                'LIMIT 1',
            )));
            $stmt->bindValue(1, $_GET['id'], PDO::PARAM_INT);
            $stmt->execute();
            if (!$row = $stmt->fetch()) {
                throw new RuntimeException('該当する画像は存在しません', 404);
            }
            header('X-Content-Type-Options: nosniff');
            header('Content-Type: ' . image_type_to_mime_type($row['type']));
            echo $row['raw_data'];
            exit;

        } catch (PDOException $e) {

            header($header, true, 500); 
            $msg = array('red', 'SELECT処理中にエラーが発生しました');

        } catch (RuntimeException $e) {

            header($header, true, $e->getCode()); 
            $msg = array('red', $e->getMessage());

        }

    }
    else {
        $msg = "は？";
    }
*/
    /*
    // サムネイル一覧取得
    $rows = $pdo->query(implode(' ', array(
                'SELECT id, name, type, thumb_data, date',
                'FROM multiimage',
                'ORDER BY date DESC',
            )))->fetchAll();
    */
    

} catch (PDOException $e) { }
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
</head>

<body>
<h1>プレビュー画面</h1>

<?php echo "file_id:".$file_id ;
    echo "test:".$test."<br /><br />" ; ?>
    <?php //画像表示 ?>
    <?=sprintf(
           '<img src="data:%s;base64,%s" alt="%s" />',
           image_type_to_mime_type($type),
           base64_encode($img),
           h($name)
       )?><br /><br />
    
<?php if (isset($msg)): ?>
  <fieldset>
    <legend>メッセージ</legend>
    <span style="color:<?=h($msg[0])?>;"><?=h($msg[1])?></span>
  </fieldset>
<?php endif; ?>

<a href = "contribute.php" >戻る</a>

</body>
</html>