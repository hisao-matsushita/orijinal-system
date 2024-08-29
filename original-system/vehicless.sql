-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-08-29 15:57:12
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `php_vehicles_app`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `vehicless`
--

CREATE TABLE `vehicless` (
  `car_id` int(11) NOT NULL,
  `car_number_name` int(3) NOT NULL COMMENT '車番',
  `car_model` varchar(10) NOT NULL COMMENT '車種',
  `car_name` varchar(30) NOT NULL COMMENT '車名',
  `car_transpottaition` varchar(5) NOT NULL COMMENT '運輸支局',
  `car_classification_no` int(3) NOT NULL COMMENT '分類番号',
  `car_purpose` varchar(1) NOT NULL COMMENT '用途別',
  `car_number01` varchar(2) NOT NULL COMMENT '番号1',
  `car_number02` varchar(2) NOT NULL COMMENT '番号2',
  `car_chassis_number` varchar(20) NOT NULL COMMENT '車台番号',
  `first_registration_year` int(5) NOT NULL COMMENT '初年度登録（年）',
  `first_registration_month` int(2) NOT NULL COMMENT '初年度登録（月）',
  `vehicle_inspection_year` int(5) NOT NULL COMMENT '車検有効期限（年）',
  `vehicle_inspection_month` int(2) NOT NULL COMMENT '車検有効期限（月）',
  `vehicle_inspection_day` int(2) NOT NULL COMMENT '車検有効期限（日）',
  `compulsory_automobile_year` int(5) NOT NULL COMMENT '自賠責有効期限（年）',
  `compulsory_automobile_month` int(2) NOT NULL COMMENT '自賠責有効期限（月）',
  `compulsory_automobile_day` int(2) NOT NULL COMMENT '自賠責有効期限（日）',
  `owner_name` varchar(20) NOT NULL COMMENT '所有者（名）',
  `owner_address` varchar(150) NOT NULL COMMENT '所有者住所',
  `user_name` varchar(20) NOT NULL COMMENT '使用者（名）',
  `user_address` varchar(150) NOT NULL COMMENT '使用者（住所）',
  `headquarters_address` int(1) NOT NULL COMMENT '本拠の位置',
  `vehicle_registrationday` datetime NOT NULL COMMENT '登録日',
  `vehicle_updateday` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `vehicless`
--

INSERT INTO `vehicless` (`car_id`, `car_number_name`, `car_model`, `car_name`, `car_transpottaition`, `car_classification_no`, `car_purpose`, `car_number01`, `car_number02`, `car_chassis_number`, `first_registration_year`, `first_registration_month`, `vehicle_inspection_year`, `vehicle_inspection_month`, `vehicle_inspection_day`, `compulsory_automobile_year`, `compulsory_automobile_month`, `compulsory_automobile_day`, `owner_name`, `owner_address`, `user_name`, `user_address`, `headquarters_address`, `vehicle_registrationday`, `vehicle_updateday`) VALUES
(79, 113, 'コンフォート', 'トヨタ', '静岡', 500, 'あ', '29', '69', '', 1999, 0, 2021, 0, 0, 2023, 0, 0, '', '', '', '', 0, '0000-00-00 00:00:00', '2024-08-28 05:15:33'),
(80, 114, 'シエンタ', 'トヨタ', '静岡', 500, 'あ', '36', '15', 'TSS-12156d', 1999, 2, 2021, 2, 3, 2023, 3, 16, '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', 3, '0000-00-00 00:00:00', '2024-08-28 05:26:05'),
(106, 0, '', '', '', 0, 'あ', '･･', '･3', '', 1999, 0, 2021, 0, 0, 2023, 0, 0, '', '', '', '', 0, '0000-00-00 00:00:00', '2024-08-28 10:41:25');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `vehicless`
--
ALTER TABLE `vehicless`
  ADD PRIMARY KEY (`car_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `vehicless`
--
ALTER TABLE `vehicless`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
