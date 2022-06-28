<?php
error_reporting(0);
session_start();
require('dbconnect.php');
if (empty($_REQUEST['id'])) {
	header('Location: index.php'); exit();
}
// 投稿を取得する
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
$posts->execute(array($_REQUEST['id']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>VIEW</title>
	<link rel="stylesheet" href="index.css" />
</head>

<body>
	
		<!--ハンバーガー-->
<header>
    <div id="head">
		<div id=imglogo><a href="top.html"><img src="img/logo2.png" class="head-logo"></a>
    </div>
        <div class="openbtn1"><span></span><span></span><span></span></div>
        <nav id="g-nav">
        <div id="g-nav-list">
             <ul>
						 <li><a href="top.html">TOPへ</a></li> 
                <li><a href="join/index">新規登録</a></li>  
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
			<p>&laquo;<a href="index.php">一覧にもどる</a></p>

			<?php
			if ($post = $posts->fetch()):
			?>
				<div class="msg">
				<!-- アイコン画像の表示 -->
				<?php
				  $ext1 = substr($post['picture'], -3);
				  if ($ext1 == 'jpg' || $ext1 == 'gif' || $ext1 == 'png' || $ext1 == 'jpeg' || $ext1 == 'JPG'):
		    ?>
				<img src="member_picture/<?php echo htmlspecialchars($post['picture'], ENT_QUOTES); ?>" width="48" height="auto" alt="<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>" />
				<?php
			    endif;
    		?>
        <!-- デフォルトアイコン画像の表示 -->
				<?php
						if ($ext1 != 'jpg' && $ext1 != 'gif'):
				?>
				<img src="member_picture/" width="48" height="48" alt="<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>" />
				<?php
						endif;
				?>

				<!-- 投稿されている画像 -->
				<?php
						$ext2 = substr($post['picture_post'], -3);
						if ($ext2 == 'jpg' || $ext2 =='gif' || $ext2 == 'png' || $ext2 == 'jpeg' || $ext2 == 'JPG'):
				?>
				<img src="post_picture/<?php echo htmlspecialchars($post['picture_post']); ?>" width="200" height="auto" alt="<?php echo htmlspecialchars($post['picture_post'], ENT_QUOTES); ?>" />
				<?php
						endif;
				?>

				<?php
						if ($ext2 != 'jpg' && $ext2 != 'gif' && $ext2 != 'png' && $ext2 != 'jpeg' && $ext2 != 'JPG'):
				?>
				<img src="post_picture/" width="200" height="200" alt="画像なし" />
				<?php
						endif;
				?>

					<p><?php echo htmlspecialchars($post['message'], ENT_QUOTES);
					?><span class="name">（<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>）</span></p>
					<p class="day"><?php echo htmlspecialchars($post['created'], ENT_QUOTES); ?></p>
				</div>
				<?php
			else:
				?>
				<p>その投稿は削除されたか、URLが間違えています</p>
				<?php
			endif;
			?>
		</div>
	</div>
</body>
</html>
