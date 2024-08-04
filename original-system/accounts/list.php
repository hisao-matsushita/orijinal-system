<?php
 $dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
 $user = 'root';
 $password = '';
 
 try {
     $pdo = new PDO($dsn, $user, $password);     

    // accountsテーブルから従業員登録された人数の合計を実行
    $stmt = $pdo->query('SELECT COUNT(*) AS total_accounts FROM accounts');
    // 結果の取得
    $total_people = $stmt->fetch(PDO::FETCH_ASSOC);

    // 乗務員のみ（所属課IDが2）の従業員数を取得
    $stmt = $pdo->query("SELECT COUNT(*) AS total_drivers FROM accounts WHERE account_department = '2'");
    $total_drivers = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 年齢計算および平均年齢計算（所属課2のみ）
    $sql = 'SELECT account_birthday_year, account_birthday_month, account_birthday_day FROM accounts WHERE account_department = 2';
    $stmt = $pdo->query($sql);
    $birthdates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAge = 0;
    $numOfEmployees = count($birthdates);

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
    //   echo '<p>所属課2の平均年齢: ' . round($averageAge, 1) . '歳</p>';


//ここがわからない！！！！！！！！！！！！！！！！！    
//accountsテーブルとmasterテーブルをmaster_idで結合し、所属課IDと所属nameを取得する
$sql = 'SELECT * FROM accounts JOIN master ON accounts.account_department = master.id';
$stmt = $pdo->query($sql);
// 結果の取得
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 結果の表示
foreach ($results as $row) {
echo $row['account_department'];
echo $row['id'];
}
//  $names = [0 => '未選択', 1 => '内勤', 2 => '外勤'];



   // keywordパラメータの値が存在すれば（「検索」ボタンを押したとき）、その値を変数$keywordに代入する    
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }
// keywordパラメータの値によってSQL文を変更する    
// if ($keyword === 'desc') {
//     $sql = 'SELECT * FROM accounts WHERE account_kana01 LIKE :keyword ORDER BY account_no DESC';
// } else {
//     $sql = 'SELECT * FROM accounts WHERE account_kana01 LIKE :keyword ORDER BY account_no ASC';
// }

    if ($keyword === 'desc') {
        $sql = 'SELECT * FROM accounts WHERE account_kana01 LIKE :keyword OR account_kana02 LIKE :keyword ORDER BY account_no DESC'; //従業員No順で表示
    } else {
        $sql = 'SELECT * FROM accounts WHERE account_kana01 LIKE :keyword OR account_kana02 LIKE :keyword ORDER BY account_no ASC';
    }

    // SQL文を用意する
    $stmt = $pdo->prepare($sql);
    // SQLのLIKE句で使うため、変数$keyword（検索ワード）の前後を%で囲む（部分一致）
    // 補足：partial match＝部分一致
    $partial_match = "%{$keyword}%";

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


 ?>

<!DOCTYPE html>
<html lang="ja">
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
                                <!-- <input type="submit" value="検索"> -->
                            </form>
                            </td>
                            <!-- <td><input type="text" placeholder="たつみいちばん"></td> -->
                            <td><select name="account_department">
                                    <option value="">選択</option>
                                    <option value="内勤">内勤</option>
                                    <option value="外勤">外勤</option>
                                </select>
                            </td>
                            <td><select name="account_classification">
                                    <option value="">選択</option>
                                    <option value="正社員">正社員</option>
                                    <option value="準正社員">準正社員</option>
                                    <option value="嘱託">嘱託</option>
                                </select>
                            </td>
                            <td><select name="account_workclass">
                                    <option value="">選択</option>
                                    <option value="役員">役員</option>
                                    <option value="管理者">管理者</option>
                                    <option value="事務員">事務員</option>
                                    <option value="整備士">整備士</option>
                                    <option value="配車係">配車係</option>
                                    <option value="乗務A">乗務A</option>
                                    <option value="乗務B">乗務B</option>
                                    <option value="乗務C">乗務C</option>
                                    <option value="乗務D">乗務D</option>
                                    <option value="乗務E">乗務E</option>
                                    <option value="乗務F">乗務F</option>
                                    <option value="乗務G">乗務G</option>
                                    <option value="乗務H">乗務H</option>
                                </select>
                            </td>
                            <td><select name="account_retirement">
                                    <option value="">選択</option>
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
                    <?php
                    // 配列の中身を順番に取り出し、表形式で出力する
                    foreach ($accounts as $account) {
                    // 登録・更新の日付のフォーマット
                    $registration_date = isset($account['registration_date']) ? (new DateTime($account['registration_date']))->format('Y年m月d日') : '日付なし';
                    $updated_at = isset($account['updated_at']) ? (new DateTime($account['updated_at']))->format('Y年m月d日') : '日付なし';
                     // 免許証有効期限を「YYYY年MM月DD日」形式にフォーマット
                    $account_deadline = htmlspecialchars($account['account_license_expiration_date_year'] . '年' . $account['account_license_expiration_date_month'] . '月' . $account['account_license_expiration_date_day'] . '日', ENT_QUOTES, 'UTF-8');
                    // 生年月日を「YYYY年MM月DD日」形式にフォーマット
                    $account_birthday = htmlspecialchars($account['account_birthday_year'] . '年' . $account['account_birthday_month'] . '月' . $account['account_birthday_day'] . '日', ENT_QUOTES, 'UTF-8');
                    // 年齢を計算
                    $birthDate = $account['account_birthday_year'] . '-' . $account['account_birthday_month'] . '-' . $account['account_birthday_day'];
                    $birthDateTime = new DateTime($birthDate);
                    $currentDateTime = new DateTime();
                    $ageYears = $currentDateTime->diff($birthDateTime)->y;
                    $ageMonths = $currentDateTime->diff($birthDateTime)->m;
                    // 雇用年月日を「YYYY年MM月DD日」形式にフォーマット
                    $account_employmentday = htmlspecialchars($account['account_employment_year'] . '年' . $account['account_employment_month'] . '月' . $account['account_employment_day'] . '日', ENT_QUOTES, 'UTF-8');
                    // 勤続年数を計算
                    $birthDate = $account['account_employment_year'] . '-' . $account['account_employment_month'] . '-' . $account['account_employment_day'];
                    $birthDateTime = new DateTime($birthDate);
                    $currentDateTime = new DateTime();
                    $serviceYears = $currentDateTime->diff($birthDateTime)->y;
                    $serviceMonths = $currentDateTime->diff($birthDateTime)->m;
                    // データのエスケープ
                    $account_no = htmlspecialchars($account['account_no'], ENT_QUOTES, 'UTF-8');
                    $account_kana = htmlspecialchars($account['account_kana01'] . $account['account_kana02'], ENT_QUOTES, 'UTF-8');
                    $account_name = htmlspecialchars($account['account_name01'] . $account['account_name02'], ENT_QUOTES, 'UTF-8');
                    // $account_deadline = htmlspecialchars($account['account_license_expiration_date_year'] . $account['account_license_expiration_date_month'] . $account['account_license_expiration_date_day'], ENT_QUOTES, 'UTF-8');
                    $account_department = htmlspecialchars($account['account_department'], ENT_QUOTES, 'UTF-8');
                    $account_classification = htmlspecialchars($account['account_classification'], ENT_QUOTES, 'UTF-8');
                    $account_workclass = htmlspecialchars($account['account_workclass'], ENT_QUOTES, 'UTF-8');
                    // $account_birthday = htmlspecialchars($account['account_birthday'], ENT_QUOTES, 'UTF-8');
                    // テーブル行の生成
                    echo "
                        <tr>
                            <td><button>詳細</button></td>
                            <td>{$account_no}</td> <!-- 従業員番号の表示 -->
                            <td>{$account_kana}<br>{$account_name}</td> <!-- 氏名（ひらがな）（漢字）の表示 -->
                            <td>" . htmlspecialchars($ageYears, ENT_QUOTES, 'UTF-8') . "歳" . htmlspecialchars($ageMonths, ENT_QUOTES, 'UTF-8') . "か月</td> <!-- 年齢の表示 -->
                            <td>" . htmlspecialchars($serviceYears, ENT_QUOTES, 'UTF-8') . "年" . htmlspecialchars($serviceMonths, ENT_QUOTES, 'UTF-8') . "か月</td> <!-- 勤続年数の表示 -->
                            <td>{$account_deadline}</td> <!-- 免許証有効期限の表示 -->
                            <td>{$account_department}</td> <!-- 所属課の表示 -->
                            <td>{$account_classification}</td> <!-- 職種区分の表示 -->
                            <td>{$account_workclass}</td> <!-- 勤務区分の表示 -->
                            <td>{$registration_date}</td> <!-- 登録年月日の表示 -->
                            <td>{$updated_at}</td> <!-- 更新年月日の表示 -->
                        </tr>
                        ";
                    }  
                    ?>
                    <tr>
                        <td><button>詳細</button></td>
                        <td>1234</td>
                        <td>ゆいとうてつや<br>由比藤哲也</td>
                        <td>60歳</td>
                        <td>10年</td>
                        <td>2024年12月12日</td>
                        <td>外勤</td>
                        <td>準正社員</td>
                        <td>配車係</td>
                        <td>2024年12月12日</td>
                        <td>2024年12月12日</td>
                    </tr>
                    <tr>
                        <td><button>詳細</button></td>
                        <td>972</td>
                        <td>ほりごめあすか<br>堀米明日香</td>
                        <td>41歳</td>
                        <td>2年</td>
                        <td>2024年12月12日</td>
                        <td>内勤</td>
                        <td>嘱託</td>
                        <td>事務員</td>
                        <td>2024年12月12日</td>
                        <td>2024年12月12日</td>
                    </tr>
                    <tr>
                        <td><button>詳細</button></td>
                        <td>673</td>
                        <td>なかやまはつお<br>中山初男</td>
                        <td>72歳</td>
                        <td>32年</td>
                        <td>2024年12月12日</td>
                        <td>外勤</td>
                        <td>嘱託</td>
                        <td>乗務A</td>
                        <td>2024年12月12日</td>
                        <td>2024年12月12日</td>
                    </tr>
                    <tr>
                        <td><button>詳細</button></td>
                        <td>927</td>
                        <td>ささきよういち<br>佐々木鷹一</td>
                        <td>37歳</td>
                        <td>3年</td>
                        <td>2024年12月12日</td>
                        <td>外勤</td>
                        <td>正社員</td>
                        <td>乗務H</td>
                        <td>2024年12月12日</td>
                        <td>2024年12月12日</td>
                    </tr>
                    <tr>
                        <td><button>詳細</button></td>
                        <td>1234</td>
                        <td>ゆいとうてつや<br>由比藤哲也</td>
                        <td>60歳</td>
                        <td>10年</td>
                        <td>2024年12月12日</td>
                        <td>外勤</td>
                        <td>準正社員</td>
                        <td>配車係</td>
                        <td>2024年12月12日</td>
                        <td>2024年12月12日</td>
                    </tr>
                </table>
            </div>
        </main>
    </body>
</html>