<?php
// セッションを開始
session_start();

// config.php をインクルードして、PDOインスタンスや定数を利用
require '../common/config.php';

// エラーメッセージの初期化
$error_message = '';

// POSTリクエストの場合にログイン処理を実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからの入力値を取得
    $account_no = $_POST['account_no'] ?? ''; // 従業員No
    $account_password = $_POST['account_password'] ?? ''; // パスワード

    // バリデーション: 従業員Noが空でなく、数字であることを確認
    if (!empty($account_no) && ctype_digit($account_no)) {
        // パスワードのバリデーション: 半角英数字を含む8桁以上16桁以下であることを確認
        if (!empty($account_password) && preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/', $account_password)) {
            try {
                // データベースから従業員Noに一致するアカウントを検索
                $sql = 'SELECT * FROM accounts WHERE account_no = :account_no';
                $stmt = $pdoAccount->prepare($sql);
                $stmt->bindValue(':account_no', $account_no, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // アカウントが存在し、パスワードが一致するかを確認
                if ($user && password_verify($account_password, $user['account_password'])) {
                    // ログイン成功：セッションに情報を保存
                    $_SESSION['account']['no'] = $user['account_no']; // 従業員No
                    $_SESSION['account']['name'] = $user['account_name01'] . ' ' . $user['account_name02']; // 名前
                    $_SESSION['account']['workclass'] = $user['account_workclass']; // 勤務区分
                    $_SESSION['auth'] = true; // 認証状態を保存

                    // ログイン後、メインメニューにリダイレクト
                    header('Location: ../main_menu/index.php');
                    exit(); // スクリプトを終了
                } else {
                    // アカウントが存在しない、またはパスワードが間違っている場合
                    $error_message = '従業員Noまたはパスワードが間違っています。';
                }
            } catch (PDOException $e) {
                // データベースエラー発生時の処理
                $error_message = 'データベースエラーが発生しました: ' . $e->getMessage();
            }
        } else {
            // パスワードのバリデーションエラー
            $error_message = 'パスワードは半角英数字を含む8桁以上16桁以下で入力してください。';
        }
    } else {
        // 従業員Noのバリデーションエラー
        $error_message = '従業員Noは半角数字で入力してください。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>ログイン</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <header>
            <div class="login_form_top"></div>
        </header>
        <form name="login_form" action="index.php" method="post">
            <p>No、パスワードをご入力の上、「ログイン」ボタンをクリックしてください</p>
            <?php if (!empty($error_message)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="login_form_btm">
                <input type="text" name="account_no" id="account_no" placeholder="Noを入力" required value="<?= htmlspecialchars($_POST['account_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="password" name="account_password" id="account_password" placeholder="パスワードを入力" required>
                <input type="submit" name="button" value="LOGIN">
            </div>
        </form>

    </body>
    
</html>