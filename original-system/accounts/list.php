<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';
$processedAccounts = [];
const ACCOUNT_DEPARTMENT = [0 =>'未選択', 1 =>'内勤', 2 =>'外勤'];
const ACCOUNT_CLASSIFICATION = [0 =>'未選択', 1 =>'正社員', 2 =>'準社員', 3 => '嘱託'];
const ACCOUNT_WORKCLASS = [0 =>'未選択', 1 =>'役員', 2 =>'管理者', 3 => '事務員', 4 =>'整備士', 5 =>'配車係', 6 =>'乗務A', 7 =>'乗務B', 8 =>'乗務C', 9 =>'乗務D',
                           10 =>'乗務E', 11 =>'乗務F', 12 =>'乗務G', 13 =>'乗務H'];

if (isset($_POST['submit'])) {
try {
    $pdo = new PDO($dsn, $user, $password);

    // 動的に変わる値をプレースホルダに置き換えたUPDATE文をあらかじめ用意する
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
                        
    // bindValue()メソッドを使って実際の値をプレースホルダにバインドする（割り当てる）
    $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_password', $_POST['account_password'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR);
    $stmt_update->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_INT);
    $stmt_update->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);

// 退職年月日のバインド
$stmt_update->bindValue(':account_retirement_year', !empty($_POST['account_retirement_year']) ? $_POST['account_retirement_year'] : null, PDO::PARAM_INT);
$stmt_update->bindValue(':account_retirement_month', !empty($_POST['account_retirement_month']) ? $_POST['account_retirement_month'] : null, PDO::PARAM_INT);
$stmt_update->bindValue(':account_retirement_day', !empty($_POST['account_retirement_day']) ? $_POST['account_retirement_day'] : null, PDO::PARAM_INT);
                            
    // SQL文を実行する
    $stmt_update->execute();
                            
    // 更新した件数を取得する
    $count = $stmt_update->rowCount();
                            
    $message = "授業員を{$count}件編集しました。";
                            
    // 商品一覧ページにリダイレクトさせる（同時にmessageパラメータも渡す）
    header("Location: register.php?message={$message}");
    } catch (PDOException $e) {
    exit($e->getMessage());
    }
}                                  
try {
    $pdo = new PDO($dsn, $user, $password);     
                            
    // accountsテーブルから従業員登録された人数の合計を実行
    $stmt = $pdo->query('SELECT COUNT(*) AS total_accounts FROM accounts');
    // 結果の取得
    $total_people = $stmt->fetch(PDO::FETCH_ASSOC);
                            
    // 乗務員のみ（所属課IDが2）の従業員数を取
    $stmt = $pdo->query("SELECT COUNT(*) AS total_drivers FROM accounts WHERE account_department = '2'");
    $total_drivers = $stmt->fetch(PDO::FETCH_ASSOC);
                                
    // 年齢計算および平均年齢計算（所属課2のみ）
    $sql = 'SELECT account_birthday_year, account_birthday_month, account_birthday_day FROM accounts WHERE account_department = 2';
    $stmt = $pdo->query($sql);
    $birthdates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
    $totalAge = 0;
    $numOfEmployees = count($birthdates);

// 退職者リストを取得
$sql = 'SELECT account_id, CONCAT(account_name01, " ", account_name02) AS account_name, account_retirement_year, account_retirement_month, account_retirement_day FROM accounts WHERE account_retirement_year IS NOT NULL';
$stmt = $pdo->query($sql);
$retiredAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($birthdates as $account) {
    // 年齢を計算
    $birthDate = $account['account_birthday_year'] . '-' . $account['account_birthday_month'] . '-' . $account['account_birthday_day'];
    $birthDateTime = new DateTime($birthDate);
    $currentDateTime = new DateTime();
    $age = $currentDateTime->diff($birthDateTime)->y;
    $totalAge += $age;
    }
    // 平均年齢を計算
    $averageAge = $numOfEmployees > 0 ? $totalAge / $numOfEmployees : 0;                                     
                                          
    // keywordパラメータの値が存在すれば（「検索」ボタンを押したとき）、その値を変数$keywordに代入する    
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }
    $account_department = isset($_GET['account_department']) ? $_GET['account_department'] : '0';
    $account_classification = isset($_GET['account_classification']) ? $_GET['account_classification'] : '0';
    $account_workclass = isset($_GET['account_workclass']) ? $_GET['account_workclass'] : '0';
    // SQLクエリを構築
// $sql = 'SELECT * FROM accounts WHERE CONCAT(account_kana01, account_kana02) LIKE :keyword AND account_retirement_year IS NULL AND account_retirement_month IS NULL AND account_retirement_day IS NULL';
    $sql = 'SELECT * FROM accounts WHERE CONCAT(account_kana01, account_kana02) LIKE :keyword';
    if ($account_department != '0') {
        $sql .= ' AND account_department = :account_department';
    }
    if ($account_classification != '0') {
        $sql .= ' AND account_classification = :account_classification';
    }
    if ($account_workclass != '0') {
        $sql .= ' AND account_workclass = :account_workclass';
    }

    $sql .= ' ORDER BY account_no ASC';

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
                                             
    // bindValue()メソッドを使って実際の値をプレースホルダにバインドする（割り当てる）
    $stmt->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
                            
    // SQL文を実行する
    $stmt->execute();
                                                    
    // SQL文の実行結果を配列で取得する
    //  $accounts = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
    exit($e->getMessage());
    }      
    foreach ($accounts as $account) {
        // 日付のフォーマット
        $registration_date = isset($account['registration_date']) ? (new DateTime($account['registration_date']))->format('Y年m月d日') : '日付なし';
        $updated_at = isset($account['updated_at']) ? (new DateTime($account['updated_at']))->format('Y年m月d日') : '日付なし';
        
        // 免許証有効期限のフォーマット
        $account_deadline = htmlspecialchars($account['account_license_expiration_date_year'] . '年' . $account['account_license_expiration_date_month'] . '月' . $account['account_license_expiration_date_day'] . '日', ENT_QUOTES, 'UTF-8');
    
        // 年齢計算
        $birthDate = $account['account_birthday_year'] . '-' . $account['account_birthday_month'] . '-' . $account['account_birthday_day'];
        $birthDateTime = new DateTime($birthDate);
        $currentDateTime = new DateTime();
        $ageYears = $currentDateTime->diff($birthDateTime)->y;
        $ageMonths = $currentDateTime->diff($birthDateTime)->m;
    
        // 勤続年数計算
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
    
        // 詳細ページのリンクを生成
        $detail_link = "update.php?account_id=" . htmlspecialchars($account['account_id'], ENT_QUOTES, 'UTF-8');
    
        // 処理したデータを新しい配列に格納
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
                <a href="register.php">新規登録</a>
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
                <!-- <input type="button" value="検索"> -->
                <button>検索</button>
                <button>クリア</button>
                <button>印刷</button>
            </div>
            <div>
                <table class="list">
                    <tr>
                        <th></th>
                        <th class="account_no">従業員番号</th>
                        <th class="account_kana01">氏名</th>
                        <th>年齢</th>
                        <th>勤続年数</th>
                        <th class="deadline">免許証有効期限</th>
                        <th>所属課</th>
                        <th>職種区分</th>
                        <th>勤務区分</th>
                        <th class="registration_date">登録年月日</th>
                        <th class="updated_at">更新年月日</th>
                    </tr>
                    <?php foreach ($processedAccounts as $account): ?>
                    <tr>
                        <td><a href="<?= $account['detail_link'] ?>">詳細</a></td>
                        <td><?= $account['account_no'] ?> <!-- 従業員番号の表示 --> 
                        <td><?= $account['account_name'] ?><br><?= $account['account_kana'] ?></td> <!-- 氏名（ひらがな）（漢字）の表示 -->
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