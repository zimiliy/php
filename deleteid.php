<?php
session_start();
require('dbconnect.php');
if(isset($_SESSION['id'])){
    $id=$_SESSION['id'];
}

    
    $users = $db->prepare('SELECT * FROM members WHERE id=?');
    $users->execute(array($id));
    $user = $users->fetch();

    if($_SESSION['id']==$user['id']){
        $del=$db->prepare('DELETE FROM members WHERE id=?');
        $del->execute(array($id));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=], initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="index.css" />
</head>
<body>

    <div id="wrap">
  
    <!--ハンバーガー-->
<header>
    <div id="head">
        <img src="img/logo2.png" class="head-logo">
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

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="  crossorigin="anonymous"></script>
<script src="index.js"></script>

    
  <div id="content">
    <p>退会しました。</p>
    <a href="login.php">戻る</a>
    </div>
</div>

</body>
</html>