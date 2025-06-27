<?php
// db.php を読み込んで、データベースに接続する
require_once 'db.php';
$pdo = db_conn();

// データ登録SQL作成
$sql = "SELECT * FROM gs_kadai_table";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// データ表示
$values = "";
if ($status == false) {
    // SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOVIE REVIEW REN.</title>
    <link href="./css/reset.css" rel="stylesheet" />
    <link href="./css/style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Gothic&display=swap" rel="stylesheet">
</head>

<body>
    <header class="site-header">
        <img src="./img/review-logo.png" alt="REVIEWロゴ" class="logo">
        <a href="index.php" class="back-link">← 映画検索に戻る</a>
    </header>

    <!-- 映画ブックマーク一覧の表示エリア -->
    <div class="content-area">
        <?php if (empty($values)): ?>
            <p class="info-message">まだブックマークがありません。映画検索ページからブックマークしてみましょう！</p>
        <?php else: ?>
            <h2>ブックマーク一覧</h2>
            <ul>
                <!-- 1件ずつループして表示 -->
                <?php foreach ($values as $row): ?>
                    <li class="movie-item">
                        <!-- 映画タイトル -->
                        <strong>「 <?= htmlspecialchars($row['title']) ?> 」</strong>
                        <!-- 映画ポスター ※登録されていない場合もある -->
                        <?php if (!empty($row['poster'])): ?>
                            <img src="<?= htmlspecialchars($row['poster']) ?>" alt="ポスター">
                        <?php endif; ?>
                        <!-- TMDbの公式ページへのリンク -->
                        <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" class="official-link">公式ページ</a>
                        <span class="review-label">【 感想 】</span>
                        <!-- 自分が入力したレビュー -->
                        <p class="review-text"><?= nl2br(htmlspecialchars($row['review'])) ?></p>
                        <!-- ブックマーク登録日 -->
                        <small class="created-at">登録日：<?= htmlspecialchars($row['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN.</p>
    </footer>
</body>

</html>