<?
// 環境変数を読み込む関数が定義されているファイルを読み込む
require_once 'load_env.php';
// サーバー名（localhost or さくらサーバーなど）によって使用する .env ファイルを切り替える
$envFile = ($_SERVER['SERVER_NAME'] === 'localhost')
    ? __DIR__ . '/.env.local' // ローカル環境用の.envファイル
    : __DIR__ . '/.env.production'; // 本番サーバー用の.envファイル
// 選択された.envファイルの中身を読み込んで、環境変数に設定する
loadEnv($envFile);

// APIキーを使う
$apiKey = getenv('TMDB_API_KEY');

// 情報メッセージとエラーメッセージを入れる変数を準備
$info_message = '';
$error_message = '';

// 検索結果を入れるための箱を用意
$results = [];

// ▼ 入力されたクエリ（検索キーワード）をチェック！！ ▼
if (!isset($_GET['query'])) {
    // 検索フォームがまだ送信されていない時（最初の画面表示時）
    $info_message = '上記に検索したい映画タイトルを入力してください。';
} elseif ($_GET['query'] === '') {
    // 空文字で検索した時
    $error_message = '検索ワードを入力してください。';
} else {
    // 検索キーワードがちゃんと送られてきた時（TMDbで実際に検索する）
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$apiKey}&language=ja-JP&query={$query}&include_adult=false";
    $json = file_get_contents($url);
    //取得した映画情報のjsonを「$data」に
    $data = json_decode($json, true);

    // 「$data」に映画の検索結果が含まれているか確認
    if (isset($data['results'])) {
        $results = $data['results'];
        // 検索ワードに映画がヒットしなかった時
        if (empty($results)) {
            $error_message = '該当する映画が見つかりませんでした。検索ワードを変えてみてください。';
        }
    } else {
        // APIから正しくデータを取れなかった時
        $error_message = '映画情報の取得に失敗しました。';
    }
}
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
        <a href="bookmark_list.php" class="back-link">ブックマーク一覧へ →</a>
    </header>
    <!-- 映画検索フォーム -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="query" placeholder="映画タイトルを入力してください" required>
        <button type="submit">検索</button>
    </form>
    <!-- 検索結果＆メッセージ表示エリア -->
    <div class="content-area">
        <!-- メッセージがある場合に表示 -->
        <?php if (!empty($info_message)): ?>
            <p class="info-message"><?= htmlspecialchars($info_message) ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="info-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <!-- 検索結果がある場合に表示 -->
        <?php if (!empty($results)): ?>
            <h2>検索結果</h2>
            <ul>
                <?php foreach ($results as $movie): ?>
                    <li class="movie-item">
                        <!-- 映画タイトル -->
                        <strong>「 <?= htmlspecialchars($movie['title']) ?> 」</strong>
                        <!-- 映画ポスター ※登録されていない場合もある -->
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w200<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>のポスター">
                        <?php endif; ?>
                        <span>公開日：<?= htmlspecialchars($movie['release_date'] ?? '不明') ?></span>
                        <span>平均評価点：<?= htmlspecialchars($movie['vote_average'] ?? 'なし') ?></span>
                        <p class="overview">あらすじ：<?= nl2br(htmlspecialchars($movie['overview'] ?? '情報なし')) ?></p>
                        <!-- TMDbの公式ページへのリンク -->
                        <a href="https://www.themoviedb.org/movie/<?= htmlspecialchars($movie['id']) ?>" target="_blank" class="official-link">公式ページを見る</a>

                        <!-- 感想を入力してブックマークするフォーム -->
                        <form method="POST" action="save.php" class="bookmark-form">
                            <input type="hidden" name="title" value="<?= htmlspecialchars($movie['title']) ?>">
                            <input type="hidden" name="poster" value="https://image.tmdb.org/t/p/w200<?= htmlspecialchars($movie['poster_path']) ?>">
                            <input type="hidden" name="url" value="https://www.themoviedb.org/movie/<?= htmlspecialchars($movie['id']) ?>">
                            <textarea name="review" placeholder="映画の感想を書く" required></textarea>
                            <button type="submit">映画をブックマーク</button>
                        </form>

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