<?php
// セッションを開始
session_start();
require '../config/config.php';  // config.php をインクルード
// $dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
// $user = 'root';
// $password = '';

try {
    // アカウント管理用のデータベースに接続
    $pdo = new PDO($dsnAccount, $userAccount, $passwordAccount);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_no = $_POST['account_no'] ?? '';
    $account_password = $_POST['account_password'] ?? '';

    // バリデーション: account_noが空でないか、そして半角数字で構成されているかを確認
    if (!empty($account_no) && ctype_digit($account_no)) {
        // パスワードのバリデーション: 半角英数字を含む8桁以上16桁以下
        if (!empty($account_password) && preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/', $account_password)) {
            try {
                // 従業員Noでユーザーを検索
                $sql = 'SELECT * FROM accounts WHERE account_no = :account_no';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_no', $account_no, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // パスワードの検証（パスワードが一致すればログイン成功）
                if ($user && password_verify($account_password, $user['account_password'])) {

                    // ユーザー情報をセッションに保存
                    $_SESSION['account']['no'] = $user['account_no'];
                    $_SESSION['account']['name'] = $user['account_name01'] . ' ' . $user['account_name02'];

                    // ユーザーの勤務区分をセッションに保存
                    $_SESSION['account']['workclass'] = $user['account_workclass'];  


                    // ログイン状態をセッションに保存
                    $_SESSION['auth'] = true;

                    // ログイン後、メインメニューにリダイレクト
                    header('Location: ../main_menu/index.php');
                    exit();
                } else {
                    $error_message = '従業員Noまたはパスワードが間違っています。';
                }
            } catch (PDOException $e) {
                $error_message = 'データベースエラーが発生しました: ' . $e->getMessage();
            }
        } else {
            $error_message = 'パスワードは半角英数字を含む8桁以上16桁以下で入力してください。';
        }
    } else {
        // バリデーションエラーメッセージ
        if (empty($account_no) || !ctype_digit($account_no)) {
            $error_message = '従業員Noは半角数字で入力してください。';
        } else {
            $error_message = '従業員Noとパスワードを入力してください。';
        }
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