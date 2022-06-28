<?php
try {
$db = new PDO('mysql:dbname=zimiliy_minibbs;host=mysql1.php.starfree.ne.jp;charset=utf8','zimiliy_admin',"zhangmeng" );
} catch (PDOException $e) {
echo 'DB???儔乕:' . $e->getMessage();
}
?>