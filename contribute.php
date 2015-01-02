<html>
<head>
	<title>投稿ページ</title>
    <?php
    /*
    jQuery.selection plugin
        http://madapaja.github.io/jquery.selection/ja_jp.html
        The MIT License
        Copyright © 2010-2012 Koji Iwasaki (@madapaja).
    */
    ?>
	<!-- Load jQuery -->
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	 
	<!-- Load jQuery.selection plugin-->
	<script src="jquery.selection.js"></script>
	 
	<script>
	  $(document).ready(function(){
		$('#do-replace').on('click', function(e){
		  e.preventDefault();
	 
		  // replace selected text by FOOBAR / 選択テキストを「FOOBAR」に置き換え
		  $('#textarea').selection('replace', {text: 'FOOBAR'});
		})
	  });
	</script>
</head>

<?php
	session_start();
    //ログインしていない場合トップに戻る
	if(!(isset($_SESSION['userId'])) || $_SESSION['userId'] == ""){
		header('Location: http://localhost/yui/login.html', true, 301);
		exit();
	}
?>

<body>
	<h1>新規投稿</h1>
    
    投稿内容修飾 <br /><br />
		<!-- htmlタグ付与 -->
		<input type="button" id="wrap-strong" value="Wrap strong tag / 強調タグで囲む" />
		<input type="button" id="wrap-link" value="Wrap link tag / リンクタグで囲む" />
		<br/>
		<!-- 見出し -->
		<input type="button" id="headline-L" value="見出し 大" />
		<input type="button" id="headline-M" value="見出し 中" />
		<input type="button" id="headline-S" value="見出し 小" />
		<br />
		
		<!-- フォントカラー -->
		<input type="button" id="col-r" value="赤" />
		<input type="button" id="col-b" value="青" />
		<input type="button" id="col-y" value="黄" />
		<input type="button" id="col-g" value="緑" />
		<br />
		<!-- フォントへの特殊文字 -->
		<input type="button" id="font-bold" value="太字" />
		<input type="button" id="font-italic" value="斜体" />
		<input type="button" id="font-underline" value="下線" />
		<br /><br />
        <!-- 画像の挿入 -->
    画像の挿入　<br /><br />
        <input type="button" id="img1" value="投稿画像1" />
        <input type="button" id="img2" value="投稿画像2" />
        <input type="button" id="img3" value="投稿画像3" />
        <input type="button" id="img4" value="投稿画像4" />
        <input type="button" id="img5" value="投稿画像5" />
    <br /><br />
        
	<form action="preview.php" method="post" enctype="multipart/form-data">
	<table border="1">
		<tr>
		<td>タイトル</td>
		<td><input type="text" name="title"></td>
		</tr>
		
		<tr>
		<td>投稿内容</td>
		<td><textarea name="detail" id="textarea" rows="8" cols="70"></textarea></td>
		</tr>

		<tr>
		<td>投稿画像1</td>
		<td>
		<INPUT type="hidden" name="MAX_FILE_SIZE1" value="65536">
		<input type="file" name="upfile1" id="upfile1">
            ギャラリー投稿
        <input type="radio" name="galley1" value="1" >する
        <input type="radio" name="galley1" value="0" checked>しない
        </td>
		</tr>
        
        <tr>
		<td>投稿画像2</td>
		<td>
		<INPUT type="hidden" name="MAX_FILE_SIZE2" value="65536">
		<input type="file" name="upfile2" id="upfile2">
            ギャラリー投稿
        <input type="radio" name="galley2" value="0" >する
        <input type="radio" name="galley2" value="1" checked>しない
        </td>
		</tr>

        <tr>
		<td>投稿画像3</td>
		<td>
		<INPUT type="hidden" name="MAX_FILE_SIZE3" value="65536">
		<input type="file" name="upfile3" id="upfile3">
            ギャラリー投稿
        <input type="radio" name="galley3" value="1" >する
        <input type="radio" name="galley3" value="0" checked>しない
        </td>
		</tr>
        
        <tr>
		<td>投稿画像4</td>
		<td>
		<INPUT type="hidden" name="MAX_FILE_SIZE4" value="65536">
		<input type="file" name="upfile4" id="upfile4">
            ギャラリー投稿
        <input type="radio" name="galley4" value="1" >する
        <input type="radio" name="galley4" value="0" checked>しない
        </td>
		</tr>
        
        <tr>
		<td>投稿画像5</td>
		<td>
		<INPUT type="hidden" name="MAX_FILE_SIZE5" value="65536">
		<input type="file" name="upfile5" id="upfile5">
            ギャラリー投稿
        <input type="radio" name="galley5" value="1" >する
        <input type="radio" name="galley5" value="0" checked>しない
        </td>
		</tr>
        
		<tr>
		<td colspan="3" align="center">
		<input type="reset" value="リセット">
		<input type="submit" name="preview" value="プレビューを見る">
		</td>
		</tr>
	</table>
	</form>


	<a href ="logout.php">ログアウト</a>
</body>

<script>
	// Wrap strong tag / 強調タグで囲む
	$('#wrap-strong').click(function(){
	  $('#textarea')
		// insert before string '<strong>'
		// <strong> を選択テキストの前に挿入
		.selection('insert', {text: '<strong>', mode: 'before'})
		// insert after string '</strong>'
		// </strong> を選択テキストの後に挿入
		.selection('insert', {text: '</strong>', mode: 'after'});
	});
	 
	// Wrap link tag / リンクタグで囲む
	$('#wrap-link').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		// insert before string '<a href="'
		// <a href=" を選択テキストの前に挿入
		.selection('insert', {text: '<a href="', mode: 'before'})
		// replace selected text by string 'http://'
		// 選択テキストを http:// に置き換える（http:// を選択状態に）
		.selection('replace', {text: 'http://'})
		// insert after string '">SELECTED TEXT</a>' 
		// ">選択テキスト</a> を選択テキストの後に挿入
		.selection('insert', {text: '">'+ selText + '</a>', mode: 'after'});
	});
	
	//見出しタグ　付与
	$('#headline-L').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		// insert before string '<a href="'
		// <a href=" を選択テキストの前に挿入
		.selection('insert', {text: '<h1>', mode: 'before'})
		// insert after string '">SELECTED TEXT</a>' 
		// ">選択テキスト</a> を選択テキストの後に挿入
		.selection('insert', {text: '</h1>', mode: 'after'});
	});
	
		$('#headline-M').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		// <h3>を選択テキストの前に挿入
		.selection('insert', {text: '<h3>', mode: 'before'})
		// </h3>を選択テキストの後に挿入
		.selection('insert', {text: '</h3>', mode: 'after'});
	});
	
		$('#headline-S').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		// <h5>を選択テキストの前に挿入
		.selection('insert', {text: '<h5>', mode: 'before'})
		// </h5> を選択テキストの後に挿入
		.selection('insert', {text: '</h5>', mode: 'after'});
	});
	
	//フォントカラーの指定
		$('#col-r').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<font color="#ff0000">', mode: 'before'})
		.selection('insert', {text: '</font>', mode: 'after'});
	});
	 
	 	$('#col-b').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<font color="#0000ff">', mode: 'before'})
		.selection('insert', {text: '</font>', mode: 'after'});
	});
	
		$('#col-y').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<font color="#ffff00">', mode: 'before'})
		.selection('insert', {text: '</font>', mode: 'after'});
	});
	
		$('#col-g').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<font color="#00ff00">', mode: 'before'})
		.selection('insert', {text: '</font>', mode: 'after'});
	});
	
	//フォント効果付与
		$('#font-bold').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<b>', mode: 'before'})
		.selection('insert', {text: '</b>', mode: 'after'});
	});
	
		$('#font-italic').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<i>', mode: 'before'})
		.selection('insert', {text: '</i>', mode: 'after'});
	});
	
	$('#font-underline').click(function(){
	  // Get selected text / 選択テキストを取得
	  var selText = $('#textarea').selection();
	 
	  $('#textarea')
		.selection('insert', {text: '<u>', mode: 'before'})
		.selection('insert', {text: '</u>', mode: 'after'});
	});
    
    //画像の挿入
    $('#img1').click(function(){
        var input_file = document.getElementById("upfile1");
        // Get selected text / 選択テキストを取得
        if (input_file.value) {
            var selText = $('#textarea').selection();

            $('#textarea')
            .selection('insert', {text: '[File-1]', mode: 'before'})
        }
        else {
            alert("該当する画像がありません。選択してください。");
        }
	});
    
    $('#img2').click(function(){
	  var input_file = document.getElementById("upfile2");
        // Get selected text / 選択テキストを取得
        if (input_file.value) {
            var selText = $('#textarea').selection();

            $('#textarea')
            .selection('insert', {text: '[File-2]', mode: 'before'})
        }
        else {
            alert("該当する画像がありません。選択してください。");
        }
	});
    
    $('#img3').click(function(){
	  var input_file = document.getElementById("upfile3");
        // Get selected text / 選択テキストを取得
        if (input_file.value) {
            var selText = $('#textarea').selection();

            $('#textarea')
            .selection('insert', {text: '[File-3]', mode: 'before'})
        }
        else {
            alert("該当する画像がありません。選択してください。");
        }
	});
    
    $('#img4').click(function(){
	  var input_file = document.getElementById("upfile4");
        // Get selected text / 選択テキストを取得
        if (input_file.value) {
            var selText = $('#textarea').selection();

            $('#textarea')
            .selection('insert', {text: '[File-4]', mode: 'before'})
        }
        else {
            alert("該当する画像がありません。選択してください。");
        }
	});
    
    $('#img5').click(function(){
	  var input_file = document.getElementById("upfile5");
        // Get selected text / 選択テキストを取得
        if (input_file.value) {
            var selText = $('#textarea').selection();

            $('#textarea')
            .selection('insert', {text: '[File-5]', mode: 'before'})
        }
        else {
            alert("該当する画像がありません。選択してください。");
        }
	});
	
	// Get selected text / 選択テキストを取得
	$('#sel-textarea').click(function(){
	  // alert selected text
	  // テキストエリアの選択範囲をアラートする
	  alert($('#textarea').selection());
	  $('#textarea').focus();
	});
</script>

</html>