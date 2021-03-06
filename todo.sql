-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 25 2020 г., 16:27
-- Версия сервера: 8.0.19
-- Версия PHP: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `todo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `task`
--

CREATE TABLE `task`
(
    `id`         int UNSIGNED NOT NULL,
    `user_name`  varchar(50)           DEFAULT NULL,
    `user_email` varchar(50)  NOT NULL,
    `text`       text         NOT NULL,
    `status`     tinyint(1)   NOT NULL DEFAULT '0',
    `edit`       tinyint(1)   NOT NULL DEFAULT '0',
    `updated_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user`
(
    `id`       int         NOT NULL,
    `name`     varchar(50) NOT NULL,
    `password` varchar(64) NOT NULL,
    `salt`     varchar(64) NOT NULL,
    `sid`      varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `password`, `salt`, `sid`)
VALUES (1, 'admin', '956b41cff1e90c38453a46082b6af8862dda9dea34ffcc597d94e50cf4ab7703',
        'db278383872694c7e2f4e7124e67a2f8744138891daf94bddc66c2d04c22bdf9', '9u7gscv53tanak76g418ncr5evu9g3de');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_name` (`user_name`,`user_email`),
  ADD KEY `idx_status` (`status`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
    ADD KEY `name` (`name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
    MODIFY `id` int NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
