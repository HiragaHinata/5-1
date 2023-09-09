<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
    </head>
    <body>
    <?php
        //DB接続設定
        $dsn='mysql:dbname=DB名;host=localhost';
        $user='ユーザ名';
        $password='パスワード';
        $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        if($pdo!==false){
            //MySQLが繋がらなかったときの処理
            }else{echo"データベースの接続に失敗したよ";}
        //DB内にテーブルを設定
        $sql = "CREATE TABLE IF NOT EXISTS tbtest220(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name char(32) NOT NULL,
        comment TEXT NOT NULL,
        password varchar(20) NOT NULL,
        date DATETIME NOT NULL 
        )";
        $stmt = $pdo->query($sql);
    //ページにアクセスする際に使用されたリクエストのメソッド名(いらない？)
    if($_SERVER["REQUEST_METHOD"]=="POST"){
    //データを入力
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["npassword"]) && empty($_POST["editnew"]))
    {$name = $_POST["name"];
     $comment = $_POST["comment"];
     $date=date("Y/n/j G:i:s");
     $password=$_POST["npassword"];
     $sql = "INSERT INTO tbtest220 (name, comment, password, date) VALUES (:name, :comment, :password, :date)";
     $sql = $pdo->prepare($sql);
     $sql->bindParam(':name', $name, PDO::PARAM_STR);
     $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
     $sql->bindParam(':password', $password, PDO::PARAM_STR);
     $sql->bindParam(':date', $date, PDO::PARAM_STR);
     $sql->execute();}
    //else{echo "パスワードをいれてね";}
    //消すところ
    elseif(!empty($_POST["delete"]) && !empty($_POST["dellpass"])){
    $delete = $_POST["delete"];
    $dellpass=$_POST["dellpass"];
    $sql = 'delete from tbtest220 where id=:id AND :password';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
    $stmt->bindParam(':password', $dellpass, PDO::PARAM_STR);
    $stmt->execute();
    //消したよの表示（いらない？）
    if($stmt->rowCount()>0){echo "消せたよ<br>";}
    else{echo"消せなかったよ　番号とパスワードを見てみてね";}}
    //編集その１　名前とコメント
    elseif(!empty($_POST["edit"]) && !empty($_POST["editpass"]))
    {
    $editnum=$_POST["edit"];
    $editpass=$_POST["editpass"];
    $sql = 'SELECT * FROM tbtest220 WHERE id=:id AND password=:password';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $editnum, PDO::PARAM_INT);
    $stmt->bindParam(':password', $editpass, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
        $nname=$result["name"];
        $ncomment=$result["comment"];
        $epass=$result["password"];}
        else{"編集するデータがないよ<br>";}}
    //編集その２ 投稿する
    elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["editnew"])){
    $id = $_POST["editnew"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date=date("Y/n/j G:i:s");
    $pass=$_POST["npassword"];
    $sql = 'UPDATE tbtest220 SET name=:name, comment=:comment, date=:date, password=:password WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
    $stmt->execute();}}
    //投稿を見るところ
    $sql = 'SELECT * FROM tbtest220';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($result){
    foreach ($result as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
    echo "<br>";}
    }
    //DB接続を閉じる（これするとDBの中身が全部消える気がする）
    //$pdo=null;
?>
<form action=""method="post">
    <p>削除や編集する場合は番号だけを記入してね、いっぺんにはできないよ<br>

    <p>　　名前　　<input type="text" name="name" value="<?php if(!empty($nname)) echo $nname; ?>" placeholder="名前"></p>
    <p>　コメント　<input type="text" name="comment" value="<?php if(!empty($ncomment)) echo $ncomment; ?>" placeholder="コメント"></p>
    <p>パスワード　<input type="text" name="npassword" value="<?php if(!empty($epass)) echo $epass;?>" placeholder="パスワード"></p>
    <p><input type="submit" name="post" value="投稿"></p>

    
    <p>　編集する　<input type="number" name="edit" placeholder="番号をいれてね"></p>
    <p>パスワード　<input type="text" name="editpass" value="" placeholder="パスワードをいれてね"></p>
    <input type="hidden" name="editnew" value="<?php if (!empty($_POST["edit"])) echo $editnum; ?>">
    <p><input type="submit" name="submit" value="編集"></p>
    
    
    <p>　　消す　　<input type="number" name="delete" placeholder="削除対象番号"></p>
    <p>パスワード　<input type="text" name="dellpass" value="" placeholder="パスワードを入れてね"></p>
    <p><input type="submit" name="submit" value="削除"></p>

</form>
</body>
</html>