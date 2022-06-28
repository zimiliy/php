<?php
 error_reporting(0);?>

<?php
session_start();
require('dbconnect.php');

// 画像投稿機能拡張
if (!empty($_FILES['image'])) {
	// 投稿画像ファイルの拡張子チェック
	$fileName = $_FILES['image']['name'];
	if (!empty($fileName)) {
		$ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' && $ext != 'JPG' && $ext != 'jpeg') {
			$error['image'] = 'type';
		}
	}
	if (empty($error)) {
		// 画像をアップロードする
		$image = date('YmdHis') . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], 'post_picture/' .$image);

	}
}

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
	// ログインしている
	$_SESSION['time'] = time();

	$members = $db->prepare('SELECT * FROM members WHERE id=?');
	$members->execute(array($_SESSION['id']));
	$member = $members->fetch();
} else {
	// ログインしていない
	header('Location: login.php');
	 exit();
}

// 投稿を記録する
if (!empty($_POST)) {
	if ($_POST['message'] != '') {
	if($_POST['reply_post_id'] != ''){
		$message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?,picture_POST=?, created=NOW()');
		$message->execute(array(
			$member['id'],
			$_POST['message'],
			$_POST['reply_post_id'],
			$image
		));

		header('Location: index.php'); exit();
	}else {
		$message = $db->prepare('INSERT INTO posts SET member_id=?, message=?,picture_post=?,created=NOW()');
		$message->execute(array(
		  $member['id'],
				  $_POST['message'],
				  $image  //画像投稿機能拡張に伴い追加
			  ));
		  header('Location: index.php'); exit();
	  }
}
}

// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
	$page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
	$response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
	$response->execute(array($_REQUEST['res']));

	$table = $response->fetch();
	$message = '@' . $table['name'] . ' ' . $table['message'];
}

// htmlspecialcharsのショートカット
function h($value) {
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定します
function makeLink($value) {
	return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>' , $value);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="index.css" />
<title>岐阜のおすすめSPOT掲示板</title>
</head>

<body>
  <!--ハンバーガー-->
<header>
    <div id="head">
        <a href="top.html"><img src="img/logo2.png" class="head-logo"></a>
        <div class="openbtn1"><span></span><span></span><span></span></div>
        <nav id="g-nav">
        <div id="g-nav-list">
             <ul>
						 <li><a href="top.html">TOPへ</a></li> 
                <li><a href="join/index.php">新規登録</a></li>  
                <li><a href="login.php">ログイン</a></li>  
				<li><a href="logout.php">ログアウト</a></li>
                <li><a href="deleteidcheck.php">退会をご希望の方</a></li>
								<li><a href="https://suzuri.jp/lolocott">Goods</a></li>
            </ul>
        </div>
        </nav>
        </div>
    </div>
  </header>
<body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="  crossorigin="anonymous"></script>
<script src="index.js"></script>
<div id="wrap">

<div id="content">
  	<div id="keijiban_title">岐阜のおすすめSPOT掲示板</div>
  <hr><br>
    <form action="" method="post" enctype="multipart/form-data">
      <dl>
			
        <dt style="text-align:center;" ><?php echo h($member['name']); ?>さん岐阜のおすすめSPOT教えてください♪</dt>
        <dd style="text-align:center;">
          <textarea name="message" cols="50" rows="5"><?php echo h($message,ENT_QUOTES); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res'],ENT_QUOTES); ?>" />
        </dd>
      </dl>
	  
<!-- 画像アップロード機能追加 -->

      <div>
        <p>
		<input type="file" name="image" size="35"   />
        	<?php if ($error['post_image'] == 'type'): ?>
			<p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
			<?php endif; ?>
        	<?php if (!empty($error)): ?>
			<p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
			<?php endif; ?>
			<input type="submit" value="投稿する" />
        </p>
		
      </div>
    </form>

<?php
foreach ($posts as $post):
?>
    <div class="msg">
    <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
   

<!--画像アップロード-->
<?php
	  $ext2 = substr($post['picture_post'], -3);
	  		if($ext2 =='jpg' || $ext2 =='gif'|| $ext2=='png'|| $ext2=='JPG'|| $ext2=='jpeg'):
	  ?>

	  <img src="post_picture/<?php echo h($post['picture_post']); ?>" width="250" height="200" alt="<?php echo h($post['picture_post'], ENT_QUOTES); ?>" />
	  <?php
	  endif;
	   ?>
	   
	   <?php
	   if($ext2 != 'jpg' && $ext2 !='gif' && $ext2 !='png' && $ext2 &&'JPG' && $ext2 !='jpeg'):
		?>
		<img src="post_picture/" width="48" height="48" alt="画像なし" />

		<?php
	   endif;
	   ?>
	    <p><?php echo makeLink(h($post['message'])); ?><span class="name">(<?php echo h($post['name']); ?>)</span>
	[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
    <p class="day"><a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
		<?php
if ($post['reply_post_id'] > 0):
?>
<a href="view.php?id=<?php echo
h($post['reply_post_id']); ?>">
返信元のメッセージ</a>
<?php
endif;
?>
<?php
if ($_SESSION['id'] == $post['member_id']):
?>
[<a href="delete.php?id=<?php echo h($post['id']); ?>"
style="color: #F33;">削除</a>]
<?php
endif;
?>
    </p>
    </div>
<?php
endforeach;
?>

<ul class="paging">
<?php
if ($page > 1) {
?>
<li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php
} else {
?>
<li>前のページへ</li>
<?php
}
?>
<?php
if ($page < $maxPage) {
?>
<li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php
} else {
?>
<li>次のページへ</li>
<?php
}
?>
</ul>
  </div>
</div>

</body>
</html>
