<?php
session_start();
// echo date('Y年m月d日 H時i分s秒');
require '../config/config.php';
// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // ログイン状態でない場合、ログインページにリダイレクト
    header('Location: ../login/index.php');
    exit();
}
$processedAccounts = [];
$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;

// function generateSelectOptions($options, $selectedValue = null) {
//     // 初期値のオプションを空の文字列として設定
//     $html = '<option value=""' . ($selectedValue === '' ? ' selected' : '') . '>選択</option>';
//     foreach ($options as $key => $value) {
//         $selected = ($key == $selectedValue) ? 'selected' : '';
//         $html .= "<option value=\"$key\" $selected>$value</option>";
//     }
//     return $html;
// }

// ログインユーザーの勤務区分を取得（勤務区分ごと更新などの制限をかけるため）
// $logged_in_workclass = $_SESSION['account']['workclass'] ?? 0;

if (isset($_POST['submit'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // アップデート用のSQL文
        $sql_update = '
            UPDATE accounts
    SET account_password = :account_password,
        account_no = :account_no,
        account_salesoffice = :account_salesoffice,
        account_kana01 = :account_kana01,
        account_kana02 = :account_kana02,
        account_name01 = :account_name01,
        account_name02 = :account_name02,
        account_birthday = :account_birthday,  -- 修正: ここを一つの account_birthday に統合
        account_license_expiration_date = :account_license_expiration_date,  -- 修正: ここも一つに統合
        account_department = :account_department,
        account_workclass = :account_workclass,
        account_classification = :account_classification,
        account_retirement_date = :account_retirement_date -- 修正: ここも一つに統合
    WHERE account_id = :account_id
';
        $stmt_update = $pdo->prepare($sql_update);
        
        // 値のバインド
        $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_INT); // 従業員id
$stmt_update->bindValue(':account_password', $_POST['account_password'], PDO::PARAM_STR); // パスワード
$stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_INT); // 従業員No
$stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属課
$stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ひらがな）
$stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ひらがな）
$stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
$stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
$stmt_update->bindValue(':account_birthday', $_POST['account_birthday'], PDO::PARAM_STR); // 修正: 生年月日（全体）
$stmt_update->bindValue(':account_license_expiration_date', $_POST['account_license_expiration_date'], PDO::PARAM_STR); // 修正: 免許証有効期限（全体）
$stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_INT); // 所属課
$stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_INT); // 職種区分
$stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_INT); // 勤務区分
$stmt_update->bindValue(':account_retirement_date', $_POST['account_retirement_date'], PDO::PARAM_STR); // 修正: 退職日（全体）
        
        $stmt_update->execute();
        
        $count = $stmt_update->rowCount();
        // $message = "授業員を{$count}件編集しました。";
        header("Location: register.php?message={$message}");
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}

try {
    $pdo = new PDO($dsnAccount, $userAccount, $passwordAccount);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 変数を先に初期化
    $account_department = isset($_GET['account_department']) ? $_GET['account_department'] : '0';
    $account_classification = isset($_GET['account_classification']) ? $_GET['account_classification'] : '0';
    $account_workclass = isset($_GET['account_workclass']) ? $_GET['account_workclass'] : '0';
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

    // 全従業員の集計
    $stmt = $pdo->query('SELECT COUNT(*) AS total_accounts FROM accounts');
    $total_people = $stmt->fetch(PDO::FETCH_ASSOC);
    // 乗務員の人数の集計
    $stmt = $pdo->query("SELECT COUNT(*) AS total_drivers FROM accounts WHERE account_department = '2'");
    $total_drivers = $stmt->fetch(PDO::FETCH_ASSOC);
    // ---- 乗務員の平均年齢の集計 ----
$stmt = $pdo->query('SELECT account_birthday FROM accounts WHERE account_department = 2');
$birthdates = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAge = 0;
$numOfEmployees = count($birthdates);

foreach ($birthdates as $account) {
    $birthDateTime = new DateTime($account['account_birthday']);
    $currentDateTime = new DateTime();
    $age = $currentDateTime->diff($birthDateTime)->y;
    $totalAge += $age;
}
$averageAge = $numOfEmployees > 0 ? $totalAge / $numOfEmployees : 0;
// ---- 乗務員の平均年齢集計コードの終了 ----

    // ---- ソート機能のコード ----
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'account_no';
    $order = isset($_GET['order']) ? $_GET['order'] : 'asc';

    $sort_columns = ['age', 'service', 'account_no', 'license_expiry', 'registration_date', 'updated_at'];
    if (!in_array($sort, $sort_columns)) {
        $sort = 'account_no';
    }

    $order = ($order === 'desc') ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM accounts WHERE CONCAT(account_kana01, account_kana02) LIKE :keyword";

if ($account_department != '0') {
    $sql .= ' AND account_department = :account_department';
}
if ($account_classification != '0') {
    $sql .= ' AND account_classification = :account_classification';
}
if ($account_workclass != '0') {
    $sql .= ' AND account_workclass = :account_workclass';
}

if ($sort === 'age') {
    $sql .= " ORDER BY account_birthday $order";
} elseif ($sort === 'service') {
    $sql .= " ORDER BY account_employment_date $order";
} elseif ($sort === 'license_expiry') {
    $sql .= " ORDER BY account_license_expiration_date $order";
} elseif ($sort === 'registration_date') {
    $sql .= " ORDER BY registration_date $order";
} elseif ($sort === 'updated_at') {
    $sql .= " ORDER BY updated_at $order";
} else {
    $sql .= " ORDER BY account_no $order";
}
    // ---- ソート機能のコード終了 ----

    // ---- キーワード（なまえ）・所属課・職種区分・勤務区分、各種の検索機能、開始 ----
    $stmt = $pdo->prepare($sql);
    $partial_match = "%{$keyword}%";
    $stmt->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
    if ($account_department != '0') {
        $stmt->bindValue(':account_department', $account_department, PDO::PARAM_INT);
    }
    if ($account_classification != '0') {
        $stmt->bindValue(':account_classification', $account_classification, PDO::PARAM_INT);
    }
    if ($account_workclass != '0') {
        $stmt->bindValue(':account_workclass', $account_workclass, PDO::PARAM_INT);
    }

    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // 検索条件をクエリパラメータに追加(検索条件をかけた後に、各項目の昇順、降順を押しても、全件表示に戻らせない)
    $query_string = http_build_query([
        'keyword' => isset($_GET['keyword']) ? $_GET['keyword'] : '',
        'account_department' => isset($_GET['account_department']) ? $_GET['account_department'] : '0',
        'account_classification' => isset($_GET['account_classification']) ? $_GET['account_classification'] : '0',
        'account_workclass' => isset($_GET['account_workclass']) ? $_GET['account_workclass'] : '0',
        'account_retirement' => isset($_GET['account_retirement']) ? $_GET['account_retirement'] : ''
    ]);
    // ---- 検索機能のコード終了 ----

    foreach ($accounts as $account) {
        // 誕生日のフォーマット
        $birthDateTime = new DateTime($account['account_birthday']);
        $currentDateTime = new DateTime();
    
        // 登録日と更新日のフォーマット
        $registration_date = isset($account['registration_date']) ? (new DateTime($account['registration_date']))->format('Y年m月d日') : '日付なし';
        $updated_at = isset($account['updated_at']) ? (new DateTime($account['updated_at']))->format('Y年m月d日') : '日付なし';
    
        // 免許証有効期限のフォーマット
        $account_deadline = htmlspecialchars((new DateTime($account['account_license_expiration_date']))->format('Y年m月d日'), ENT_QUOTES, 'UTF-8');
    
        // 年齢の計算
        $ageYears = $currentDateTime->diff($birthDateTime)->y;
        $ageMonths = $currentDateTime->diff($birthDateTime)->m;
    
        // 勤続年数の計算
        $employmentDateTime = new DateTime($account['account_employment_date']);
        $serviceYears = $currentDateTime->diff($employmentDateTime)->y;
        $serviceMonths = $currentDateTime->diff($employmentDateTime)->m;
    
        // データのエスケープ
        $account_no = htmlspecialchars($account['account_no'], ENT_QUOTES, 'UTF-8');
        $account_kana = htmlspecialchars($account['account_kana01'] . '　' . $account['account_kana02'], ENT_QUOTES, 'UTF-8');
        $account_name = htmlspecialchars($account['account_name01'] . '　' . $account['account_name02'], ENT_QUOTES, 'UTF-8');
        $account_department = htmlspecialchars(ACCOUNT_DEPARTMENT[$account['account_department']], ENT_QUOTES, 'UTF-8');
        $account_classification = htmlspecialchars(ACCOUNT_CLASSIFICATION[$account['account_classification']], ENT_QUOTES, 'UTF-8');
        $account_workclass = htmlspecialchars(ACCOUNT_WORKCLASS[$account['account_workclass']], ENT_QUOTES, 'UTF-8');
    
        // 詳細（編集）ページへのリンク生成
        $detail_link = "update.php?account_id=" . htmlspecialchars($account['account_id'], ENT_QUOTES, 'UTF-8');
    
        // データを$processedAccountsに格納し、htmlに表示させる
        $processedAccounts[] = [
            'account_no' => $account_no,
            'account_kana' => $account_kana,
            'account_name' => $account_name,
            'account_department' => $account_department,
            'account_classification' => $account_classification,
            'account_workclass' => $account_workclass,
            'account_deadline' => $account_deadline,
            'age' => "$ageYears 歳 $ageMonths ヶ月",
            'service' => "$serviceYears 年 $serviceMonths ヶ月",
            'registration_date' => $registration_date,
            'updated_at' => $updated_at,
            'detail_link' => $detail_link
        ];
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}      
?>

<!DOCTYPE html>
<html lang="ja">
<form method="GET">
    <head>
        <meta charset="utf-8">
        <!-- <link rel="stylesheet" href="../assets/css/reset.css">
        <link rel="stylesheet" href="../assets/css/style.css"> -->
        <link rel="stylesheet" href="list.css">

        <title>従業員一覧</title>
    </head>
    <body>
        <header>
            <h1>従業員一覧</h1>
                <!-- パンクズナビ -->
                 <nav>
                <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="../main_menu/index.php">
                            <span itemprop="name">メインメニュー</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                </ol>
                </nav>
        </header>

        <main>
            <table class="info">
                <?php
                 // 全従業員・乗務員（外勤）の人数・乗務員（外勤）の平均年齢の結果を表示
                    echo '<tr>';
                    echo '<th>全従業員人数</th>';
                    echo '<td>' . $total_people['total_accounts'] . '名' . '</td>';
                    echo '<th>乗務員者数</th>';
                    echo '<td>' . $total_drivers['total_drivers'] . '名</td>';
                    echo '<th>乗務員平均年齢</th>';
                    echo '<td>' . round($averageAge, 1) . '歳</td>';           
                    echo '</tr>';
                ?>
            </table>
            <!-- 役員または管理者のみが「新規登録」ボタンを表示 -->
            <?php if (isset($logged_in_workclass) && ($logged_in_workclass === 1 || $logged_in_workclass === 2)): ?>
                <div class="insert">
                    <a href="register.php" class="btn1">新規登録</a>
                </div>
            <?php endif; ?>
            <!-- <div class="insert">
                <a href="register.php" class="btn1">新規登録</a>
            </div> -->
           
                <!-- // （従業員の登録・編集・削除後）messageパラメータの値を受け取っていれば、それを表示する -->
          
<?php if (isset($_GET['message'])): ?>
    <div class="success-message">
        <?= htmlspecialchars(urldecode($_GET['message']), ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
                <form>
                    <table class="search">
                        <tr>
                            <th colspan="5">検索</th>
                        </tr>
                        <tr>
                            <th>氏名（ひらがな）</th>
                            <th>所属課</th>
                            <th>職種区分</th>
                            <th>勤務区分</th>
                            <th>退職者</th>
                        </tr>
                        <tr>
                            <td><form action="" method="get" class="search-form">
                                <input type="text" placeholder="ふりがなで検索" name="keyword">
                            </td>
                            <td>
                                <select name="account_department">
                                    <option value="0">選択</option>
                                    <option value="1">内勤</option>
                                    <option value="2">外勤</option>
                                </select>
                            </td>
                            <!-- <td>
                                <select name="account_department">
                                    <?= generateSelectOptions(ACCOUNT_DEPARTMENT, $_GET['account_department'] ?? '0'); ?>
                                </select>
                            </td> -->
                            <td><select name="account_classification">
                                    <option value="0">選択</option>
                                    <option value="1">正社員</option>
                                    <option value="2">準正社員</option>
                                    <option value="3">嘱託</option>
                                </select>
                            <!-- <td>
                                <select name="account_classification">
                                    <?= generateSelectOptions(ACCOUNT_CLASSIFICATION, $_GET['account_classification'] ?? '0'); ?>
                                </select>
                            </td> -->
                            <td>
                                <select name="account_workclass">
                                    <option value="0">選択</option>
                                    <option value="1">役員</option>
                                    <option value="2">管理者</option>
                                    <option value="3">事務員</option>
                                    <option value="4">整備士</option>
                                    <option value="5">配車係</option>
                                    <option value="6">乗務A</option>
                                    <option value="7">乗務B</option>
                                    <option value="8">乗務C</option>
                                    <option value="9">乗務D</option>
                                    <option value="10">乗務E</option>
                                    <option value="11">乗務F</option>
                                    <option value="12">乗務G</option>
                                    <option value="13">乗務H</option>
                                </select>
                            </td>
                            <!-- <td>
                                <select name="account_workclass">
                                    <?= generateSelectOptions(ACCOUNT_WORKCLASS, $_GET['account_workclass'] ?? '0'); ?> 
                                </select>
                            </td>  -->
                            <td>
                                <select name="account_retirement">
                                    <option value="">選択</option>
                                    <?php foreach ($retiredAccounts as $retired): ?>
                                        <option value="<?= htmlspecialchars($retired['account_id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($retired['account_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?> 
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
            <div class="btn_area">
                <button>検索</button>
                <button>クリア</button>
                <button type="button" onclick="window.print()">印刷</button>
            </div>
            <div class="print-area">
                <table class="list">
                    <tr>
                        <th></th>
                        <th class="account_no">番号
                            <a href="?<?= $query_string ?>&sort=account_no&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=account_no&order=desc">▼</a>
                        </th>
                        <th class="account_kana01">氏名</th>
                        <th>年齢 
                            <a href="?<?= $query_string ?>&sort=age&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=age&order=desc">▼</a>
                        </th>
                        <th>勤続年数
                            <a href="?<?= $query_string ?>&sort=service&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=service&order=desc">▼</a>
                        </th>
                        <th class="deadline">免許証有効期限
                            <a href="?<?= $query_string ?>&sort=license_expiry&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=license_expiry&order=desc">▼</a>
                        </th>
                        <th>所属課</th>
                        <th>職種区分</th>
                        <th>勤務区分</th>
                        <th class="registration_date">登録年月日
                            <a href="?<?= $query_string ?>&sort=registration_date&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=registration_date&order=desc">▼</a>
                        </th>
                        <th class="updated_at">更新年月日
                            <a href="?<?= $query_string ?>&sort=updated_at&order=asc">▲</a>
                            <a href="?<?= $query_string ?>&sort=updated_at&order=desc">▼</a>
                        </th>
                    </tr>
                    <?php foreach ($processedAccounts as $account): ?>
                        <tr>
                            <td><a href="<?= $account['detail_link'] ?>"class="button">詳細</a></td>
                            <td><?= $account['account_no'] ?> <!-- 従業員番号の表示 --> 
                            <td><?= $account['account_kana'] ?><br><?= $account['account_name'] ?></td> <!-- 氏名（ひらがな・漢字）の表示 -->
                            <td><?= $account['age'] ?></td> <!-- 年齢の表示 -->
                            <td><?= $account['service'] ?></td> <!-- 勤続年数の表示 -->
                            <td><?= $account['account_deadline'] ?></td> <!-- 免許証有効期限の表示 -->
                            <td><?= $account['account_department'] ?></td> <!-- 所属課の表示 -->
                            <td><?= $account['account_classification'] ?></td> <!-- 職種区分の表示 -->
                            <td><?= $account['account_workclass'] ?></td> <!-- 勤務区分の表示 -->
                            <td><?= $account['registration_date'] ?></td> <!-- 登録年月日の表示 -->
                            <td><?= $account['updated_at'] ?></td> <!-- 更新年月日の表示 -->  
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>
    </body>
</html>