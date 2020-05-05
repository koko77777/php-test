<?php

// 変数の初期化
$page_flag = 0;

// サニタイズした値を$cleanに格納
$clean = array();

$error = array();

// サニタイズ
if( !empty($_POST) ) {
	foreach( $_POST as $key => $value ) {
		$clean[$key] = htmlspecialchars( $value, ENT_QUOTES);
	}
}

// btn_confirmがクリックされたら発火
if( !empty($clean['btn_confirm']) ) {

	// validation関数に$cleanを格納、入力の有無を確認
	$error = validation($clean); 

	if( empty($error) ) {

		// $page_flagの値を変えることでページ遷移を管理
		$page_flag = 1; 
		
		// セッションの書き込み
		session_start();
		$_SESSION['page'] = true;
	}
	
// btn_submitがクリックされたら発火
} elseif( !empty($clean['btn_submit']) ) {

	session_start();
	if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

		// セッションの削除
		unset($_SESSION['page']);

	$page_flag = 2;  // $page_flagの値を変えることでページ遷移を管理

	// 変数とタイムゾーンを初期化
	$header = null;
	$auto_reply_subject = null;
	$auto_reply_text = null;
	$admin_reply_subject = null;
	$admin_reply_text = null;
	date_default_timezone_set('Asia/Tokyo');

	// ヘッダー情報を設定
	$header = "MIME-Version: 1.0\n";   //「このメールのMIMEバージョンは、いくつです」な情報が書かれている。
	$header .= "From: koji <koji777kz@gmail.com>\n";

	// 件名を設定
	$auto_reply_subject = 'お問い合わせありがとうございます。';

	// 本文を設定
	$auto_reply_text = "この度は、お問い合わせ頂き誠にありがとうございます。下記の内容でお問い合わせを受け付けました。\n\n";
	$auto_reply_text .= "お問い合わせ日時：" . date("Y-m-d H:i") . "\n";
	$auto_reply_text .= "氏名：" . $clean['your_name'] . "\n";
	$auto_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";
	
	if( $clean['gender'] === "male" ) {
		$auto_reply_text .= "性別：男性\n";
	} else {
		$auto_reply_text .= "性別：女性\n";
	}

	if( $clean['age'] === "1" ){
		$auto_reply_text .= "年齢：〜19歳\n";
	} elseif ( $clean['age'] === "2" ){
		$auto_reply_text .= "年齢：20歳〜29歳\n";
	} elseif ( $clean['age'] === "3" ){
		$auto_reply_text .= "年齢：30歳〜39歳\n";
	} elseif ( $clean['age'] === "4" ){
		$auto_reply_text .= "年齢：40歳〜49歳\n";
	} elseif( $clean['age'] === "5" ){
		$auto_reply_text .= "年齢：50歳〜59歳\n";
	} elseif( $clean['age'] === "6" ){
		$auto_reply_text .= "年齢：60歳〜\n";
	}

	$auto_reply_text .= "お問い合わせ内容：" . nl2br($clean['contact']) . "\n\n";

	$auto_reply_text .= "koji";

	// メール送信
	mb_send_mail( $clean['email'], $auto_reply_subject, $auto_reply_text, $header);

	// 運営側へ送るメールの件名
	$admin_reply_subject = "お問い合わせを受け付けました";
	
	// 本文を設定
	$admin_reply_text = "下記の内容でお問い合わせがありました。\n\n";
	$admin_reply_text .= "お問い合わせ日時：" . date("Y-m-d H:i") . "\n";
	$admin_reply_text .= "氏名：" . $clean['your_name'] . "\n";
	$admin_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";

	if( $clean['gender'] === "male" ) {
		$admin_reply_text .= "性別：男性\n";
	} else {
		$admin_reply_text .= "性別：女性\n";
	}

	if( $clean['age'] === "1" ){
		$admin_reply_text .= "年齢：〜19歳\n";
	} elseif ( $clean['age'] === "2" ){
		$admin_reply_text .= "年齢：20歳〜29歳\n";
	} elseif ( $clean['age'] === "3" ){
		$admin_reply_text .= "年齢：30歳〜39歳\n";
	} elseif ( $clean['age'] === "4" ){
		$admin_reply_text .= "年齢：40歳〜49歳\n";
	} elseif( $clean['age'] === "5" ){
		$admin_reply_text.= "年齢：50歳〜59歳\n";
	} elseif( $clean['age'] === "6" ){
		$admin_reply_text.= "年齢：60歳〜\n";
	}

	$admin_reply_text .= "お問い合わせ内容：" . nl2br($clean['contact']) . "\n\n";

	// 運営側へメール送信
	mb_send_mail( 'koji777kz@gmail.com', $admin_reply_subject, $admin_reply_text, $header);
	
	} else {
		$page_flag = 0;
	}
}

// validation関数を定義
function validation($data) {

	$error = array();

	// バリデーション
	if( empty($data['your_name']) ) { // $dataの中の項目に入力があるかチェック
		$error[] = "「氏名」は必ず入力してください。"; // 配列に追加
	} elseif( 20 < mb_strlen($data['your_name']) ) {
		$error[] = "「氏名」は20文字以内で入力してください。";
	}

	if( empty($data['email']) ) { // $dataの中の項目に入力があるかチェック
		$error[] = "「メールアドレス」は必ず入力してください。"; // 配列に追加
	} elseif( !preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $data['email']) ) {
		$error[] = "「メールアドレス」は正しい形式で入力してください。";
	}
	
	// 性別のバリデーション
	if( empty($data['gender']) ) {
		$error[] = "「性別」は必ず入力してください。";

	} elseif( $data['gender'] !== 'male' && $data['gender'] !== 'female' ) {
		$error[] = "「性別」は必ず入力してください。";
	}
	
	// 年齢のバリデーション
	if( empty($data['age']) ) {
		$error[] = "「年齢」は必ず入力してください。";

	} elseif( (int)$data['age'] < 1 || 6 < (int)$data['age'] ) { // $data['age']に格納された値で判定
		$error[] = "「年齢」は必ず入力してください。";
	}

	// お問い合わせ内容のバリデーション
	if( empty($data['contact']) ) {
		$error[] = "「お問い合わせ内容」は必ず入力してください。";
	}

	// プライバシーポリシー同意のバリデーション
	if( empty($data['agreement']) ) {
		$error[] = "プライバシーポリシーをご確認ください。";

	} elseif( (int)$data['agreement'] !== 1 ) { // value値を利用
		$error[] = "プライバシーポリシーをご確認ください。";
	}

	return $error;
}

?>

<!DOCTYPE>
<html lang="ja">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="keywords" content="">
<title>フォームテスト</title>

<meta property="og:title" content="">
<meta property="og:type" content="website">
<meta property="og:url" content="">
<meta property="og:image" content="">
<meta property="og:site_name" content="">
<meta property="og:description" content="">

<link rel="canonical" href="">
<link rel="stylesheet" href="style.css">

</head>
<body>
	<h1>お問い合わせフォーム</h1>

<!-- 内容確認　if文で表示を制御 -->
<?php if( $page_flag === 1 ): ?>

    <form method="post" action="">
	<div class="element_wrap">
		<label>氏名</label>
		<p><?php echo $clean['your_name']; ?></p>
	</div>
	<div class="element_wrap">
		<label>メールアドレス</label>
		<p><?php echo $clean['email']; ?></p>
	</div>
	<div class="element_wrap">
		<label>性別</label>
		<p><?php if( $clean['gender'] === "male" ){ echo '男性'; }
		elseif( $clean['gender'] === "female" ){ echo '女性'; } 
		else{ echo '選択されていません'; } ?></p>
	</div>
	<div class="element_wrap">
		<label>年齢</label>
		<p><?php if( $clean['age'] === "1" ){ echo '〜19歳'; }
		elseif( $clean['age'] === "2" ){ echo '20歳〜29歳'; }
		elseif( $clean['age'] === "3" ){ echo '30歳〜39歳'; }
		elseif( $clean['age'] === "4" ){ echo '40歳〜49歳'; }
		elseif( $clean['age'] === "5" ){ echo '50歳〜59歳'; }
		elseif( $clean['age'] === "6" ){ echo '60歳〜'; } ?></p>
	</div>
	<div class="element_wrap">
		<label>お問い合わせ内容</label>
		<p><?php echo nl2br($clean['contact']); ?></p>　<!-- nl2br()関数で改行を反映 -->
	</div>
	<div class="element_wrap">
		<label>プライバシーポリシーに同意する</label>
		<p><?php if( $clean['agreement'] === "1" ){ echo '同意する'; }
		else{ echo '同意しない'; } ?></p>
	</div>

	<input type="submit" name="btn_back" value="戻る">
	<input type="submit" name="btn_submit" value="送信">

	<!-- nl2br()関数で改行を反映 -->
	<input type="hidden" name="your_name" value="<?php echo $clean['your_name']; ?>">
	<input type="hidden" name="email" value="<?php echo $clean['email']; ?>">
	<input type="hidden" name="gender" value="<?php echo $clean['gender']; ?>">
	<input type="hidden" name="age" value="<?php echo $clean['age']; ?>">
	<input type="hidden" name="contact" value="<?php echo $clean['contact']; ?>">
	<input type="hidden" name="agreement" value="<?php echo $clean['agreement']; ?>">

    </form>

<!-- 送信完了後　if文で表示を制御 -->
<?php elseif( $page_flag === 2 ): ?>

    <p>送信が完了しました。</p>

<!-- 初期表示　if文で表示を制御 -->
<?php else: ?>

	<!-- 必須項目に入力が無い場合、表示 -->
	<?php if( !empty($error) ): ?> <!-- $errorが空でなければ -->
	<ul class="error_list">
	<?php foreach( $error as $value ): ?> <!-- $error を $value に格納 -->
		<li><?php echo $value; ?></li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>

<form method="post" action="">
	<div class="element_wrap">
		<label class="required">氏名</label>
		<input type="text" name="your_name" value="<?php if( !empty($clean['your_name']) ){ echo $clean['your_name']; } ?>">
	</div>
	<div class="element_wrap">
		<label class="required">メールアドレス</label>
		<input type="text" name="email" value="<?php if( !empty($clean['email']) ){ echo $clean['email']; } ?>">
	</div>
	
	<div class="element_wrap">
		<label class="required">性別</label>
		<label for="gender_male"><input id="gender_male" type="radio" name="gender" value="male" <?php if( !empty($clean['gender']) && $clean['gender'] === "male" ){ echo 'checked'; } ?>>男性</label>
		<label for="gender_female"><input id="gender_female" type="radio" name="gender" value="female" <?php if( !empty($clean['gender']) && $clean['gender'] === "female" ){ echo 'checked'; } ?>>女性</label>
	</div>
	<div class="element_wrap">
		<label class="required">年齢</label>
		<select name="age">
			<option value="">選択してください</option>
			<option value="1" <?php if( !empty($clean['age']) && $clean['age'] === "1" ){ echo 'selected'; } ?>>〜19歳</option>
			<option value="2" <?php if( !empty($clean['age']) && $clean['age'] === "2" ){ echo 'selected'; } ?>>20歳〜29歳</option>
			<option value="3" <?php if( !empty($clean['age']) && $clean['age'] === "3" ){ echo 'selected'; } ?>>30歳〜39歳</option>
			<option value="4" <?php if( !empty($clean['age']) && $clean['age'] === "4" ){ echo 'selected'; } ?>>40歳〜49歳</option>
			<option value="5" <?php if( !empty($clean['age']) && $clean['age'] === "5" ){ echo 'selected'; } ?>>50歳〜59歳</option>
			<option value="6" <?php if( !empty($clean['age']) && $clean['age'] === "6" ){ echo 'selected'; } ?>>60歳〜</option>
		</select>
	</div>
	<div class="element_wrap">
		<label class="required">お問い合わせ内容</label>
		<textarea name="contact"><?php if( !empty($clean['contact']) ){ echo $clean['contact']; } ?></textarea>
	</div>
	<div class="element_wrap">
		<label class="required" for="agreement"><input id="agreement" type="checkbox" name="agreement" value="1" <?php if( !empty($clean['agreement']) && $clean['agreement'] === "1" ){ echo 'checked'; } ?>>プライバシーポリシーに同意する</label>
	</div>
	<input type="submit" name="btn_confirm" value="入力内容を確認する">
</form>

<?php endif; ?>
</body>
</html>