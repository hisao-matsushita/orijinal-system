<?php
print_r($_POST)
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
              <a itemprop="item" href="../main_menu/index.html">
                  <span itemprop="name">メインメニュー</span>
              </a>
              <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="list.html">
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
                        <td><input type="password" class="text" placeholder="t0542542471" name="password"></td>
                    </tr>
                    <tr>
                        <th>従業員No</th>
                        <td><input type="text" placeholder="1111" name="account_no"></td>
                        <th>所属営業所</th>
                        <td><select name="sales_office">
                                <option value="">選択</option>
                                <option value="本社営業所">本社営業所</option>
                                <option value="向敷地営業所">向敷地営業所</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）</th>
                        <td><input type="text" class="text" placeholder="たつみ" name="account_kana01"></td>
                        <th>名（ふりがな）</th>
                        <td><input type="text" class="text" placeholder="いちばん" name="account_kana02"></td>
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
                                <option value="">選択</option>
                                <option value="1941">1941</option>
                                <option value="1942">1942</option>
                                <option value="1943">1943</option>
                                <option value="1944">1944</option>
                                <option value="1945">1945</option>
                                <option value="1946">1946</option>
                                <option value="1947">1947</option>
                                <option value="1948">1948</option>
                                <option value="1949">1949</option>
                                <option value="1950">1950</option>
                                <option value="1951">1951</option>
                                <option value="1952">1952</option>
                                <option value="1953">1953</option>
                                <option value="1954">1954</option>
                                <option value="1955">1955</option>
                                <option value="1956">1956</option>
                                <option value="1957">1957</option>
                                <option value="1958">1958</option>
                                <option value="1959">1959</option>
                                <option value="1960">1960</option>
                                <option value="1961">1961</option>
                                <option value="1962">1962</option>
                                <option value="1963">1963</option>
                                <option value="1964">1964</option>
                                <option value="1965">1965</option>
                                <option value="1966">1966</option>
                                <option value="1967">1967</option>
                                <option value="1968">1968</option>
                                <option value="1969">1969</option>
                                <option value="1970">1970</option>
                                <option value="1971">1971</option>
                                <option value="1972">1972</option>
                                <option value="1973">1973</option>
                                <option value="1974">1974</option>
                                <option value="1975">1975</option>
                                <option value="1976">1976</option>
                                <option value="1977">1977</option>
                                <option value="1978">1978</option>
                                <option value="1979">1979</option>
                                <option value="1980">1980</option>
                                <option value="1981">1981</option>
                                <option value="1982">1982</option>
                                <option value="1983">1983</option>
                                <option value="1984">1984</option>
                                <option value="1985">1985</option>
                                <option value="1986">1986</option>
                                <option value="1987">1987</option>
                                <option value="1988">1988</option>
                                <option value="1989">1989</option>
                                <option value="1990">1990</option>
                                <option value="1991">1991</option>
                                <option value="1992">1992</option>
                                <option value="1993">1993</option>
                                <option value="1994">1994</option>
                                <option value="1995">1995</option>
                                <option value="1996">1996</option>
                                <option value="1997">1997</option>
                                <option value="1998">1998</option>
                                <option value="1999">1999</option>
                            </select>年
                            <select name="account_birthday_month">
                                <option value="">選択</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>月
                            <select name="account_birthday_day">
                                <option value="">選択</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>日
                        </td>
                    </tr>    
                    <tr>
                        <th>性別</th>
                        <td><select name="account_jenda">
                                <option value="">選択</option>
                                <option value="男">男</option>
                                <option value="女">女</option>
                            </select>
                        </td>
                        <th>血液型</th>
                        <td><select name="account_bloodtype">
                                <option value="">選択</option>
                                <option value="A型">A型</option>
                                <option value="B型">B型</option>
                                <option value="O型">O型</option>
                                <option value="AB型">AB型</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td><input type="text" class="p-postal-code" size="3" maxlength="3" placeholder="420" name="account_zipcord01"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" placeholder="0042" name="account_zipcord02">
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" readonly name="account_pref"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" readonly name="account_address01"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                        <input type="text" class="p-street-address" name="account_address02" /></td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                        <input type="text" class="p-extended-address" name="account_address03" /></td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                        <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_tel01"> -
                        <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_tel02"> -
                        <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_tel03">
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                        <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_tel04"> -
                        <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_tel05"> -
                        <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_tel06">
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限</th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <option value="">選択</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                                <option value="2031">2031</option>
                                <option value="2032">2032</option>
                                <option value="2033">2033</option>
                                <option value="2034">2034</option>
                                <option value="2035">2035</option>
                                <option value="2036">2036</option>
                                <option value="2037">2037</option>
                                <option value="2038">2038</option>
                                <option value="2039">2039</option>
                                <option value="2040">2040</option>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <option value="">選択</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <option value="">選択</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>日
                        </td>
                    </tr>    
                </table>
        </div>

        <div class="h-adr">
            <span class="p-country-name" style="display:none;">Japan</span>
                <table class="second-table">
                    <hr>
                    <tr>
                        <th>身元保証人<br>氏（ふりがな）</th>
                        <td><input type class="text" placeholder="たつみ" name="account_guarentor_kana01"></td>
                        <th>身元保証人<br>名（ふりがな）</th>
                        <td><input type class="text" placeholder="おやじ" name="account_guarentor_kana02"></td>
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
                        <td><input type="text" class="p-postal-code" size="3" maxlength="3" placeholder="420" name="account_guarentor_zipcord01"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" placeholder="0042" name="account_guarentor_zipcord02">
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" readonly name="account_guarentor_pref"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" readonly name="account_guarentor_address01"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                        <input type="text" class="p-street-address" name="account_guarentor_address02" /></td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                        <input type="text" class="p-extended-address" name="account_guarentor_address03" /></td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                        <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel01"> -
                        <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel02"> -
                        <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel03">
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                        <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel04"> -
                        <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel05"> -
                        <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel06">
                        </td>
                    </tr>
                </table>
        </div>
            <table class="third-table">
                <hr>
                <tr>
                <th>所属課</th>
                <td><select name="account_department">
                        <option value="">選択</option>
                        <option value="内勤">内勤</option>
                        <option value="外勤">外勤</option>
                    </select>
                </td>
                <th>勤務区分</th>
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
                <tr>
                <th>職種区分</th>
                <td><select name="account_classification">
                        <option value="">選択</option>
                        <option value="正社員">正社員</option>
                        <option value="準正社員">準正社員</option>
                        <option value="嘱託">嘱託</option>
                    </select>
                </td>
                <th>在籍区分</th>
                <td><select name="account_enrollment">
                        <option value="">選択</option>
                        <option value="本採用">本採用</option>
                        <option value="中途採用">中途採用</option>
                        <option value="退職">退職</option>
                    </select>
                </td>
                </tr>
                <tr>
                <th>雇用年月日</th>
                <td colspan="2">
                    <select name="account_employment_year">
                        <option value="">選択</option>
                        <option value="1941">1941</option>
                        <option value="1942">1942</option>
                        <option value="1943">1943</option>
                        <option value="1944">1944</option>
                        <option value="1945">1945</option>
                        <option value="1946">1946</option>
                        <option value="1947">1947</option>
                        <option value="1948">1948</option>
                        <option value="1949">1949</option>
                        <option value="1950">1950</option>
                        <option value="1951">1951</option>
                        <option value="1952">1952</option>
                        <option value="1953">1953</option>
                        <option value="1954">1954</option>
                        <option value="1955">1955</option>
                        <option value="1956">1956</option>
                        <option value="1957">1957</option>
                        <option value="1958">1958</option>
                        <option value="1959">1959</option>
                        <option value="1960">1960</option>
                        <option value="1961">1961</option>
                        <option value="1962">1962</option>
                        <option value="1963">1963</option>
                        <option value="1964">1964</option>
                        <option value="1965">1965</option>
                        <option value="1966">1966</option>
                        <option value="1967">1967</option>
                        <option value="1968">1968</option>
                        <option value="1969">1969</option>
                        <option value="1970">1970</option>
                        <option value="1971">1971</option>
                        <option value="1972">1972</option>
                        <option value="1973">1973</option>
                        <option value="1974">1974</option>
                        <option value="1975">1975</option>
                        <option value="1976">1976</option>
                        <option value="1977">1977</option>
                        <option value="1978">1978</option>
                        <option value="1979">1979</option>
                        <option value="1980">1980</option>
                        <option value="1981">1981</option>
                        <option value="1982">1982</option>
                        <option value="1983">1983</option>
                        <option value="1984">1984</option>
                        <option value="1985">1985</option>
                        <option value="1986">1986</option>
                        <option value="1987">1987</option>
                        <option value="1988">1988</option>
                        <option value="1989">1989</option>
                        <option value="1990">1990</option>
                        <option value="1991">1991</option>
                        <option value="1992">1992</option>
                        <option value="1993">1993</option>
                        <option value="1994">1994</option>
                        <option value="1995">1995</option>
                        <option value="1996">1996</option>
                        <option value="1997">1997</option>
                        <option value="1998">1998</option>
                        <option value="1999">1999</option>
                    </select>年
                    <select name="account_employment_month">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>月
                        <select name="account_employment_day">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>日
                </td>
                </tr>
                <tr>
                <th>選任年月日</th>
                <td colspan="2">
                    <select name="account_appointment_year">
                        <option value="">選択</option>
                        <option value="1985">1985</option>
                        <option value="1986">1986</option>
                        <option value="1987">1987</option>
                        <option value="1988">1988</option>
                        <option value="1989">1989</option>
                        <option value="1990">1990</option>
                        <option value="1991">1991</option>
                        <option value="1992">1992</option>
                        <option value="1993">1993</option>
                        <option value="1994">1994</option>
                        <option value="1995">1995</option>
                        <option value="1996">1996</option>
                        <option value="1997">1997</option>
                        <option value="1998">1998</option>
                        <option value="1999">1999</option>
                        <option value="2000">2000</option>
                        <option value="2001">2001</option>
                        <option value="2002">2002</option>
                        <option value="2003">2003</option>
                        <option value="2004">2004</option>
                        <option value="2005">2005</option>
                        <option value="2006">2006</option>
                        <option value="2007">2007</option>
                        <option value="2008">2008</option>
                        <option value="2009">2009</option>
                        <option value="2010">2010</option>
                        <option value="2011">2011</option>
                        <option value="2012">2012</option>
                        <option value="2013">2013</option>
                        <option value="2014">2014</option>
                        <option value="2015">2015</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>年
                    <select name="account_appointment_month">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>月
                    <select name="account_appointment_day">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>日 
                </td>
                </tr>
                <tr>
                <th>退職年月日</th>
                <td colspan="2">
                    <select name="account_retirement_year">
                        <option value="">選択</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>年
                    <select name="account_retirement_month">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>月
                    <select name="account_retirement_day">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>日
                </td>
                </tr> 
                <tr>
                <th>?????</th>
                <td><select name="account_enrollment">
                        <option value="">選択</option>
                        <option value="年末調整対象">年末調整対象</option> 
                        <option value="年末調整対象外">年末調整対象外</option> 
                        <option value="死亡退職">死亡退職</option> 
                    </select>
                </td>                           
            </table>
            <table table class="fourth-table">
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
            </table>
            <div table class="flex">
                <input type="submit" value="登録" name="button">
                <!-- <a href="account_menu.html">閉じる</a> -->
            </div>
    </form>
    </body>
</html> 