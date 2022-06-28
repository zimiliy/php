<?php error_reporting(0);?>

<?php
require('../dbconnect.php');

session_start();

if (!empty($_POST)) {
	// エラー項目の確認
	if ($_POST['name'] == '') {
		$error['name'] = 'blank';
	}
	if ($_POST['email'] == ''|| !preg_match('/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*(\.[\w-]{2,})$/', $_POST['email'])) {
		$error['email'] = 'blank';
	}
	if (strlen($_POST['password']) < 6) {
		$error['password'] = 'length';
	}
	if ($_POST['password'] == '') {
		$error['password'] = 'blank';
	}
	$fileName = $_FILES['image']['name'];
	if (!empty($fileName)) {
		$ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext != 'gif') {
			$error['image'] = 'type';
		}
	}
	
	// 重複アカウントのチェック
	if (empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['email'] = 'duplicate';
		}
	}

	if (empty($error)) {
		// 画像をアップロードする
		$image = date('YmdHis') . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
		
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		header('Location: check.php'); exit();
	}
}

// 書き直し
if ($_REQUEST['action'] == 'rewrite') {
	$_POST = $_SESSION['join'];
	$error['rewite'] = true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../index.css" />

	<title>JOIN</title>

	
</head>
<!--ハンバーガー-->
<header>
    <div id="head">
		<div id=imglogo><a href="../top.html"><img src="../img/logo2.png" class="head-logo"></a>
    </div>
        <div class="openbtn1"><span></span><span></span><span></span></div>
        <nav id="g-nav">
        <div id="g-nav-list">
             <ul>
						 <li><a href="../top.html">TOPへ</a></li> 
                <li><a href="index.php">新規登録</a></li>  
                <li><a href="../login.php">ログイン</a></li>  
				<li><a href="../logout.php">ログアウト</a></li>
                <li><a href="../deleteidcheck.php">退会をご希望の方</a></li>
								<li><a href="https://suzuri.jp/lolocott">Goods</a></li>
            </ul>
        </div>
        </nav>
        </div>
    </div>
  </header>
<body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="  crossorigin="anonymous"></script>
<script src="../index.js"></script>
<body>
<div id="wrap">
<div id="content">
  <div id="login_title">MEMBER SHIP</div>
  <hr><br>


<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); ?>" />
        	<?php if ($error['name'] == 'blank'): ?>
			<p class="error">* ニックネームを入力してください</p>
			<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); ?>" />
        	<?php if ($error['email'] == 'blank'): ?>
			<p class="error">* メールアドレスを正しく入力してください</p>
            <?php endif; ?>
        	<?php if ($error['email'] == 'duplicate'): ?>
			<p class="error">* 指定されたメールアドレスはすでに登録されています</p>
			<?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'); ?>" />
        	<?php if ($error['password'] == 'blank'): ?>
			<p class="error">* パスワードを入力してください</p>
			<?php endif; ?>
        	<?php if ($error['password'] == 'length'): ?>
			<p class="error">* パスワードは6文字以上で入力してください</p>
			<?php endif; ?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
        	<?php if ($error['image'] == 'type'): ?>
			<p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
			<?php endif; ?>
        	<?php if (!empty($error)): ?>
			<p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
			<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
					
</div>
</div>
</body>
</html>
