<?php
// db.php を読み込んで、データベースに接続する
require_once 'db.php';
// 関数 db_conn() を使って、$pdo にデータベース接続オブジェクトを代入
$pdo = db_conn();

// POSTデータ取得
$title = $_POST['title'] ?? '';
$poster = $_POST['poster'] ?? '';
$url = $_POST['url'] ?? '';
$review = $_POST['review'] ?? '';

// バリデーション
// 必須項目が1つでも空だったらエラーメッセージを表示して終了
if ($title === '' || $url === '' || $review === '' || $poster === '') {
    exit('すべての項目を入力してください');
}
// URLの形式が正しくない場合はエラーメッセージを表示して終了
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    exit('URLの形式が正しくありません');
}
// レビューが1000文字を超えていたらエラーメッセージを表示して終了
if (mb_strlen($review) > 1000) {
    exit('レビューは1000文字以内で入力してください');
}

// データ登録SQL作成
$sql = "INSERT INTO gs_kadai_table (title, poster, url, review) VALUES (:title, :poster, :url, :review)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':title', $title, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':poster', $poster, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':url', $url, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':review', $review, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //ここで実装する！

// データ登録処理後
if ($status == false) {
    // SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQLError" . $error[2]);
} else {
    // 正常に登録できた場合は、bookmark_list.phpへリダイレクト
    header("Location: bookmark_list.php");
    exit;
}
