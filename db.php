<?php
// エラー表示
ini_set("display_errors", 1); // エラー表示設定

// .envファイルの読み込み
require_once __DIR__ . '/load_env.php';
$envFile = ($_SERVER['SERVER_NAME'] === 'localhost')
    ? __DIR__ . '/.env.local'
    : __DIR__ . '/.env.production';
loadEnv($envFile);

function db_conn()
{
    // 環境変数からDB接続情報を取得
    $db_name = getenv('DB_NAME');
    $db_id   = getenv('DB_ID');
    $db_pw   = getenv('DB_PW');
    $db_host = getenv('DB_HOST');

    try {
        $pdo = new PDO("mysql:dbname={$db_name};charset=utf8;host={$db_host}", $db_id, $db_pw);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}
