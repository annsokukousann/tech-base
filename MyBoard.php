<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>MyBoard</title>
        </head>
    <body>
<?php
    //データベースに接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//テーブルを作成
	$sql = "CREATE TABLE IF NOT EXISTS BulletinBoard"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "txt TEXT,"
    . "date TEXT,"
    . "editnum char(32),"
    . "editname char(32),"
    . "editcomment TEXT,"
    . "pass char(32)"
	.");";
	$stmt = $pdo->query($sql);
	
	if(!empty($_POST["name"])){
        $name = $_POST["name"];
    }
    if(!empty($_POST["txt"])){
        $txt = $_POST["txt"];
    }
    if(!empty($_POST["delete"])){
        $del = $_POST["delete"];
    }
    if(!empty($_POST["edit"])){
        $edit = $_POST["edit"];
    }
    if(!empty($_POST["passwords"])){
        $pass= $_POST["passwords"];
    }
    if(!empty($_POST["editnum"])){
        $editnum=$_POST["editnum"];

    }
    $date = date("Y/m/d H:i:s");

/*新規書き込み*/
    if(!empty($name) && !empty($txt) && empty($editnum) && !empty($pass)){
        $sql = $pdo -> prepare("INSERT INTO BulletinBoard (name,txt,date,pass) VALUES (:name,:txt,:date,:pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR); 
        $sql -> bindParam(':txt', $txt, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	    $sql -> execute();
            echo "書き込み成功！<br>";
    }
/*編集書き込み*/
    if(!empty($name) && !empty($txt) && !empty($editnum) && !empty($pass)){
        $sql = $pdo->prepare('UPDATE BulletinBoard SET name=:name,txt=:txt,date=:date,pass=:pass WHERE id=:editnum');
        $sql -> bindParam(':name', $name, PDO::PARAM_STR); 
        $sql -> bindParam(':txt', $txt, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	    $sql->bindParam(':editnum', $editnum, PDO::PARAM_INT);
	    $sql->execute();
        echo "書き変え成功！<br>";
    }
/*削除*/
    if(!empty($del) && !empty($pass)){            //「削除番号」あり
        $sql = $pdo->prepare('SELECT * FROM BulletinBoard WHERE id=:del');   //データベースから削除対象番号のデータを取ってくる
        $sql->bindParam(':del', $del, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $sql->execute();                             // ←SQLを実行する。
        $deldel = $sql->fetch(); 
            if($pass==$deldel["pass"]){    //送信したパスワードとデータベースから取ってきたパスワードが一致する
                $stmt = $pdo->prepare('DELETE FROM BulletinBoard WHERE id=:del ');
                $stmt->bindParam(':del', $del, PDO::PARAM_INT);
                $stmt->execute();
            }
    }

/*編集番号*/
    if(!empty($_POST["edit"]) && !empty($pass)){
        $sql = $pdo->prepare('SELECT * FROM BulletinBoard  WHERE id=:edit');
        $sql->bindParam(':edit', $edit, PDO::PARAM_INT); 
        $sql->execute();
        $edits=$sql->fetch();
        var_dump($edits);
        if($pass==$edits["pass"]){   
            $editnum=$edits['id'];
            $editname=$edits['name'];
            $editcomment=$edits['txt'];
            echo $editnum."<br>";
        }
    }
?>
        <form action=""method="post">
            <input type="text" name="name" placeholder="名前" value="<?php if(!empty($editname)){echo $editname;}?>"><br>
            <input type="text" name="txt" placeholder="コメント" value="<?php if(!empty($editcomment)){echo $editcomment;}?>">
            <input type="hidden" name="editnum" value="<?php if(!empty($editnum)){echo $editnum;}?>">
            <input type="password" name="passwords" placeholder="パスワード">
            <button type="submit">送信</button>
        </form>
        <form action=""method="post">
            <input type="text" name="delete" placeholder="削除対象番号">
            <input type="password" name="passwords" placeholder="パスワード">
            <button type="submit">削除</button>
        </form>
        <form action=""method="post">
            <input type="text" name="edit" placeholder="編集対象番号">
            <input type="password" name="passwords" placeholder="パスワード">
            <button type="submit">編集</button>
        </form>
        <?php

$sql = 'SELECT * FROM BulletinBoard';   //データベースからすべてのデータを取ってくる
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){ //1投稿ずつ
   //$rowの中にはテーブルのカラム名が入る
   //番号・名前・コメント・日時を表示する
 echo $row['id'].',';
 echo $row['name'].',';
 echo $row['txt'].'.';
 echo $row['date'].'<br>';
 echo "<hr>";
}

   ?>