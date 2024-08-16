<!-- config.php -->
<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

const ACCOUNT_SALESOFFICE = [1 =>'本社営業所', 2 =>'向敷地営業所'];
const ACCOUNT_JENDA = [1 =>'男', 2 =>'女'];
const ACCOUNT_BLOODTYPE = [1 =>'A型', 2 =>'B型', 3 =>'O型', 4 =>'AB型'];
const ACCOUNT_DEPARTMENT = [1 =>'内勤', 2 =>'外勤'];
const ACCOUNT_WORKCLASS = [1 =>'役員', 2 =>'管理者', 3 => '事務員', 4 =>'整備士', 5 =>'配車係', 6 =>'乗務A', 7 =>'乗務B', 8 =>'乗務C', 9 =>'乗務D',
                           10 =>'乗務E', 11 =>'乗務F', 12 =>'乗務G', 13 =>'乗務H'];
const ACCOUNT_CLASSIFICATION = [1 =>'正社員', 2 =>'準社員', 3 => '嘱託'];                         
const ACCOUNT_ENROLLMENT = [1 => '本採用', 2 => '中途採用', 3 => '退職'];


function generateSelectOptions($optionsArray, $selectedValue = '') {
    $html = '<option value="">選択</option>';
    foreach ($optionsArray as $value => $label) {
        $isSelected = ($value == $selectedValue) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $isSelected . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    return $html;
}
?>


<!-- list.php -->
<?php
require 'config.php';
$processedAccounts = [];

if (isset($_POST['submit'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // アップデート用のSQL文
        $sql_update = '
            UPDATE accounts
            SET account_id = :account_id,
                account_password = :account_password,
                account_no = :account_no,
                account_salesoffice = :account_salesoffice,
                account_kana01 = :account_kana01,
                account_kana02 = :account_kana02,
                account_name01 = :account_name01,
                account_name02 = :account_name02,
                account_birthday_year = :account_birthday_year,
                account_birthday_month = :account_birthday_month,
                account_birthday_day = :account_birthday_day,
                account_license_expiration_date_year = :account_license_expiration_date_year,
                account_license_expiration_date_month = :account_license_expiration_date_month,
                account_license_expiration_date_day = :account_license_expiration_date_day,
                account_department = :account_department,
                account_workclass = :account_workclass,
                account_classification = :account_classification,
                account_retirement_year = :account_retirement_year,
                account_retirement_month = :account_retirement_month,
                account_retirement_day = :account_retirement_day
            WHERE account_id = :account_id
        ';
        $stmt_update = $pdo->prepare($sql_update);
        
        // 値のバインド
        $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_STR); // 従業員id
        $stmt_update->bindValue(':account_password', $_POST['account_password'], PDO::PARAM_STR); // パスワード
        $stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR); // 従業員No
        $stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属課
        $stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ひらがな）
        $stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ひらがな）
        $stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
        $stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
        $stmt_update->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_INT); // 誕生日（年）
        $stmt_update->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_INT); // 誕生日（月）
        $stmt_update->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_INT); // 誕生日（日）
        $stmt_update->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_INT); // 免許証有効期限（年）
        $stmt_update->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_INT); // 免許証有効期限（月）
        $stmt_update->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_INT); // 免許証有効期限（日）
        $stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_INT); // 所属課
        $stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_INT); // 職種区分
        $stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_INT); // 勤務区分
        $stmt_update->bindValue(':account_retirement_year', !empty($_POST['account_retirement_year']) ? $_POST['account_retirement_year'] : null, PDO::PARAM_INT); // 退職日（年）
        $stmt_update->bindValue(':account_retirement_month', !empty($_POST['account_retirement_month']) ? $_POST['account_retirement_month'] : null, PDO::PARAM_INT); // 退職日（月）
        $stmt_update->bindValue(':account_retirement_day', !empty($_POST['account_retirement_day']) ? $_POST['account_retirement_day'] : null, PDO::PARAM_INT); // 退職日（日）
        
        $stmt_update->execute();
        
        $count = $stmt_update->rowCount();
        // $message = "授業員を{$count}件編集しました。";
        header("Location: register.php?message={$message}");
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}

try {
    $pdo = new PDO($dsn, $user, $password);

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
    $stmt = $pdo->query('SELECT account_birthday_year, account_birthday_month, account_birthday_day FROM accounts WHERE account_department = 2');
    $birthdates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAge = 0;
    $numOfEmployees = count($birthdates);

    foreach ($birthdates as $account) {
        $birthDate = $account['account_birthday_year'] . '-' . $account['account_birthday_month'] . '-' . $account['account_birthday_day'];
        $birthDateTime = new DateTime($birthDate);
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
        $sql .= " ORDER BY account_birthday_year $order, account_birthday_month $order, account_birthday_day $order";
    } elseif ($sort === 'service') {
        $sql .= " ORDER BY account_employment_year $order, account_employment_month $order, account_employment_day $order";
    } elseif ($sort === 'license_expiry') {
        $sql .= " ORDER BY account_license_expiration_date_year $order, account_license_expiration_date_month $order, account_license_expiration_date_day $order";
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
        // 登録日と更新日のフォーマット
        $registration_date = isset($account['registration_date']) ? (new DateTime($account['registration_date']))->format('Y年m月d日') : '日付なし';
        $updated_at = isset($account['updated_at']) ? (new DateTime($account['updated_at']))->format('Y年m月d日') : '日付なし';
        // 免許証有効期限のフォーマット
        $account_deadline = htmlspecialchars($account['account_license_expiration_date_year'] . '年' . $account['account_license_expiration_date_month'] . '月' . $account['account_license_expiration_date_day'] . '日', ENT_QUOTES, 'UTF-8');
        // 年齢の計算
        $birthDate = $account['account_birthday_year'] . '-' . $account['account_birthday_month'] . '-' . $account['account_birthday_day'];
        $birthDateTime = new DateTime($birthDate);
        $currentDateTime = new DateTime();
        $ageYears = $currentDateTime->diff($birthDateTime)->y;
        $ageMonths = $currentDateTime->diff($birthDateTime)->m;
        // 勤続年数の計算
        $employmentDate = $account['account_employment_year'] . '-' . $account['account_employment_month'] . '-' . $account['account_employment_day'];
        $employmentDateTime = new DateTime($employmentDate);
        $serviceYears = $currentDateTime->diff($employmentDateTime)->y;
        $serviceMonths = $currentDateTime->diff($employmentDateTime)->m;
        // データのエスケープ
        $account_no = htmlspecialchars($account['account_no'], ENT_QUOTES, 'UTF-8');
        $account_kana = htmlspecialchars($account['account_kana01'] .'　'. $account['account_kana02'], ENT_QUOTES, 'UTF-8');
        $account_name = htmlspecialchars($account['account_name01'] .'　'. $account['account_name02'], ENT_QUOTES, 'UTF-8');
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
            <div class="insert">
                <a href="register.php" class="btn1">新規登録</a>
            </div>
            <?php
                // （従業員の登録・編集・削除後）messageパラメータの値を受け取っていれば、それを表示する
                if (isset($_GET['message'])) {
                     echo "<p class='success'>{$_GET['message']}</p>";
                }  
            ?>
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
                            <td><form action="where-list.php" method="get" class="search-form">
                                <input type="text" placeholder="ふりがなで検索" name="keyword">
                            </form>
                            </td>
                            <td><select name="account_department">
                                    <option value="0">選択</option>
                                    <option value="1">内勤</option>
                                    <option value="2">外勤</option>
                                </select>
                            </td>
                            <td><select name="account_classification">
                                    <option value="0">選択</option>
                                    <option value="1">正社員</option>
                                    <option value="2">準正社員</option>
                                    <option value="3">嘱託</option>
                                </select>
                            </td>
                            <td><select name="account_workclass">
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
                <!-- <div class="btn_area"> -->
                    <button type="button" onclick="window.print()">印刷</button>
                <!-- </div> -->
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



<!-- update.php -->
<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';
$errors = []; // エラーメッセージを格納する配列
require 'config.php';  // config.php をインクルード
echo date('Y-m-d H:i:s');

// データベース接続
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // アカウントIDが指定されている場合、アカウント情報を取得
    if (isset($_GET['account_id'])) {
        $sql = 'SELECT * FROM accounts WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // validate() 関数を定義
    function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
        if (empty($input) || !preg_match($pattern, $input)) {
            $errors[$fieldName] = $errorMessage;
        }
    }

    // フォームが送信された場合、アカウント情報を更新
    if (isset($_POST['submit'])) {

        // バリデーションパターンの定義
        $patterns = [
            'half_width_numeric' => '/^\d+$/',
            'hiragana' => '/^[ぁ-んー　]+$/u',
            'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
            'address_kanji_hiragana_english' => '/^[\p{Han}\p{Hiragana}A-Z\d!@#$%^&*()\-_=+{};:,<.>]+$/u',
            'address_kanji_hiragana_katakana_english' => '/^[\p{Han}\p{Hiragana}\p{Katakana}A-Za-z0-9!@#$%^&*()\-_=+{};:,<.>]+$/u',
        ];

        // ---- バリデーション処理 ----
        // パスワードバリテーション（空の場合、バリテーションをスキップ）
        if (!empty($_POST['account_password'])) {
            validate($_POST['account_password'], $patterns['password'], '半角英数字を含む8桁以上16桁以下で入力してください。', $errors, 'account_password');
        }

        // 従業員Noバリテーション
        validate($_POST['account_no'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $errors, 'account_no');
        validate($_POST['account_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana01');
        validate($_POST['account_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana02');
        // account_no が既に存在するか確認 (更新時、現在のアカウントIDは除外)
        if (!isset($errors['account_no']) && !empty($_POST['account_no'])) {
            try {
                $sql = 'SELECT COUNT(*) FROM accounts WHERE account_no = :account_no AND account_id != :account_id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR);
                $stmt->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $errors['account_no'] = 'この従業員Noは既に存在します。別の番号を入力してください。';
                }
            } catch (PDOException $e) {
                $errors['account_no'] = 'データベースエラーが発生しました: ' . $e->getMessage();
            }
        }

        // 郵便番号バリデーション
        $zip_errors = [];
        validate($_POST['account_zipcord01'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord01');
        validate($_POST['account_zipcord02'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord02');
        if (!empty($zip_errors)) {
            $errors['account_zipcord'] = implode('<br>', $zip_errors);
        }

        // 住所バリデーション（account_address03は、空の場合、バリテーションをスキップ）
        validate($_POST['account_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_address02');
        if (!empty($_POST['account_address03'])) {
            validate($_POST['account_address03'], $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_address03');
        }
        
        // 電話番号バリデーション（空の場合、バリテーションをスキップ）
        $tel2_errors = [];
        if (!empty($_POST['account_tel01'])) {
            validate($_POST['account_tel01'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel01');
        }
        if (!empty($_POST['account_tel02'])) {
            validate($_POST['account_tel02'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel02');
        }
        if (!empty($_POST['account_tel03'])) {
            validate($_POST['account_tel03'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel03');
        }
        if (!empty($tel2_errors)) {
            $errors['account_tel1'] = '半角数字のみで入力してください。';
        }

        // 連絡先2のバリデーション（空の場合、バリテーションをスキップ）
        $tel2_errors = [];
        if (!empty($_POST['account_tel04'])) {
            validate($_POST['account_tel04'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel04');
        }
        if (!empty($_POST['account_tel05'])) {
            validate($_POST['account_tel05'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel05');
        }
        if (!empty($_POST['account_tel06'])) {
            validate($_POST['account_tel06'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel06');
        }
        if (!empty($tel2_errors)) {
            $errors['account_tel2'] = '半角数字のみで入力してください。';
        }

        // 保証人氏名（ひらがな）バリデーション（空の場合は、バリテーションをスキップ）
        if (!empty($_POST['account_guarentor_kana01'])) {
            validate($_POST['account_guarentor_kana01'], $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana01');
        }

        if (!empty($_POST['account_guarentor_kana02'])) {
            validate($_POST['account_guarentor_kana02'], $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana02');
        }

        // 保証人の郵便番号バリデーション（空の場合は、バリテーションをスキップ）
        $guarentor_zip_errors = [];
        if (!empty($_POST['account_guarentor_zipcord01']) || !empty($_POST['account_guarentor_zipcord02'])) {
            if (!empty($_POST['account_guarentor_zipcord01'])) {
                validate($_POST['account_guarentor_zipcord01'], $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord01');
            }
            if (!empty($_POST['account_guarentor_zipcord02'])) {
                validate($_POST['account_guarentor_zipcord02'], $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord02');
            }
            if (!empty($guarentor_zip_errors)) {
                $errors['account_guarentor_zipcord'] = implode('<br>', $guarentor_zip_errors);
            }
        }

        // 保証人の住所バリデーション（空の場合は、バリテーションをスキップ）
        if (!empty($_POST['account_guarentor_address02'])) {
            validate($_POST['account_guarentor_address02'], $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_guarentor_address02');
        }
        if (!empty($_POST['account_guarentor_address03'])) {
            validate($_POST['account_guarentor_address03'], $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_guarentor_address03');
        }

        // 保証人の連絡先1バリデーション（空の場合、バリテーションをスキップ）
        $guarentor_tel_errors = [];

        if (!empty($_POST['account_guarentor_tel01'])) {
            validate($_POST['account_guarentor_tel01'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel01');
        }

        if (!empty($_POST['account_guarentor_tel02'])) {
            validate($_POST['account_guarentor_tel02'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel02');
        }

        if (!empty($_POST['account_guarentor_tel03'])) {
            validate($_POST['account_guarentor_tel03'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel03');
        }

        if (!empty($guarentor_tel_errors)) {
            $errors['account_guarentor_tel'] = '半角数字のみで入力してください。';
        }

        // 保証人の連絡先2バリデーション（空の場合、バリテーションをスキップ）
        $guarentor_tel2_errors = [];
        if (!empty($_POST['account_guarentor_tel04'])) {
            validate($_POST['account_guarentor_tel04'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel04');
        }
        if (!empty($_POST['account_guarentor_tel05'])) {
            validate($_POST['account_guarentor_tel05'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel05');
        }
        if (!empty($_POST['account_guarentor_tel06'])) {
            validate($_POST['account_guarentor_tel06'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel06');
        }
        if (!empty($guarentor_tel2_errors)) {
            $errors['account_guarentor_tel2'] = '半角数字のみで入力してください。';
        }

        // パスワードが入力されている場合、ハッシュ化する
        if (!empty($_POST['account_password'])) {
            $hashed_password = password_hash($_POST['account_password'], PASSWORD_DEFAULT);
        } else {
            // パスワードが空の場合、既存のパスワードを使用
            $hashed_password = $account['account_password'];
        }

        // ---- バリテーション処理コードの終了----

        // すべてのエラーチェックが終わった後、エラーがなければデータベースを更新
        if (empty($errors)) {
            $sql_update = '
            UPDATE accounts
            SET account_password = :account_password, account_no = :account_no, account_salesoffice = :account_salesoffice, account_kana01 = :account_kana01, account_kana02 = :account_kana02,
                account_name01 = :account_name01, account_name02 = :account_name02, account_birthday_year = :account_birthday_year, account_birthday_month = :account_birthday_month, account_birthday_day = :account_birthday_day,
                account_jenda = :account_jenda, account_bloodtype = :account_bloodtype, account_zipcord01 = :account_zipcord01, account_zipcord02 = :account_zipcord02,
                account_pref = :account_pref, account_address01 = :account_address01, account_address02 = :account_address02, account_address03 = :account_address03,
                account_tel01 = :account_tel01, account_tel02 = :account_tel02, account_tel03 = :account_tel03, account_tel04 = :account_tel04, account_tel05 = :account_tel05, account_tel06 = :account_tel06,   
                account_license_expiration_date_year = :account_license_expiration_date_year, account_license_expiration_date_month = :account_license_expiration_date_month, account_license_expiration_date_day = :account_license_expiration_date_day,
                account_guarentor_kana01 = :account_guarentor_kana01, account_guarentor_kana02 = :account_guarentor_kana02, account_guarentor_name01 = :account_guarentor_name01, account_guarentor_name02 = :account_guarentor_name02,
                account_relationship = :account_relationship, account_guarentor_zipcord01 = :account_guarentor_zipcord01, account_guarentor_zipcord02 = :account_guarentor_zipcord02,
                account_guarentor_pref = :account_guarentor_pref, account_guarentor_address01 = :account_guarentor_address01, account_guarentor_address02 = :account_guarentor_address02, account_guarentor_address03 = :account_guarentor_address03,
                account_guarentor_tel01 = :account_guarentor_tel01, account_guarentor_tel02 = :account_guarentor_tel02, account_guarentor_tel03 = :account_guarentor_tel03, account_guarentor_tel04 = :account_guarentor_tel04, account_guarentor_tel05 = :account_guarentor_tel05, account_guarentor_tel06 = :account_guarentor_tel06,
                account_department = :account_department, account_workclass = :account_workclass, account_classification = :account_classification, account_enrollment = :account_enrollment,
                account_employment_year = :account_employment_year, account_employment_month = :account_employment_month, account_employment_day = :account_employment_day,
                account_appointment_year = :account_appointment_year, account_appointment_month = :account_appointment_month, account_appointment_day = :account_appointment_day,
                account_retirement_year = :account_retirement_year, account_retirement_month = :account_retirement_month, account_retirement_day = :account_retirement_day
            WHERE account_id = :account_id
            ';
            $stmt_update = $pdo->prepare($sql_update);

            // Bind values
            $stmt_update->bindValue(':account_password', $hashed_password, PDO::PARAM_STR); // パスワード
            $stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR); // 従業員No
            $stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属営業所
            $stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ふりがな）
            $stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ふりがな）
            $stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
            $stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
            $stmt_update->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_STR); // 生年月日（年）
            $stmt_update->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_STR); // 生年月日（月）
            $stmt_update->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_STR); // 生年月日（日）
            $stmt_update->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_STR); // 性別
            $stmt_update->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_STR); // 血液型
            $stmt_update->bindValue(':account_zipcord01', $_POST['account_zipcord01'], PDO::PARAM_STR); // 郵便番号（前半）
            $stmt_update->bindValue(':account_zipcord02', $_POST['account_zipcord02'], PDO::PARAM_STR); // 郵便番号（後半）
            $stmt_update->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR); // 都道府県
            $stmt_update->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR); // 市町村区
            $stmt_update->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR); // 町名番地
            $stmt_update->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR); // マンション名など
            $stmt_update->bindValue(':account_tel01', $_POST['account_tel01'], PDO::PARAM_STR); // 連絡先1（前半）
            $stmt_update->bindValue(':account_tel02', $_POST['account_tel02'], PDO::PARAM_STR); // 連絡先1（中）
            $stmt_update->bindValue(':account_tel03', $_POST['account_tel03'], PDO::PARAM_STR); // 連絡先1（後半）
            $stmt_update->bindValue(':account_tel04', $_POST['account_tel04'], PDO::PARAM_STR); // 連絡先2（前半）
            $stmt_update->bindValue(':account_tel05', $_POST['account_tel05'], PDO::PARAM_STR); // 連絡先2（中）
            $stmt_update->bindValue(':account_tel06', $_POST['account_tel06'], PDO::PARAM_STR); // 連絡先2（後半）
            $stmt_update->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_STR); // 免許証有効期限（年）
            $stmt_update->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_STR); // 免許証有効期限（月）
            $stmt_update->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_STR); // 免許証有効期限（日）
            $stmt_update->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR); // 身元保証人氏（ふりがな）
            $stmt_update->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR); // 身元保証人名（ふりがな）
            $stmt_update->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR); // 身元保証人氏（漢字）
            $stmt_update->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR); // 身元保証人名（漢字）
            $stmt_update->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR); // 続柄
            $stmt_update->bindValue(':account_guarentor_zipcord01', $_POST['account_guarentor_zipcord01'], PDO::PARAM_STR); // 身元保証人郵便番号（前半）
            $stmt_update->bindValue(':account_guarentor_zipcord02', $_POST['account_guarentor_zipcord02'], PDO::PARAM_STR); // 身元保証人郵便番号（後半）
            $stmt_update->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR); // 身元保証人都道府県
            $stmt_update->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR); // 身元保証人市町村区
            $stmt_update->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR); // 身元保証人町名番地
            $stmt_update->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR); // 身元保証人マンション名など
            $stmt_update->bindValue(':account_guarentor_tel01', $_POST['account_guarentor_tel01'], PDO::PARAM_STR); // 身元保証人連絡先1（前半）
            $stmt_update->bindValue(':account_guarentor_tel02', $_POST['account_guarentor_tel02'], PDO::PARAM_STR); // 身元保証人連絡先1（中）
            $stmt_update->bindValue(':account_guarentor_tel03', $_POST['account_guarentor_tel03'], PDO::PARAM_STR); // 身元保証人連絡先1（後半）
            $stmt_update->bindValue(':account_guarentor_tel04', $_POST['account_guarentor_tel04'], PDO::PARAM_STR); // 身元保証人連絡先2（前半）
            $stmt_update->bindValue(':account_guarentor_tel05', $_POST['account_guarentor_tel05'], PDO::PARAM_STR); // 身元保証人連絡先2（中）
            $stmt_update->bindValue(':account_guarentor_tel06', $_POST['account_guarentor_tel06'], PDO::PARAM_STR); // 身元保証人連絡先2（後半）
            $stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_STR); // 所属課
            $stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_STR); // 勤務区分
            $stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_STR); // 職種区分
            $stmt_update->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_STR); // 在籍区分
            $stmt_update->bindValue(':account_employment_year', $_POST['account_employment_year'], PDO::PARAM_STR); // 雇用年月日（年）
            $stmt_update->bindValue(':account_employment_month', $_POST['account_employment_month'], PDO::PARAM_STR); // 雇用年月日（月）
            $stmt_update->bindValue(':account_employment_day', $_POST['account_employment_day'], PDO::PARAM_STR); // 雇用年月日（日）
            $stmt_update->bindValue(':account_appointment_year', $_POST['account_appointment_year'], PDO::PARAM_STR); // 選任年月日（年）
            $stmt_update->bindValue(':account_appointment_month', $_POST['account_appointment_month'], PDO::PARAM_STR); // 選任年月日（月）
            $stmt_update->bindValue(':account_appointment_day', $_POST['account_appointment_day'], PDO::PARAM_STR); // 選任年月日（日）
            $stmt_update->bindValue(':account_retirement_year', $_POST['account_retirement_year'], PDO::PARAM_STR); // 退職年月日（年）
            $stmt_update->bindValue(':account_retirement_month', $_POST['account_retirement_month'], PDO::PARAM_STR); // 退職年月日（月）
            $stmt_update->bindValue(':account_retirement_day', $_POST['account_retirement_day'], PDO::PARAM_STR); // 退職年月日（日）
            $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_INT); 
            
            // SQL文を実行する
            $stmt_update->execute();

            // 更新した件数を取得する
            $message = "従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に編集されました。";
            header("Location: list.php?message=" . urlencode($message));
            exit();
        }
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>従業員編集</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
        <script src="https://yubinbango.github.io/yubinbango/yubinbango.js"></script>
    </head>

    <header>
        <h1>従業員編集</h1>
        <!-- パンクズナビ -->
        <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="../main_menu/index.php">
                  <span itemprop="name">メインメニュー</span>
              </a>
              <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="list.php">
                  <span itemprop="name">従業員一覧</span>
              </a>
              <meta itemprop="position" content="2" />
            </li>
        </ol>
    </header>

    <body>
        <form action="" method="POST">
            <input type="hidden" name="account_id" value="<?= htmlspecialchars($account['account_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="first-table">
                    <tr>
                        <th>パスワード</th>
                        <td>
                            <input type="password" name="account_password" placeholder="パスワードを入力"
                                value="<?= htmlspecialchars($_POST['account_password'] ?? $account['account_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_password'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_password'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>従業員No</th>
                        <td>
                            <input type="text" name="account_no" placeholder="1111" 
                                value="<?= htmlspecialchars($_POST['account_no'] ?? $account['account_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_no'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>所属営業所</th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE, $account['account_salesoffice'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" 
                                value="<?= htmlspecialchars($_POST['account_kana01'] ?? $account['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" 
                                value="<?= htmlspecialchars($_POST['account_kana02'] ?? $account['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_name01" 
                                value="<?= htmlspecialchars($_POST['account_name01'] ?? $account['account_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>名（漢字）</th>
                        <td><input type="text" class="text" placeholder="一番" name="account_name02" 
                                value="<?= htmlspecialchars($_POST['account_name02'] ?? $account['account_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>生年月日</th>
                        <td colspan="2">
                            <select name="account_birthday_year">
                                <?php
                                    $startYear = 1939;  
                                    $endYear = date("Y") - 25;  // 現在の年から25年前を終了年に設定
                                    $selectedYear = $_POST['account_birthday_year'] ?? $account['account_birthday_year'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_birthday_month">
                                <?php
                                    $selectedMonth = $_POST['account_birthday_month'] ?? $account['account_birthday_month'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月  
                            <select name="account_birthday_day">
                                <?php
                                    $selectedDay = $_POST['account_birthday_day'] ?? $account['account_birthday_day'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>   
                    <tr>
                        <th>性別</th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA, $_POST['account_jenda'] ?? $account['account_jenda'] ?? '') ?>
                            </select>
                        </td>
                        <th>血液型</th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE, $_POST['account_bloodtype'] ?? $account['account_bloodtype'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="420" 
                                value="<?= htmlspecialchars($_POST['account_zipcord01'] ?? $account['account_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="0042" 
                                value="<?= htmlspecialchars($_POST['account_zipcord02'] ?? $account['account_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_pref" 
                                value="<?= htmlspecialchars($_POST['account_pref'] ?? $account['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_address01" 
                                value="<?= htmlspecialchars($_POST['account_address01'] ?? $account['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_address02" placeholder="駒形通2丁目2-25" 
                                value="<?= htmlspecialchars($_POST['account_address02'] ?? $account['account_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_address03" placeholder="辰巳マンション1111号室" 
                                value="<?= htmlspecialchars($_POST['account_address03'] ?? $account['account_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address03'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_tel01'] ?? $account['account_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel02'] ?? $account['account_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel03'] ?? $account['account_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel04" size="4" maxlength="4" name="account_tel04" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_tel04'] ?? $account['account_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel05" size="4" maxlength="4" name="account_tel05" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel05'] ?? $account['account_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel06" size="4" maxlength="4" name="account_tel06" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel06'] ?? $account['account_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限</th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $startYear = 2024;  
                                    $endYear = date('Y') + 10;  // 現在の年から10年後を終了年に設定(自動的に10年更新される)
                                    $selectedYear = $_POST['account_license_expiration_date_year'] ?? $account['account_license_expiration_date_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?php
                                    $selectedMonth = $_POST['account_license_expiration_date_month'] ?? $account['account_license_expiration_date_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?php
                                    $selectedDay = $_POST['account_license_expiration_date_day'] ?? $account['account_license_expiration_date_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>    
                </table>
            </div>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="second-table">
                    <tr>
                        <th>身元保証人<br>氏（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_guarentor_kana01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_kana01'] ?? $account['account_guarentor_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>身元保証人<br>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="おやじ" name="account_guarentor_kana02" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_kana02'] ?? $account['account_guarentor_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>身元保証人<br>氏（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_guarentor_name01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_name01'] ?? $account['account_guarentor_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>身元保証人<br>名（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="親父" name="account_guarentor_name02" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_name02'] ?? $account['account_guarentor_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>続柄</th>
                        <td>
                            <input type="text" class="text" placeholder="父" name="account_relationship" 
                                value="<?= htmlspecialchars($_POST['account_relationship'] ?? $account['account_relationship'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_guarentor_zipcord01" placeholder="420" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord01'] ?? $account['account_guarentor_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_guarentor_zipcord02" placeholder="0042" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord02'] ?? $account['account_guarentor_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['account_guarentor_zipcord'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_guarentor_pref" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_pref'] ?? $account['account_guarentor_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_guarentor_address01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address01'] ?? $account['account_guarentor_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_guarentor_address02" placeholder="駒形通2丁目2-25" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address02'] ?? $account['account_guarentor_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">                            <?php if (isset($errors['account_guarentor_address02'])): ?>
                                    <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_guarentor_address03" placeholder="辰巳マンション1111号室" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address03'] ?? $account['account_guarentor_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address03'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_guarentor_tel01" size="4" maxlength="4" name="account_guarentor_tel01" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel01'] ?? $account['account_guarentor_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel02" size="4" maxlength="4" name="account_guarentor_tel02" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel02'] ?? $account['account_guarentor_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel03" size="4" maxlength="4" name="account_guarentor_tel03" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel03'] ?? $account['account_guarentor_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel04" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel04'] ?? $account['account_guarentor_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel05" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel05'] ?? $account['account_guarentor_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel06" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel06'] ?? $account['account_guarentor_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel2'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="third-table">
                    <tr>
                        <th>所属課</th>
                        <td>
                            <select name="account_department">
                                <?= generateSelectOptions(ACCOUNT_DEPARTMENT, $_POST['account_department'] ?? $account['account_department'] ?? '') ?>
                            </select>
                        </td>
                        <th>勤務区分</th>
                        <td>
                            <select name="account_workclass">
                                <?= generateSelectOptions(ACCOUNT_WORKCLASS, $_POST['account_workclass'] ?? $account['account_workclass'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>職種区分</th>
                        <td>
                            <select name="account_classification">
                                <?= generateSelectOptions(ACCOUNT_CLASSIFICATION, $_POST['account_classification'] ?? $account['account_classification'] ?? '') ?>
                            </select>
                        </td>
                        <th>在籍区分</th>
                        <td>
                            <select name="account_enrollment">
                                <?= generateSelectOptions(ACCOUNT_ENROLLMENT, $_POST['account_enrollment'] ?? $account['account_enrollment'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>雇用年月日</th>
                        <td colspan="2">
                            <select name="account_employment_year">
                                <?php
                                    $startYear = 1985;  // 開始年
                                    $endYear = date('Y') + 1;  // 終了年を現在の年から1年後に設定(自動的に1年追加される)
                                    $selectedYear = $_POST['account_employment_year'] ?? $account['account_employment_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_employment_month">
                                <?php
                                    $selectedMonth = $_POST['account_employment_month'] ?? $account['account_employment_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_employment_day">
                                <?php
                                    $selectedDay = $_POST['account_employment_day'] ?? $account['account_employment_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>
                    <tr>
                        <th>選任年月日</th>
                        <td colspan="2">
                            <select name="account_appointment_year">
                                <?php
                                    $startYear = 1985;  // 開始年
                                    $endYear = date('Y') + 1;  // 現在の年から1年後までを終了年に設定(自動的に1年追加される)
                                    $selectedYear = $_POST['account_appointment_year'] ?? $account['account_appointment_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_appointment_month">
                                <?php
                                    $selectedMonth = $_POST['account_appointment_month'] ?? $account['account_appointment_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_appointment_day">
                                <?php
                                 $selectedDay = $_POST['account_appointment_day'] ?? $account['account_appointment_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日 
                        </td>
                    </tr>
                    <tr>
                        <th>退職年月日</th>
                        <td colspan="2">
                            <select name="account_retirement_year">
                                <?php
                                    $startYear = 2020;  // 開始年
                                    $endYear = date('Y') + 1;  // 現在の年から1年後までを終了年に設定(自動的に1年追加される)
                                    $selectedYear = $_POST['account_retirement_year'] ?? $account['account_retirement_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_retirement_month">
                                <?php
                                    $selectedMonth = $_POST['account_retirement_month'] ?? $account['account_retirement_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_retirement_day">
                                <?php
                                    $selectedDay = $_POST['account_retirement_day'] ?? $account['account_retirement_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>                        
                </table>
            </div>    

            <div table class="flex">
                <input type="submit" name="submit" value="更新">
            </div>
        </form>

    </body>
    
</html>html>



<!-- register.php -->
<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';
$errors = []; // エラーメッセージを格納する配列
require 'config.php';  // config.php をインクルード
// date_default_timezone_set('Asia/Tokyo');
echo date('Y-m-d H:i:s');
                
// バリデーション関数
function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
    if (!empty($input) && !preg_match($pattern, $input)) {
        $errors[$fieldName] = $errorMessage;
    }
}
// データベース接続
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

// POSTリクエストがある場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // バリデーションパターンの定義
    $patterns = [
        'half_width_numeric' => '/^\d+$/',
        'hiragana' => '/^[ぁ-んー　]+$/u',
        'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
        'address_kanji_hiragana_english' => '/^[\p{Han}\p{Hiragana}A-Z\d!@#$%^&*()\-_=+{};:,<.>]+$/u',
        'address_kanji_hiragana_katakana_english' => '/^[\p{Han}\p{Hiragana}\p{Katakana}A-Za-z0-9!@#$%^&*()\-_=+{};:,<.>]+$/u',
    ];

    // バリデーション処理
    validate($_POST['account_password'] ?? '', $patterns['password'], '半角英数字を含む8桁以上16桁以下で入力してください。', $errors, 'account_password');
    validate($_POST['account_no'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $errors, 'account_no');
    validate($_POST['account_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana01');
    validate($_POST['account_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana02');

    // 必須項目のバリデーション
    if (empty($_POST['account_no'])) {
        $errors['account_no'] = '従業員Noは必須です。';
    }

    // account_no が既に存在するか確認
    if (!isset($errors['account_no']) && !empty($_POST['account_no'])) {
        try {
            $sql = 'SELECT COUNT(*) FROM accounts WHERE account_no = :account_no';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors['account_no'] = 'この従業員Noは既に存在します。別の番号を入力してください。';
            }
        } catch (PDOException $e) {
            $errors['account_no'] = 'データベースエラーが発生しました: ' . $e->getMessage();
        }
    }

    // 郵便番号バリデーション
    $zip_errors = [];
    validate($_POST['account_zipcord01'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord01');
    validate($_POST['account_zipcord02'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord02');
    if (!empty($zip_errors)) {
        $errors['account_zipcord'] = implode('<br>', $zip_errors);
    }

    // 住所バリデーション
    validate($_POST['account_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_address02');
    validate($_POST['account_address03'] ?? '', $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_address03');

    // 電話番号バリデーション
    $tel_errors = [];
    validate($_POST['account_tel01'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel01');
    validate($_POST['account_tel02'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel02');
    validate($_POST['account_tel03'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel03');
    if (!empty($tel_errors)) {
        $errors['account_tel'] = '半角数字のみで入力してください。';
    }

    // 連絡先2のバリデーション
    $tel2_errors = [];
    validate($_POST['account_tel04'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel04');
    validate($_POST['account_tel05'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel05');
    validate($_POST['account_tel06'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel06');
    if (!empty($tel2_errors)) {
        $errors['account_tel2'] = '半角数字のみで入力してください。';
    }

    // 保証人の情報バリデーション
    validate($_POST['account_guarentor_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana01');
    validate($_POST['account_guarentor_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana02');

    // 保証人の郵便番号バリデーション
    $guarentor_zip_errors = [];
    validate($_POST['account_guarentor_zipcord01'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord01');
    validate($_POST['account_guarentor_zipcord02'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord02');
    if (!empty($guarentor_zip_errors)) {
        $errors['account_guarentor_zipcord'] = implode('<br>', $guarentor_zip_errors);
    }

    // 保証人の住所バリデーション
    validate($_POST['account_guarentor_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_guarentor_address02');
    validate($_POST['account_guarentor_address03'] ?? '', $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_guarentor_address03');

    // 保証人の連絡先バリデーション
    $guarentor_tel_errors = [];
    validate($_POST['account_guarentor_tel01'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel01');
    validate($_POST['account_guarentor_tel02'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel02');
    validate($_POST['account_guarentor_tel03'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel03');
    if (!empty($guarentor_tel_errors)) {
        $errors['account_guarentor_tel'] = '半角数字のみで入力してください。';
    }

    // 保証人の連絡先2バリデーション
    $guarentor_tel2_errors = [];
    validate($_POST['account_guarentor_tel04'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel04');
    validate($_POST['account_guarentor_tel05'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel05');
    validate($_POST['account_guarentor_tel06'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel06');
    if (!empty($guarentor_tel2_errors)) {
        $errors['account_guarentor_tel2'] = '半角数字のみで入力してください。';
    }
    
    

    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                var_dump($_POST); // ここで送信されたデータを確認する
                

// パスワードをハッシュ化して保存
$hashed_password = password_hash($_POST['account_password'], PASSWORD_DEFAULT);


            $sql  = '
                INSERT INTO accounts (account_password, account_no, account_salesoffice, account_kana01, account_kana02, account_name01, account_name02,
                                      account_birthday_year, account_birthday_month, account_birthday_day, account_jenda, account_bloodtype, 
                                      account_zipcord01, account_zipcord02, account_pref, account_address01, account_address02, account_address03,
                                      account_tel01, account_tel02, account_tel03, account_tel04, account_tel05, account_tel06,
                                      account_license_expiration_date_year, account_license_expiration_date_month, account_license_expiration_date_day,
                                      account_guarentor_kana01, account_guarentor_kana02, account_guarentor_name01, account_guarentor_name02, account_relationship,
                                      account_guarentor_zipcord01, account_guarentor_zipcord02, account_guarentor_pref, account_guarentor_address01, account_guarentor_address02, account_guarentor_address03,
                                      account_guarentor_tel01, account_guarentor_tel02, account_guarentor_tel03, account_guarentor_tel04, account_guarentor_tel05, account_guarentor_tel06,
                                      account_department, account_workclass, account_classification, account_enrollment,  
                                      account_employment_year, account_employment_month, account_employment_day, account_appointment_year, account_appointment_month, account_appointment_day,    
                                      account_retirement_year, account_retirement_month, account_retirement_day)
                VALUES (:account_password, :account_no, :account_salesoffice, :account_kana01, :account_kana02, :account_name01, :account_name02,
                        :account_birthday_year, :account_birthday_month, :account_birthday_day, :account_jenda, :account_bloodtype,       
                        :account_zipcord01, :account_zipcord02, :account_pref, :account_address01, :account_address02, :account_address03,
                        :account_tel01, :account_tel02, :account_tel03, :account_tel04, :account_tel05, :account_tel06,
                        :account_license_expiration_date_year, :account_license_expiration_date_month, :account_license_expiration_date_day,
                        :account_guarentor_kana01, :account_guarentor_kana02, :account_guarentor_name01, :account_guarentor_name02, :account_relationship,
                        :account_guarentor_zipcord01, :account_guarentor_zipcord02, :account_guarentor_pref, :account_guarentor_address01, :account_guarentor_address02, :account_guarentor_address03,
                        :account_guarentor_tel01, :account_guarentor_tel02, :account_guarentor_tel03, :account_guarentor_tel04, :account_guarentor_tel05, :account_guarentor_tel06,
                        :account_department, :account_workclass, :account_classification, :account_enrollment,  
                        :account_employment_year, :account_employment_month, :account_employment_day, :account_appointment_year, :account_appointment_month, :account_appointment_day,    
                        :account_retirement_year, :account_retirement_month, :account_retirement_day)
            ';
            $stmt = $pdo->prepare($sql);

            // Bind values
            $stmt->bindValue(':account_password', $hashed_password, PDO::PARAM_STR); // ハッシュ化されたパスワードを保存
            $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR); // 従業員No
            $stmt->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属営業所
            $stmt->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ふりがな）
            $stmt->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ふりがな）
            $stmt->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
            $stmt->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
            $stmt->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_STR); // 生年月日（年）
            $stmt->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_STR); // 生年月日（月）
            $stmt->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_STR); // 生年月日（日）
            $stmt->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_STR); // 性別
            $stmt->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_STR); // 血液型
            $stmt->bindValue(':account_zipcord01', $_POST['account_zipcord01'], PDO::PARAM_STR); // 郵便番号（前半）
            $stmt->bindValue(':account_zipcord02', $_POST['account_zipcord02'], PDO::PARAM_STR); // 郵便番号（後半）
            $stmt->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR); // 都道府県
            $stmt->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR); // 市町村区
            $stmt->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR); // 町名番地
            $stmt->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR); // マンション名など
            $stmt->bindValue(':account_tel01', $_POST['account_tel01'], PDO::PARAM_STR); // 連絡先1（前半）
            $stmt->bindValue(':account_tel02', $_POST['account_tel02'], PDO::PARAM_STR); // 連絡先1（中）
            $stmt->bindValue(':account_tel03', $_POST['account_tel03'], PDO::PARAM_STR); // 連絡先1（後半）
            $stmt->bindValue(':account_tel04', $_POST['account_tel04'], PDO::PARAM_STR); // 連絡先2（前半）
            $stmt->bindValue(':account_tel05', $_POST['account_tel05'], PDO::PARAM_STR); // 連絡先2（中）
            $stmt->bindValue(':account_tel06', $_POST['account_tel06'], PDO::PARAM_STR); // 連絡先2（後半）
            $stmt->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_STR); // 免許証有効期限（年）
            $stmt->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_STR); // 免許証有効期限（月）
            $stmt->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_STR); // 免許証有効期限（日）
            $stmt->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR); // 身元保証人氏（ふりがな）
            $stmt->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR); // 身元保証人名（ふりがな）
            $stmt->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR); // 身元保証人氏（漢字）
            $stmt->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR); // 身元保証人名（漢字）
            $stmt->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR); // 続柄
            $stmt->bindValue(':account_guarentor_zipcord01', $_POST['account_guarentor_zipcord01'], PDO::PARAM_STR); // 身元保証人郵便番号（前半）
            $stmt->bindValue(':account_guarentor_zipcord02', $_POST['account_guarentor_zipcord02'], PDO::PARAM_STR); // 身元保証人郵便番号（後半）
            $stmt->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR); // 身元保証人都道府県
            $stmt->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR); // 身元保証人市町村区
            $stmt->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR); // 身元保証人町名番地
            $stmt->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR); // 身元保証人マンション名など
            $stmt->bindValue(':account_guarentor_tel01', $_POST['account_guarentor_tel01'], PDO::PARAM_STR); // 身元保証人連絡先1（前半）
            $stmt->bindValue(':account_guarentor_tel02', $_POST['account_guarentor_tel02'], PDO::PARAM_STR); // 身元保証人連絡先1（中）
            $stmt->bindValue(':account_guarentor_tel03', $_POST['account_guarentor_tel03'], PDO::PARAM_STR); // 身元保証人連絡先1（後半）
            $stmt->bindValue(':account_guarentor_tel04', $_POST['account_guarentor_tel04'], PDO::PARAM_STR); // 身元保証人連絡先2（前半）
            $stmt->bindValue(':account_guarentor_tel05', $_POST['account_guarentor_tel05'], PDO::PARAM_STR); // 身元保証人連絡先2（中）
            $stmt->bindValue(':account_guarentor_tel06', $_POST['account_guarentor_tel06'], PDO::PARAM_STR); // 身元保証人連絡先2（後半）
            $stmt->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_STR); // 所属課
            $stmt->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_STR); // 勤務区分
            $stmt->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_STR); // 職種区分
            $stmt->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_STR); // 在籍区分
            $stmt->bindValue(':account_employment_year', $_POST['account_employment_year'], PDO::PARAM_STR); // 雇用年月日（年）
            $stmt->bindValue(':account_employment_month', $_POST['account_employment_month'], PDO::PARAM_STR); // 雇用年月日（月）
            $stmt->bindValue(':account_employment_day', $_POST['account_employment_day'], PDO::PARAM_STR); // 雇用年月日（日）
            $stmt->bindValue(':account_appointment_year', $_POST['account_appointment_year'], PDO::PARAM_STR); // 選任年月日（年）
            $stmt->bindValue(':account_appointment_month', $_POST['account_appointment_month'], PDO::PARAM_STR); // 選任年月日（月）
            $stmt->bindValue(':account_appointment_day', $_POST['account_appointment_day'], PDO::PARAM_STR); // 選任年月日（日）
            $stmt->bindValue(':account_retirement_year', $_POST['account_retirement_year'], PDO::PARAM_STR); // 退職年月日（年）
            $stmt->bindValue(':account_retirement_month', $_POST['account_retirement_month'], PDO::PARAM_STR); // 退職年月日（月）
            $stmt->bindValue(':account_retirement_day', $_POST['account_retirement_day'], PDO::PARAM_STR); // 退職年月日（日）
            }   
            // var_dump($_POST); 
            // exit;
            // SQL文を実行する
            $stmt->execute();

            // メッセージを設定してリダイレクト
            $message = "従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に登録されました。";
            header("Location: list.php?message=" . urlencode($message));
            exit();
        } catch (PDOException $e) {
            echo 'データベースエラー: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>従業員新規登録</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
        <script src="https://yubinbango.github.io/yubinbango/yubinbango.js"></script>
    </head>

    <header>
        <h1>従業員新規登録</h1>
        <!-- パンクズナビ -->
        <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="../main_menu/index.php">
                  <span itemprop="name">メインメニュー</span>
              </a>
              <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="list.php">
                  <span itemprop="name">従業員一覧</span>
              </a>
              <meta itemprop="position" content="2" />
            </li>
        </ol>
    </header>

    <body>
        <form action="" method="POST">
            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="first-table">
                    <tr>
                        <th>パスワード</th>
                        <td>
                            <input type="password" name="account_password" placeholder="t0542542471" value="<?= htmlspecialchars($_POST['account_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_password'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_password'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>  
                    </tr>
                    <tr>
                        <th>従業員No<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="account_no" placeholder="1111" value="<?= htmlspecialchars($_POST['account_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_no'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>所属営業所</th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE); ?>
                            </select>
                        </td>
                        <!-- <td><select name="account_salesoffice">
                                <option value="">選択</option>
                                <option value="1">本社営業所</option>
                                <option value="2">向敷地営業所</option>
                            </select>
                        </td> -->
                    </tr>
                    <tr>
                        <th>氏（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" value="<?= htmlspecialchars($_POST['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" value="<?= htmlspecialchars($_POST['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（漢字）</th>
                        <td><input type="text" class="text" placeholder="辰巳" name="account_name01"></td>
                        <th>名（漢字）</th>
                        <td><input type="text" class="text" placeholder="一番" name="account_name02"></td>
                    </tr>
                    <tr>
                        <th>生年月日</th>
                        <td colspan="2">
                        <select name="account_birthday_year">
                                <?php
                                    $startYear = 1939;
                                    $endYear = date("Y") - 25;  // 現在の年から25年前を終了年に設定
                                        echo generateYearOptions($startYear, $endYear, $_POST['account_birthday_year'] ?? '');
                                ?>
                            </select>年
                            <select name="account_birthday_month">
                                <?= generateMonthOptions($_POST['account_birthday_month'] ?? '') ?>
                            </select>月  
                            <select name="account_birthday_day">
                                <?= generateDayOptions($_POST['account_birthday_day'] ?? '') ?>
                            </select>日
                        </td>
                    </tr>
                    <tr>
                        <th>性別</th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA); ?>
                            </select>
                        </td>
                        <!-- <td><select name="account_jenda">
                                <option value="">選択</option>
                                <option value="1">男</option>
                                <option value="2">女</option>
                            </select>
                        </td> -->
                        <th>血液型</th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE); ?>
                            </select>
                        </td>
                        <!-- <td><select name="account_bloodtype">
                                <option value="">選択</option>
                                <option value="1">A型</option>
                                <option value="2">B型</option>
                                <option value="3">O型</option>
                                <option value="4">AB型</option>
                            </select>
                        </td> -->
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="420" value="<?= htmlspecialchars($_POST['account_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="0042" value="<?= htmlspecialchars($_POST['account_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_pref" value="<?= htmlspecialchars($_POST['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_address01" value="<?= htmlspecialchars($_POST['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_address02" placeholder="駒形通2丁目2-25" value="<?= htmlspecialchars($_POST['account_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_address03" placeholder="辰巳マンション1111号室" value="<?= htmlspecialchars($_POST['account_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address03'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="0120" value="<?= htmlspecialchars($_POST['account_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" value="<?= htmlspecialchars($_POST['account_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" value="<?= htmlspecialchars($_POST['account_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td> 
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel04" size="4" maxlength="4" name="account_tel04" placeholder="0120" value="<?= htmlspecialchars($_POST['account_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel05" size="4" maxlength="4" name="account_tel05" placeholder="1234" value="<?= htmlspecialchars($_POST['account_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel06" size="4" maxlength="4" name="account_tel06" placeholder="5678" value="<?= htmlspecialchars($_POST['account_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel2'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限</th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $currentYear = 2024;
                                    $endYear = $currentYear + 10;  // 現在の年から10年後まで(自動的に10年追加される)
                                        echo generateYearOptions($currentYear, $endYear, $_POST['account_license_expiration_date_year'] ?? '');
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?= generateMonthOptions($_POST['account_license_expiration_date_month'] ?? '') ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?= generateDayOptions($_POST['account_license_expiration_date_day'] ?? '') ?>
                            </select>日
                        </td>
                    </tr>   
                </table>
            </div>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="second-table">
                    <tr>
                        <th>身元保証人<br>氏（ふりがな）</th>
                        <td>
                            <input type class="text" placeholder="たつみ" name="account_guarentor_kana01" value="<?= htmlspecialchars($_POST['account_guarentor_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>身元保証人<br>名（ふりがな）</th>
                        <td>
                            <input type class="text" placeholder="おやじ" name="account_guarentor_kana02" value="<?= htmlspecialchars($_POST['account_guarentor_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>身元保証人<br>氏（漢字）</th>
                        <td><input type class="text" placeholder="辰巳" name="account_guarentor_name01"></td>
                        <th>身元保証人<br>名（漢字）</th>
                        <td><input type class="text" placeholder="親父" name="account_guarentor_name02"></td>
                    </tr>
                    <tr>
                        <th>続柄</th>
                        <td><input type class="text" placeholder="父" name="account_relationship"></td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_guarentor_zipcord01" placeholder="420" value="<?= htmlspecialchars($_POST['account_guarentor_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_guarentor_zipcord02" placeholder="0042" value="<?= htmlspecialchars($_POST['account_guarentor_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['account_guarentor_zipcord'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_guarentor_pref" value="<?= htmlspecialchars($_POST['account_guarentor_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_guarentor_address01" value="<?= htmlspecialchars($_POST['account_guarentor_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_guarentor_address02" placeholder="駒形通2丁目2-25" value="<?= htmlspecialchars($_POST['account_guarentor_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_guarentor_address03" placeholder="辰巳マンション1111号室" value="<?= htmlspecialchars($_POST['account_guarentor_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address03'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_guarentor_tel01" size="4" maxlength="4" name="account_guarentor_tel01" placeholder="0120" value="<?= htmlspecialchars($_POST['account_guarentor_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel02" size="4" maxlength="4" name="account_guarentor_tel02" placeholder="1234" value="<?= htmlspecialchars($_POST['account_guarentor_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel03" size="4" maxlength="4" name="account_guarentor_tel03" placeholder="5678" value="<?= htmlspecialchars($_POST['account_guarentor_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>           
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel04" value="<?= htmlspecialchars($_POST['account_guarentor_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel05" value="<?= htmlspecialchars($_POST['account_guarentor_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel06" value="<?= htmlspecialchars($_POST['account_guarentor_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel2'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="third-table">
                <tr>
                <th>所属課</th>
                        <td><select name="account_department">
                            <?= generateSelectOptions(ACCOUNT_DEPARTMENT); ?>
                        </select></td>
                    <!-- <th>所属課</th>
                    <td><select name="account_department">
                            <option value="">選択</option>
                            <option value="1">内勤</option>
                            <option value="2">外勤</option>
                        </select>
                    </td> -->
                    <th>勤務区分</th>
                    <td><select name="account_workclass">
                            <?= generateSelectOptions(ACCOUNT_WORKCLASS); ?>
                        </select></td>
                    <!-- <td><select name="account_workclass">
                            <option value="">選択</option>
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
                    </td> -->
                <tr>
                    <th>職種区分</th>
                    <td><select name="account_classification">
                            <?= generateSelectOptions(ACCOUNT_CLASSIFICATION); ?>
                        </select></td>
                    <th>在籍区分</th>
                        <td><select name="account_enrollment">
                            <?= generateSelectOptions(ACCOUNT_ENROLLMENT); ?>
                        </select></td>
                    <!-- <td><select name="account_classification">
                            <option value="">選択</option>
                            <option value="1">正社員</option>
                            <option value="2">準正社員</option>
                            <option value="3">嘱託</option>
                        </select>
                    </td>
                    <th>在籍区分</th>
                    <td><select name="account_enrollment">
                            <option value="">選択</option>
                            <option value="1">本採用</option>
                            <option value="2">中途採用</option>
                            <option value="3">退職</option>
                        </select>
                    </td> -->
                </tr>
                <tr>
                    <th>雇用年月日</th>
                    <td colspan="2">
                        <select name="account_employment_year">
                            <?php
                                $startYear = 1985;  // 開始年
                                $endYear = date('Y') + 1;  // 終了年を現在の年から1年後に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_employment_year'] ?? date('Y'));
                            ?>
                        </select>年
                        <select name="account_employment_month">
                            <?php
                                echo generateMonthOptions($_POST['account_employment_month'] ?? date('n'));
                            ?>
                        </select>月
                        <select name="account_employment_day">
                            <?php
                                echo generateDayOptions($_POST['account_employment_day'] ?? date('j'));
                            ?>
                        </select>日
                    </td>
                </tr>
                <tr>
                    <th>選任年月日</th>
                    <td colspan="2">
                        <select name="account_appointment_year">
                            <?php
                                $startYear = 1985;  // 開始年
                                $endYear = date('Y') + 1;  // 現在の年から1年後までを終了年に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_appointment_year'] ?? date('Y'));
                            ?>
                        </select>年
                        <select name="account_appointment_month">
                            <?php
                                echo generateMonthOptions($_POST['account_appointment_month'] ?? date('n'));
                            ?>
                        </select>月
                        <select name="account_appointment_day">
                            <?php
                                echo generateDayOptions($_POST['account_appointment_day'] ?? date('j'));
                            ?>
                        </select>日 
                    </td>
                </tr>
                <tr>
                    <th>退職年月日</th>
                    <td colspan="2">
                        <select name="account_retirement_year">
                            <?php
                                $startYear = 2020;  // 開始年
                                $endYear = date('Y') + 1;  // 現在の年から1年後を終了年に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_retirement_year'] ?? '');
                            ?>
                        </select>年
                        <select name="account_retirement_month">
                            <?php
                                echo generateMonthOptions($_POST['account_retirement_month'] ?? '');
                            ?>
                        </select>月
                        <select name="account_retirement_day">
                            <?php
                                echo generateDayOptions($_POST['account_retirement_day'] ?? '');
                            ?>
                        </select>日
                    </td>
                </tr> 
                <tr>
                    <th>?????</th>
                    <td><select name="account_enrollment1">
                            <option value="">選択</option>
                            <option value="1">年末調整対象</option> 
                            <option value="2">年末調整対象外</option> 
                            <option value="3">死亡退職</option> 
                        </select>
                    </td>
                </tr>                           
            </table>
            <!-- <table table class="fourth-table">
                <hr>
                <tr>
                <th>無効フラグ</th>
                <td><select name="account_invalidflag">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </td>
                </tr>
            </table> -->
            <div table class="flex">
                <input type="submit" value="登録" name="submit">
            </div>
        </form>

    </body>
    
</html>

<!-- ログイン　index.php -->
<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

// セッションを開始
session_start();

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$account_no = $_POST['account_no'] ?? '';
$account_password = $_POST['account_password'] ?? '';

if (!empty($account_no) && !empty($account_password)) {
    try {
        // 従業員Noでユーザーを検索
        $sql = 'SELECT * FROM accounts WHERE account_no = :account_no';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':account_no', $account_no, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // パスワードの検証
            if ($user && password_verify($account_password, $user['account_password'])) {
                // パスワードが一致すればログイン成功
                $_SESSION['account_no'] = $user['account_no'];
                $_SESSION['account_name'] = $user['account_name01'] . ' ' . $user['account_name02'];
                    
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
        $error_message = '従業員Noとパスワードを入力してください。';
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
            <input type="text" name="account_no" id="account_no" placeholder="Noを入力" required>
            <input type="password" name="account_password" id="account_password" placeholder="パスワードを入力" required>
            <input type="submit" name="button" value="LOGIN">
        </div>
    </form>
</body>

</html>


<!-- メインメニュー　index.php -->
<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

session_start();

// ログインチェック
if (!isset($_SESSION['account_no'])) {
    header('Location: ../login/index.php');
    exit();
}

// フルネームの取得
$user_name = $_SESSION['account_name'];

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['account_name'])) {
    header('Location: ../login/index.php');
    exit();
}

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 現在の日付を取得
    $currentDate = new DateTime();

    // 14日以内に有効期限が切れる免許証を持つ従業員を取得
    $sql = '
        SELECT account_id, account_name01, account_name02, account_license_expiration_date_year, account_license_expiration_date_month, account_license_expiration_date_day
        FROM accounts
        WHERE DATE(CONCAT(account_license_expiration_date_year, "-", account_license_expiration_date_month, "-", account_license_expiration_date_day)) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();

    $expiringAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>メインメニュー</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <header>
            <h1>メインメニュー</h1>
                <!-- パンクズナビ -->
                <!-- <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="../login/index.html">
                            <span itemprop="name">ログイン</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                </ol> -->
        </header>

        <main>
            <!-- ログインユーザーの氏名を表示 -->
        <p><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?> さんがログイン中です</p>
        <?php if (!empty($expiringAccounts)): ?>
    <h2>免許証を更新してください</h2>
    <?php foreach ($expiringAccounts as $account): ?>
        <?php
            // 有効期限をフォーマットする
            $expirationDate = "{$account['account_license_expiration_date_year']}年{$account['account_license_expiration_date_month']}月{$account['account_license_expiration_date_day']}日";
            
            // 氏名をフォーマットする
            $fullName = htmlspecialchars($account['account_name01'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($account['account_name02'], ENT_QUOTES, 'UTF-8');
        ?>
        <p>※<?= $fullName ?>　免許証有効期限：<?= htmlspecialchars($expirationDate, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
<?php endif; ?>
            <div class="container">
                <a href="#">
                    <button>日次集計</button>
                </a>
                <a href="#">
                    <button>月次集計</button>
                </a>
                <a href="../accounts/list.php">
                  <button>人事管理</button>
                </a>
                <a href="#">
                  <button>勤怠管理</button>
                </a>
                <a href="#">
                  <button>車両管理</button>
                </a>
            </div>
        </main>
    </body>
</html>